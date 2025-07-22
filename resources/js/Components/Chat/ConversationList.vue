<script setup>
import { defineProps, defineEmits, inject } from 'vue';

const emit = defineEmits(['select']);

const onlineUsers = inject('onlineUsers', null);

const props = defineProps({
  conversations: {
    type: Array,
    required: true,
    default: () => []
  },
  currentUser: {
    type: Object,
    required: true
  },
  selectedConversation: {
    type: Object,
    default: null
  }
});

const selectConversation = (conversation) => {
  emit('select', conversation);
};

const isActive = (conversation) => {
  return props.selectedConversation?.id === conversation.id;
};

const getTitle = (conversation) => {
  if (conversation.is_group || conversation.type === 'group') {
    return conversation.title || 'Group Chat';
  }

  const other = getOtherParticipant(conversation);
  return other?.name || 'Unknown User';
};

const getAvatar = (conversation) => {
  // Default fallback avatar
  const defaultAvatar = 'https://ui-avatars.com/api/?name=U&background=random';
  
  try {
    // For group chats
    if (conversation.is_group || conversation.type === 'group') {
      // Ensure avatar is a string and doesn't contain HTML
      const groupAvatar = conversation.avatar;
      if (groupAvatar && typeof groupAvatar === 'string' && 
          !groupAvatar.trim().startsWith('<') && 
          groupAvatar.match(/^https?:\/\//i)) {
        return groupAvatar;
      }
      
      // Generate a default group avatar
      const groupName = conversation.title || 'GC';
      const cleanName = typeof groupName === 'string' ? groupName.replace(/[^\w\s]/gi, '') : 'GC';
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(cleanName)}&background=random`;
    }
    
    // For direct messages
    let user = null;
    
    // Check if we have the other participant directly
    if (conversation.other_participant) {
      user = conversation.other_participant;
    } 
    // Fallback to checking participants array
    else if (conversation.participants) {
      user = conversation.participants.find(p => p.id !== props.currentUser.id);
    }

    // Fallback to relationships or helper
    if (!user && conversation.relationships?.other_participant) {
      user = conversation.relationships.other_participant;
    }
    if (!user) {
      user = getOtherParticipant(conversation);
    }
    
    if (user) {
      // Validate the avatar URL
      if (user.avatar && 
          typeof user.avatar === 'string' && 
          !user.avatar.trim().startsWith('<') && 
          user.avatar.match(/^https?:\/\//i)) {
        return user.avatar;
      }
      
      // Generate a default avatar with user initials
      const userName = user.name || 'U';
      const cleanName = typeof userName === 'string' ? userName.replace(/[^\w\s]/gi, '') : 'U';
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(cleanName)}&background=random`;
    }
  } catch (error) {
    console.error('Error generating avatar URL:', error);
  }
  
  return defaultAvatar;
};

const getOtherParticipant = (conversation) => {
  if (conversation.other_participant) return conversation.other_participant;
  if (conversation.relationships?.other_participant) return conversation.relationships.other_participant;
  if (Array.isArray(conversation.participants)) {
    return conversation.participants.find(p => p.id !== props.currentUser.id);
  }
  if (Array.isArray(conversation.relationships?.participants)) {
    return conversation.relationships.participants.find(p => p.id !== props.currentUser.id);
  }
  return null;
};

const isOnline = (conversation) => {
  if (!onlineUsers) return false;
  if (conversation.is_group || conversation.type === 'group') return false;
  const other = getOtherParticipant(conversation);
  return other && onlineUsers.value instanceof Set ? onlineUsers.value.has(other.id) : false;
};

const getLatestMessage = (conversation) => {
  return conversation.last_message || conversation.latest_message || conversation.relationships?.latest_message || null;
};

const getLastMessagePreview = (conversation) => {
  const message = getLatestMessage(conversation);
  if (!message) return 'No messages yet';
  
  const prefix = message.sender_id === props.currentUser.id 
    ? 'You: ' 
    : '';
    
  if (message.attachments?.length > 0) {
    return prefix + '📎 Attachment';
  }
  
  return prefix + (message.content || message.body || 'Sent a message');
};

const getUnreadCount = (conversation) => {
  return conversation.unread_messages_count || 0;
};

const formatTime = (timestamp) => {
  if (!timestamp) return '';
  
  const date = new Date(timestamp);
  const now = new Date();
  const diffInDays = Math.floor((now - date) / (1000 * 60 * 60 * 24));
  
  if (diffInDays === 0) {
    // Today - show time
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  } else if (diffInDays === 1) {
    // Yesterday
    return 'Yesterday';
  } else if (diffInDays < 7) {
    // This week - show day name
    return date.toLocaleDateString([], { weekday: 'short' });
  } else {
    // Older - show date
    return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
  }
};
</script>

<template>
  <div class="tg-sidebar__list">
    <div 
      v-for="conversation in conversations" 
      :key="conversation.id"
      @click="selectConversation(conversation)"
      class="tg-dialog"
     :class="{ 'tg-dialog--active': isActive(conversation) }"
      data-testid="conversation-item"
    >
      <!-- Avatar -->
      <div class="relative flex-shrink-0">
        <img 
          :src="getAvatar(conversation)" 
          :alt="getTitle(conversation)"
          class="tg-dialog__avatar"
        >
        <span 
          v-if="isOnline(conversation)"
          class="absolute bottom-0 right-0 block h-3 w-3 rounded-full bg-green-500 ring-2 ring-white"
        ></span>
      </div>

      <!-- Conversation Info -->
      <div class="tg-dialog__content">
        <div class="flex items-center justify-between">
          <h3 class="tg-dialog__title" :class="{ 'font-bold': getUnreadCount(conversation) > 0 }">
            {{ getTitle(conversation) }}
          </h3>
          <span class="tg-dialog__time">
            {{ formatTime(conversation.last_message_at) }}
          </span>
        </div>
        <div class="flex items-center justify-between">
          <p class="tg-dialog__snippet" :class="{ 'font-semibold': getUnreadCount(conversation) > 0 }">
            {{ getLastMessagePreview(conversation) }}
          </p>
          <span 
            v-if="getUnreadCount(conversation) > 0"
            class="tg-dialog__badge"
          >
            {{ getUnreadCount(conversation) }}
          </span>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="conversations.length === 0" class="p-6 text-center">
      <div class="text-gray-400 mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
      </div>
      <p class="text-sm text-gray-500">No conversations yet</p>
      <button 
        @click="$emit('new-conversation')"
        class="mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium"
      >
        Start a new conversation
      </button>
    </div>
  </div>
</template>


  props: {
    conversations: {
      type: Array,
      default: () => [],
    },
    currentUser: {
      type: Object,
      required: true,
    },
    selectedConversationId: {
      type: [Number, String],
      default: null,
    },
  },

  methods: {
    /**
     * Try to find the other participant user record, regardless of
     * whether the backend sent it at root or under relationships.
     */
    getOtherParticipant(conversation) {
      if (conversation.other_participant) return conversation.other_participant;
      if (conversation.relationships?.other_participant) return conversation.relationships.other_participant;
      if (Array.isArray(conversation.participants)) {
        return conversation.participants.find(p => p.id !== this.currentUser.id);
      }
      if (Array.isArray(conversation.relationships?.participants)) {
        return conversation.relationships.participants.find(p => p.id !== this.currentUser.id);
      }
      return null;
    },

    /**
     * Latest message helper that tolerates different key names
     */
    getLatestMessage(conversation) {
      return conversation.last_message || conversation.latest_message || conversation.relationships?.latest_message || null;
    },
    selectConversation(conversation) {
      this.$emit('select', conversation);
    },

    isActive(conversation) {
      return this.selectedConversationId === conversation.id;
    },

    getTitle(conversation) {
      if (conversation.title) return conversation.title;
            // For direct messages, fall back to participants array if other_participant missing
       if (conversation.other_participant) {
         return conversation.other_participant.name;
       }

       if (Array.isArray(conversation.participants)) {
         const other = conversation.participants.find(p => p.id !== this.currentUser.id);
         if (other && other.name) return other.name;
       }
       
       return 'Unknown User';
    },

  
    getAvatar(conversation) {
      // Prefer explicit conversation avatar (e.g. group icon)
      if (conversation.avatar) return conversation.avatar;
      // Resolve other participant record
      const other = this.getOtherParticipant(conversation);
      if (other?.avatar) return other.avatar;
      // Fallback: generate via initials
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(this.getTitle(conversation))}&background=random`;
    },
      

    isOnline(conversation) {
      if (conversation.type === 'group') return false;
      const other = this.getOtherParticipant(conversation);
      return other?.is_online || false;
    },

    getLastMessagePreview(conversation) {
      
      
      const message = this.getLatestMessage(conversation);
       if (!message) return 'No messages yet';
      const isCurrentUser = message.user_id === this.currentUser.id;
      const prefix = isCurrentUser ? 'You: ' : '';
            if (message.attachments?.length > 0) {
         return `${prefix}📎 Attachment`;
       }
       
       return prefix + (message.content || message.body || 'Sent a message');
    },

    getUnreadCount(conversation) {
      return conversation.unread_count || 0;
    },

    formatTime(timestamp) {
      if (!timestamp) return '';
      
      const date = new Date(timestamp);
      const now = new Date();
      
      // If today, show time
      if (date.toDateString() === now.toDateString()) {
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
      }
      
      // If yesterday
      const yesterday = new Date(now);
      yesterday.setDate(yesterday.getDate() - 1);
      if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
      }
      
      // If within the last 7 days, show day name
      const lastWeek = new Date(now);
      lastWeek.setDate(lastWeek.getDate() - 7);
      if (date > lastWeek) {
        return date.toLocaleDateString([], { weekday: 'short' });
      }
      
      // Otherwise, show date
      return date.toLocaleDateString([], { month: 'short', day: 'numeric' });
    },
  },
};

