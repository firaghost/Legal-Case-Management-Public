<template>
  <div class="min-h-screen bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
      <h1 class="text-2xl font-bold mb-6">Pusher Integration Test</h1>
      
      <!-- Connection Status -->
      <div class="mb-8 p-4 rounded-md" :class="connectionStatus.class">
        <div class="flex items-center">
          <div class="w-3 h-3 rounded-full mr-2" :class="connectionStatus.indicator"></div>
          <span class="font-medium">{{ connectionStatus.text }}</span>
        </div>
        <div v-if="connectionError" class="mt-2 text-sm text-red-600">
          {{ connectionError }}
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Send Test Message -->
        <div class="space-y-4">
          <h2 class="text-lg font-semibold">Send Test Message</h2>
          <div class="space-y-2">
            <div>
              <label class="block text-sm font-medium text-gray-700">Channel</label>
              <input v-model="testChannel" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="test-channel">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Event</label>
              <input v-model="testEvent" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="test-event">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Message</label>
              <textarea v-model="testMessage" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Test message content"></textarea>
            </div>
            <button 
              @click="sendTestMessage" 
              :disabled="!isConnected || isSending"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ isSending ? 'Sending...' : 'Send Test Message' }}
            </button>
          </div>
        </div>

        <!-- Send to Conversation -->
        <div class="space-y-4">
          <h2 class="text-lg font-semibold">Test Conversation</h2>
          <div class="space-y-2">
            <div>
              <label class="block text-sm font-medium text-gray-700">Conversation ID</label>
              <input v-model.number="conversationId" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="1">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Message</label>
              <textarea v-model="conversationMessage" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Test conversation message"></textarea>
            </div>
            <button 
              @click="sendConversationMessage" 
              :disabled="!isConnected || isSending"
              class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {{ isSending ? 'Sending...' : 'Send to Conversation' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Event Log -->
      <div class="mt-8">
        <div class="flex justify-between items-center mb-2">
          <h2 class="text-lg font-semibold">Event Log</h2>
          <button 
            @click="eventLog = []" 
            class="text-sm text-gray-500 hover:text-gray-700"
          >
            Clear Log
          </button>
        </div>
        <div class="bg-gray-50 p-4 rounded-md h-64 overflow-y-auto space-y-2">
          <div v-for="(event, index) in eventLog" :key="index" class="text-sm font-mono p-2 bg-white rounded border border-gray-200">
            <div class="text-blue-600 font-medium">{{ event.type }}</div>
            <div class="text-xs text-gray-500 mb-1">{{ event.timestamp }}</div>
            <pre class="whitespace-pre-wrap text-xs">{{ formatEventData(event.data) }}</pre>
          </div>
          <div v-if="eventLog.length === 0" class="text-center text-gray-500 text-sm py-8">
            No events received yet. Send a test message to see events here.
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import Echo from 'laravel-echo';

const props = defineProps({
  auth: Object,
  initialData: Object,
});

// Connection state
const echo = ref(null);
const isConnected = ref(false);
const isConnecting = ref(false);
const connectionError = ref(null);

// Test message form
const testChannel = ref('test-channel');
const testEvent = ref('test-event');
const testMessage = ref('Hello from the test page!');
const conversationId = ref(1);
const conversationMessage = ref('Test message in conversation');
const isSending = ref(false);

// Event log
const eventLog = ref([]);

// Computed status
const connectionStatus = computed(() => {
  if (isConnecting.value) {
    return {
      text: 'Connecting to Pusher...',
      class: 'bg-yellow-50',
      indicator: 'bg-yellow-400'
    };
  }
  
  if (isConnected.value) {
    return {
      text: 'Connected to Pusher',
      class: 'bg-green-50',
      indicator: 'bg-green-500'
    };
  }
  
  return {
    text: 'Disconnected from Pusher',
    class: 'bg-red-50',
    indicator: 'bg-red-500'
  };
});

// Format event data for display
const formatEventData = (data) => {
  try {
    if (typeof data === 'string') {
      data = JSON.parse(data);
    }
    return JSON.stringify(data, null, 2);
  } catch (e) {
    return data;
  }
};

// Add event to log
const logEvent = (type, data) => {
  eventLog.value.unshift({
    type,
    data,
    timestamp: new Date().toLocaleTimeString()
  });
};

// Initialize Pusher connection
const initializePusher = () => {
  isConnecting.value = true;
  
  const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const ziggy = usePage().props.ziggy || {};
  const pusherCfg = props.initialData?.pusher || { 
    key: ziggy?.pusher?.key || import.meta.env.VITE_PUSHER_APP_KEY, 
    cluster: ziggy?.pusher?.cluster || import.meta.env.VITE_PUSHER_APP_CLUSTER 
  };

  // Configure axios
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
  window.axios.defaults.withCredentials = true;

  // Initialize Echo with Pusher
  echo.value = new Echo({
    broadcaster: 'pusher',
    key: pusherCfg.key,
    cluster: pusherCfg.cluster,
    forceTLS: true,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
      },
    },
  });

  // Connection event handlers
  const pusher = echo.value.connector.pusher;
  
  // Connection established
  pusher.connection.bind('connected', () => {
    console.log('Pusher connected successfully');
    isConnected.value = true;
    isConnecting.value = false;
    connectionError.value = null;
    logEvent('Connected', { status: 'Connected to Pusher' });
    
    // Subscribe to the test channel
    const channel = echo.value.channel(testChannel.value);
    
    // Listen for all events on the test channel
    channel.listen('.', (data) => {
      logEvent(`Event: ${data.event || 'unknown'}`, data.data || data);
    });
    
    // Listen for conversation events
    const conversationChannel = echo.value.private(`conversation.${conversationId.value}`);
    conversationChannel.listen('.', (data) => {
      logEvent(`Conversation Event: ${data.event || 'unknown'}`, data.data || data);
    });
  });

  // Connection error
  pusher.connection.bind('error', (err) => {
    console.error('Pusher connection error:', err);
    isConnected.value = false;
    isConnecting.value = false;
    connectionError.value = err.error?.data?.message || 'Connection error. Trying to reconnect...';
    logEvent('Connection Error', { error: err.message || 'Unknown error' });
    
    // Attempt to reconnect after a delay
    setTimeout(() => {
      if (!isConnected.value) {
        console.log('Attempting to reconnect to Pusher...');
        pusher.connect();
      }
    }, 5000);
  });

  // Disconnected
  pusher.connection.bind('disconnected', () => {
    console.log('Pusher disconnected');
    isConnected.value = false;
    isConnecting.value = false;
    logEvent('Disconnected', { status: 'Disconnected from Pusher' });
    
    // Attempt to reconnect
    setTimeout(() => {
      if (!isConnected.value) {
        console.log('Attempting to reconnect to Pusher...');
        pusher.connect();
      }
    }, 1000);
  });
};

// Send test message
const sendTestMessage = async () => {
  if (!testChannel.value || !testEvent.value || !testMessage.value) {
    alert('Please fill in all fields');
    return;
  }

  isSending.value = true;
  
  try {
    const response = await axios.post('/api/test/pusher/message', {
      channel: testChannel.value,
      event: testEvent.value,
      message: testMessage.value
    });
    
    logEvent('Message Sent', response.data);
  } catch (error) {
    console.error('Error sending test message:', error);
    logEvent('Error', { 
      message: error.message,
      response: error.response?.data 
    });
  } finally {
    isSending.value = false;
  }
};

// Send conversation message
const sendConversationMessage = async () => {
  if (!conversationId.value || !conversationMessage.value) {
    alert('Please fill in all fields');
    return;
  }

  isSending.value = true;
  
  try {
    const response = await axios.post('/api/test/pusher/conversation-message', {
      conversation_id: conversationId.value,
      message: conversationMessage.value
    });
    
    logEvent('Conversation Message Sent', response.data);
  } catch (error) {
    console.error('Error sending conversation message:', error);
    logEvent('Error', { 
      message: error.message,
      response: error.response?.data 
    });
  } finally {
    isSending.value = false;
  }
};

// Initialize on mount
onMounted(() => {
  initializePusher();
});

// Clean up on unmount
onBeforeUnmount(() => {
  if (echo.value) {
    echo.value.disconnect();
  }
});
</script>

<style scoped>
/* Add any custom styles here */
</style>
