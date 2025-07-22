<script setup>
import { ref, computed, defineEmits, defineProps, watch, onMounted, onUnmounted } from 'vue';
import ConversationList from './ConversationList.vue';
import ConversationHeader from './ConversationHeader.vue';
import MessageList from './MessageList.vue';
import MessageInput from './MessageInput.vue';
import VoiceRecorder from './VoiceRecorder.vue';
import Echo from 'laravel-echo';
import axios from 'axios';

const emit = defineEmits(['select-conversation', 'send-message', 'create-conversation']);

const props = defineProps({
  conversations: {
    type: Array,
    required: true
  },
  currentUser: {
    type: Object,
    required: true
  },
  currentConversation: {
    type: Object,
    default: null
  },
  isTyping: {
    type: Boolean,
    default: false
  },
  isSending: {
    type: Boolean,
    default: false
  },
  messages: {
    type: Array,
    default: () => []
  }
});

const searchQuery = ref('');

// Messages to display come from the current conversation prop or the fallback prop `messages`
const displayMessages = computed(() => props.messages || []);

// Filter conversations based on search query
const sortByUpdated = (a, b) => {
  const getTs = (c) => new Date(
    c.updated_at || c.last_message_at || c.last_message?.created_at || 0
  ).getTime();
  return getTs(b) - getTs(a);
};

const filteredConversations = computed(() => {
  // Always start from a sorted copy
  let list = props.conversations.slice().sort(sortByUpdated);

  if (!searchQuery.value) return list;

  const query = searchQuery.value.toLowerCase();
  return list.filter(conv => {
    const participantNames = (conv.participants || [])
      .filter(p => p.id !== props.currentUser.id)
      .map(p => (p.name || '').toLowerCase());
    return participantNames.some(name => name.includes(query));
  });
});
  
  


const selectConversation = (conversation) => {
  console.log('ChatContainer: Conversation selected', conversation);
  emit('select-conversation', conversation);
};

const handleSendMessage = (messageData) => {
  console.log('ChatContainer: Sending message', messageData);
  // Ensure replyToId is a number or null
  let replyToId = null;
  if (typeof messageData.replyToId !== 'undefined' && messageData.replyToId !== null) {
    replyToId = Number(messageData.replyToId) || null;
  }
  // Handle different message data formats
  if (typeof messageData === 'object' && messageData !== null) {
    const { content = '', attachments = [], type = 'text' } = messageData;
    const messageContent = (type === 'audio' && !content) ? '🎤 Voice message' : content;
    emit('send-message', messageContent, attachments, type, replyToId);
  } else if (typeof messageData === 'string') {
    emit('send-message', messageData, [], 'text');
  } else {
    console.error('Invalid message format:', messageData);
  }
};

const handleRecordingError = (error) => {
  console.error('Recording error:', error);
  // You could show a notification to the user here
  alert(error);
};

const handleRecordingComplete = (audioBlob) => {
  console.log('ChatContainer: Audio recording complete', audioBlob);
  
  // Create a unique filename for the audio file
  const fileName = `voice-message-${Date.now()}.wav`;
  
  // Create a file from the blob
  const audioFile = new File([audioBlob], fileName, {
    type: 'audio/wav',
    lastModified: Date.now()
  });
  
  // Include a default content string for audio messages
  const audioMessageContent = '🎤 Voice message';
  
  // Emit the audio file as an attachment with the default content
  // Pass the content as a string, not an object
  emit('send-message', audioMessageContent, [audioFile], 'audio');
};

// Watch for changes in current conversation
// Watch selected conversation change
watch(() => props.currentConversation, async (newVal) => {
  console.log('ChatContainer: Current conversation changed', newVal);
  if (newVal) {
    try {
      await axios.post(`/api/conversations/${newVal.id}/mark-as-seen`);
      const conv = props.conversations.find(c => c.id === newVal.id);
      if (conv) conv.unread_messages_count = 0;
    } catch (err) {
      console.error('mark-seen failed', err);
    }
  }
});

// Desktop notification for new incoming messages


const subscribeToConversations = () => {
  props.conversations.forEach(conv => {
    if (!conv.__echoBound) {
      window.Echo.private(`conversation.${conv.id}`)
        .listen('.message.sent', (e) => {
          handleIncomingMessage(e, conv.id);
        })
        .listen('.conversation.read', (e) => {
          if (e.user_id === props.currentUser.id) {
            const c = props.conversations.find(cc => cc.id === e.conversation_id);
            if (c) c.unread_messages_count = 0;
          }
        });
      conv.__echoBound = true;
    }
  });
};

function handleIncomingMessage(payload, convId) {
  // Ignore events for messages we already have (prevents duplicates)
  if (props.messages.some(m => m.id === payload.id)) return;
  // Update conversation list item
  const conv = props.conversations.find(c => c.id === convId);
  if (conv) {
    conv.last_message = payload;
    conv.updated_at = payload.created_at;
    if (payload.user.id !== props.currentUser.id) {
      conv.unread_messages_count = (conv.unread_messages_count || 0) + 1;
    }
  }
  // If this is the active conversation append to messages
  if (props.currentConversation && props.currentConversation.id === convId) {
    // Only push if not duplicate (checked above) and not authored by current user
    if (payload.user.id !== props.currentUser.id) {
      props.messages.push(payload);
    }
  }
  // Trigger desktop notification if necessary
  if (payload.user.id !== props.currentUser.id && document.visibilityState !== 'visible') {
    if ('Notification' in window && Notification.permission === 'granted') {
      new Notification(`New message from ${payload.user.name}`, { body: payload.body || 'You have a new message' });
    }
  }
}

// keep subscriptions up-to-date when conversation list changes
watch(
  () => props.conversations.map(c => c.id).join(','),
  () => subscribeToConversations(),
  { immediate: true }
);

onMounted(() => {
  if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
  }
});

watch(() => props.messages.length, (newLen, oldLen) => {
  if (newLen <= oldLen) return;
  const msg = props.messages[props.messages.length - 1];
  if (!msg || msg.user_id === props.currentUser.id) return;
  if (document.visibilityState === 'visible') return;
  if ('Notification' in window && Notification.permission === 'granted') {
    const title = msg.user?.name ? `New message from ${msg.user.name}` : 'New message';
    const body = msg.body || msg.content || 'You have a new message';
    new Notification(title, { body });
  }
});

</script>

<template>
  <div class="chat-container">
    <!-- Sidebar -->
    <div class="chat-sidebar">
      <!-- Header -->
      <div class="sidebar-header">
        <div class="header-content">
          <div class="header-title">
            <div class="title-icon">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
            </div>
            <h1 class="title-text">Messages</h1>
          </div>
          <button 
            @click="$emit('toggle-sidebar')" 
            class="toggle-btn"
            aria-label="Toggle sidebar"
          >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 100-2H4a1 1 0 100 2h5z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
        
        <!-- Search -->
        <div class="search-container">
          <div class="search-icon">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Search conversations..."
            class="search-input"
          >
        </div>
      </div>

      <!-- Start New Chat Button -->
      <div class="new-chat-section">
        <button
          @click="$emit('create-conversation')"
          class="new-chat-btn"
        >
          <div class="btn-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
          </div>
          <span class="btn-text">New Conversation</span>
        </button>
      </div>

      <!-- Conversation List -->
      <div class="conversations-container">
        <ConversationList 
          :conversations="filteredConversations"
          :current-user="currentUser"
          :current-conversation="currentConversation"
          @select="selectConversation"
        />
      </div>
    </div>

    <!-- Chat Area -->
    <div class="chat-area">
      <template v-if="currentConversation">
        <!-- Chat Header -->
        <div class="chat-header">
          <ConversationHeader 
            :conversation="currentConversation"
            :current-user="currentUser"
          />
        </div>

        <!-- Messages -->
        <div class="messages-container">
          <MessageList 
            :messages="displayMessages"
            :current-user="currentUser"
            :is-typing="isTyping"
            :loading="isLoadingMessages"
            @reply="handleReply"
            @delete="handleDelete"
            @edit="handleEdit"
            @forward="handleForward"
          />
        </div>

        <!-- Message Input -->
        <div class="message-input-container">
          <div class="input-wrapper">
            <MessageInput 
              @send-message="handleSendMessage"
              :is-typing="isTyping"
              :is-sending="isSending"
              :reply-to="replyTo"
              @cancel-reply="handleCancelReply"
              @typing="handleTyping"
            >
              <template #voice-recorder>
                <VoiceRecorder 
                  @recording-complete="handleRecordingComplete"
                  @error="handleRecordingError"
                  class="ml-2"
                />
              </template>
            </MessageInput>
          </div>
        </div>
      </template>

      <!-- Empty State -->
      <div v-else class="empty-state">
        <div class="empty-content">
          <div class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
          </div>
          <div class="empty-text">
            <h3 class="empty-title">Welcome to Legal Case Chat</h3>
            <p class="empty-subtitle">Select a conversation to start messaging with your colleagues</p>
          </div>
          <div class="empty-action">
            <button @click="$emit('create-conversation')" class="start-chat-btn">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Start New Conversation
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ConversationList from './ConversationList.vue';
import ConversationHeader from './ConversationHeader.vue';
import MessageList from './MessageList.vue';
import MessageInput from './MessageInput.vue';

export default {
  components: {
    ConversationList,
    ConversationHeader,
    MessageList,
    MessageInput,
  },

  props: {
    conversations: {
      type: Array,
      default: () => [],
    },
    currentUser: {
      type: Object,
      required: true,
    },
    messages: {
      type: Array,
      default: () => []
    },
  },

  data() {
    return {
      selectedConversation: null,
      optimisticMessages: [],
      isLoadingMessages: false,
      isRecordingAudio: false,
      replyTo: null,
    };
  },

  methods: {
    handleReply(message) {
      this.replyTo = message;
    },
    handleCancelReply() {
      this.replyTo = null;
    },
    async handleDelete(message) {
      try {
        // TODO: implement server-side delete. For now just remove locally.
        this.messages = this.messages.filter(m => m.id !== message.id);
      } catch (error) {
        console.error('Local delete');
      }
    },
    handleEdit(message) {
      // Placeholder for future edit functionality
      console.log('Edit message', message);
    },
    handleForward(message) {
      const prefix = 'Forwarded:\n';
      const body = message.content || message.body || '';
      this.handleSendMessage({ content: prefix + body, attachments: [], type: 'text' });
    },
    selectConversation(conversation) {
      this.selectedConversation = conversation;
      this.loadMessages(conversation.id);
    },

    async loadMessages(conversationId) {
      this.isLoadingMessages = true;
      try {
        const response = await axios.get(`/api/conversations/${conversationId}/messages`, {
          params: {
            per_page: 100
          },
          headers: {
            'Accept': 'application/json'
          }
        });
        this.messages = response.data && response.data.data ? response.data.data : [];

        // Update last_message preview for the selected conversation
        if (this.messages.length > 0) {
          const lastMessage = this.messages[this.messages.length - 1];
          this.selectedConversation.last_message = lastMessage;
        
          // Update in conversations list as well so ConversationList shows preview
          const convIndex = this.conversations.findIndex(c => c.id === this.selectedConversation.id);
          if (convIndex !== -1) {
            this.$set(this.conversations, convIndex, {
              ...this.conversations[convIndex],
              last_message: lastMessage,
            });
          }
        }
      } catch (error) {
        console.error('Failed to load messages:', error);
      } finally {
        this.isLoadingMessages = false;
      }
    },

    async sendMessage(content, attachments = [], type = 'text', replyToId = null) {
      if (!this.selectedConversation) return;
      
      let replyToObj = null;
      if (replyToId) {
        const ref = this.messages.find(m => m.id === replyToId);
        if (ref) {
          replyToObj = {
            id: ref.id,
            body: ref.body || ref.content,
            user: ref.user ? { id: ref.user.id, name: ref.user.name } : null,
          };
        }
      }

      const message = {
        id: 'temp-' + Date.now(), // Temporary ID with prefix to avoid conflicts
        content,
        attachments: [...attachments], // Clone the attachments array
        user_id: this.currentUser.id,
        conversation_id: this.selectedConversation.id,
        created_at: new Date().toISOString(),
        status: 'sending',
        user: { ...this.currentUser },
        type: attachments.length ? 'audio' : 'text',
        reply_to: replyToObj,
      };

      // Optimistically add the message
      this.messages = [...this.messages, message];

      try {
        const formData = new FormData();
        formData.append('conversation_id', this.selectedConversation.id);
        // Backend expects 'body' field, not 'content'
        formData.append('body', content || '');
        // Prefer explicit type param; if not provided, detect from first attachment
        let detectedType = type;
        if (attachments.length && type === 'text') {
          const mime = attachments[0].type || '';
          if (mime.startsWith('image/')) detectedType = 'image';
          else if (mime.startsWith('audio/')) detectedType = 'audio';
          else if (mime.startsWith('video/')) detectedType = 'video';
          else detectedType = 'document';
        }
        formData.append('type', detectedType);
         if (replyToId) {
           if (replyToId) {
           formData.append('reply_to', replyToId);
         }
         }
        
        // Add attachments if any
        if (attachments && attachments.length > 0) {
          attachments.forEach((file, index) => {
            formData.append(`attachments[${index}]`, file);
          });
        }

        // Make the API call
        const response = await axios.post(`/api/conversations/${this.selectedConversation.id}/messages`, formData, {
          headers: {
            'Content-Type': 'multipart/form-data',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        });
        
        // Update the message with server response
        const index = this.messages.findIndex(m => m.id === message.id);
        if (index !== -1) {
          this.messages[index] = {
            ...this.messages[index],
            ...response.data.data,
            status: 'sent'
          };
          this.$set(this.messages, index, this.messages[index]);
        }
      } catch (error) {
        console.error('Failed to send message:', error);
        // Update message status to error
        const index = this.messages.findIndex(m => m.id === message.id);
        if (index !== -1) {
          this.messages[index].status = 'error';
          this.$set(this.messages, index, { ...this.messages[index] });
          
          // Show error to user
          if (error.response) {
            const errorMessage = error.response.data?.message || 'Failed to send message';
            alert(errorMessage);
          } else {
            alert('Failed to send message. Please check your connection and try again.');
          }
        }
      }
    },

    startAudioRecording() {
      this.isRecordingAudio = true;
      // TODO: Implement audio recording
      console.log('Audio recording started');
    },

    stopAudioRecording() {
      this.isRecordingAudio = false;
      // TODO: Implement audio recording stop and send
      console.log('Audio recording stopped');
    },
  },
};
</script>

<style scoped>
/* Chat Container */
.chat-container {
  @apply h-full flex;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.1);
  margin: 16px;
  position: relative;
}

/* Sidebar */
.chat-sidebar {
  @apply flex flex-col;
  width: 320px;
  background: linear-gradient(135deg, rgba(248, 250, 252, 0.95) 0%, rgba(241, 245, 249, 0.95) 100%);
  backdrop-filter: blur(20px);
  border-right: 1px solid rgba(226, 232, 240, 0.5);
  position: relative;
}

.chat-sidebar::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(135deg, rgba(60, 164, 76, 0.05) 0%, rgba(30, 58, 46, 0.05) 100%);
  pointer-events: none;
}

/* Sidebar Header */
.sidebar-header {
  @apply p-6 border-b;
  border-color: rgba(226, 232, 240, 0.5);
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  position: relative;
  z-index: 10;
}

.header-content {
  @apply flex items-center justify-between mb-4;
}

.header-title {
  @apply flex items-center space-x-3;
}

.title-icon {
  @apply w-10 h-10 rounded-xl flex items-center justify-center;
  background: linear-gradient(135deg, #3ca44c 0%, #1e3a2e 100%);
  color: white;
  box-shadow: 0 4px 6px -1px rgba(60, 164, 76, 0.3);
}

.title-text {
  @apply text-xl font-bold;
  background: linear-gradient(135deg, #1e3a2e 0%, #3ca44c 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.toggle-btn {
  @apply p-2 rounded-xl transition-all duration-200;
  background: rgba(60, 164, 76, 0.1);
  color: #3ca44c;
}

.toggle-btn:hover {
  background: rgba(60, 164, 76, 0.2);
  transform: scale(1.05);
}

/* Search Container */
.search-container {
  @apply relative;
}

.search-icon {
  @apply absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none;
  color: #64748b;
}

.search-input {
  @apply w-full pl-12 pr-4 py-3 rounded-xl border-0 text-sm;
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(10px);
  box-shadow: 
    0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 0 0 1px rgba(226, 232, 240, 0.5);
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  box-shadow: 
    0 4px 6px -1px rgba(60, 164, 76, 0.2),
    0 0 0 2px rgba(60, 164, 76, 0.3);
  background: rgba(255, 255, 255, 0.95);
}

.search-input::placeholder {
  color: #94a3b8;
}

/* New Chat Section */
.new-chat-section {
  @apply p-4;
  background: rgba(255, 255, 255, 0.5);
  border-bottom: 1px solid rgba(226, 232, 240, 0.5);
}

.new-chat-btn {
  @apply w-full flex items-center justify-center gap-3 px-4 py-3 rounded-xl font-medium transition-all duration-300;
  background: linear-gradient(135deg, #3ca44c 0%, #1e3a2e 100%);
  color: white;
  box-shadow: 
    0 4px 6px -1px rgba(60, 164, 76, 0.3),
    0 2px 4px -1px rgba(60, 164, 76, 0.2);
}

.new-chat-btn:hover {
  transform: translateY(-1px);
  box-shadow: 
    0 6px 8px -1px rgba(60, 164, 76, 0.4),
    0 4px 6px -1px rgba(60, 164, 76, 0.3);
}

.btn-icon {
  @apply w-5 h-5 flex items-center justify-center;
}

.btn-text {
  @apply text-sm font-semibold;
}

/* Conversations Container */
.conversations-container {
  @apply flex-1 overflow-y-auto;
  scrollbar-width: thin;
  scrollbar-color: rgba(60, 164, 76, 0.3) transparent;
}

.conversations-container::-webkit-scrollbar {
  width: 4px;
}

.conversations-container::-webkit-scrollbar-track {
  background: transparent;
}

.conversations-container::-webkit-scrollbar-thumb {
  background: rgba(60, 164, 76, 0.3);
  border-radius: 2px;
}

.conversations-container::-webkit-scrollbar-thumb:hover {
  background: rgba(60, 164, 76, 0.5);
}

/* Chat Area */
.chat-area {
  @apply flex-1 flex flex-col;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
  position: relative;
}

.chat-area::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    radial-gradient(circle at 20% 80%, rgba(60, 164, 76, 0.03) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(30, 58, 46, 0.03) 0%, transparent 50%);
  pointer-events: none;
}

/* Chat Header */
.chat-header {
  @apply border-b;
  border-color: rgba(226, 232, 240, 0.5);
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(20px);
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
  position: relative;
  z-index: 10;
}

/* Messages Container */
.messages-container {
  @apply flex-1 overflow-y-auto p-6;
  background: transparent;
  position: relative;
  z-index: 5;
}

/* Message Input Container */
.message-input-container {
  @apply border-t p-4;
  border-color: rgba(226, 232, 240, 0.5);
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(20px);
  box-shadow: 0 -1px 3px 0 rgba(0, 0, 0, 0.1);
  position: relative;
  z-index: 10;
}

.input-wrapper {
  @apply max-w-4xl mx-auto;
}

/* Empty State */
.empty-state {
  @apply flex-1 flex flex-col items-center justify-center p-8;
  position: relative;
  z-index: 5;
}

.empty-content {
  @apply text-center max-w-md mx-auto;
}

.empty-icon {
  @apply w-24 h-24 mx-auto mb-6 p-6 rounded-full;
  background: linear-gradient(135deg, rgba(60, 164, 76, 0.1) 0%, rgba(30, 58, 46, 0.1) 100%);
  color: #3ca44c;
}

.empty-text {
  @apply mb-8;
}

.empty-title {
  @apply text-2xl font-bold mb-3;
  background: linear-gradient(135deg, #1e3a2e 0%, #3ca44c 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.empty-subtitle {
  @apply text-gray-600 leading-relaxed;
}

.empty-action {
  @apply flex justify-center;
}

.start-chat-btn {
  @apply flex items-center px-6 py-3 rounded-xl font-medium transition-all duration-300;
  background: linear-gradient(135deg, #3ca44c 0%, #1e3a2e 100%);
  color: white;
  box-shadow: 
    0 4px 6px -1px rgba(60, 164, 76, 0.3),
    0 2px 4px -1px rgba(60, 164, 76, 0.2);
}

.start-chat-btn:hover {
  transform: translateY(-1px);
  box-shadow: 
    0 6px 8px -1px rgba(60, 164, 76, 0.4),
    0 4px 6px -1px rgba(60, 164, 76, 0.3);
}

/* Animations */
@keyframes slideInLeft {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

.chat-container {
  animation: fadeIn 0.5s ease-out;
}

.chat-sidebar {
  animation: slideInLeft 0.5s ease-out;
}

/* Dark theme support */
@media (prefers-color-scheme: dark) {
  .chat-container {
    background: rgba(30, 41, 59, 0.95);
  }
  
  .chat-sidebar {
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
    border-right-color: rgba(51, 65, 85, 0.5);
  }
  
  .sidebar-header {
    background: rgba(30, 41, 59, 0.8);
    border-color: rgba(51, 65, 85, 0.5);
  }
  
  .title-text {
    background: linear-gradient(135deg, #60a5fa 0%, #3ca44c 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  
  .search-input {
    background: rgba(30, 41, 59, 0.8);
    color: #f1f5f9;
  }
  
  .search-input::placeholder {
    color: #64748b;
  }
  
  .new-chat-section {
    background: rgba(30, 41, 59, 0.5);
    border-color: rgba(51, 65, 85, 0.5);
  }
  
  .chat-area {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 100%);
  }
  
  .chat-header {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(51, 65, 85, 0.5);
  }
  
  .message-input-container {
    background: rgba(30, 41, 59, 0.9);
    border-color: rgba(51, 65, 85, 0.5);
  }
  
  .empty-subtitle {
    color: #94a3b8;
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .chat-container {
    @apply m-2 rounded-xl;
  }
  
  .chat-sidebar {
    width: 280px;
  }
  
  .sidebar-header {
    @apply p-4;
  }
  
  .title-text {
    @apply text-lg;
  }
  
  .empty-icon {
    @apply w-20 h-20 p-5;
  }
  
  .empty-title {
    @apply text-xl;
  }
}

@media (max-width: 640px) {
  .chat-sidebar {
    width: 100%;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    z-index: 20;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .chat-sidebar.open {
    transform: translateX(0);
  }
}
</style>
