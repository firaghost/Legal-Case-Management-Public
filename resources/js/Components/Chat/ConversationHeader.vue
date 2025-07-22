<template>
  <div class="tg-chat-header">
    <!-- Left side: User info -->
    <div class="flex items-center">
      <!-- Back button for mobile -->
      <button 
        @click="$emit('back')"
        class="md:hidden mr-2 text-gray-500 hover:text-gray-700 focus:outline-none"
        aria-label="Back to conversations"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      
      <!-- Avatar -->
      <div class="relative">
        <img 
          :src="conversation.avatar || getDefaultAvatar(conversation)" 
          :alt="getConversationTitle(conversation)"
          class="tg-chat-header__avatar"
        >
        <!-- Online status indicator -->
        <span 
          v-if="isOnline(conversation)" 
          class="absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full bg-green-500 ring-2 ring-white"
        ></span>
      </div>
      
      <!-- User/Group info -->
      <div class="ml-3">
        <h2 class="tg-chat-header__title">
          {{ getConversationTitle(conversation) }}
        </h2>
        <p class="tg-chat-header__status">
          <template v-if="isTyping">
            <span class="text-blue-500">typing...</span>
          </template>
          <template v-else-if="isOnline(conversation)">
            <span class="text-green-500">Online</span>
          </template>
          <template v-else>
            {{ lastSeenText }}
          </template>
        </p>
      </div>
    </div>
    
    <!-- Right side: Action buttons -->
    <div class="tg-chat-header__actions flex items-center space-x-2">
      <!-- Call buttons -->
      <button 
        @click="$emit('audio-call')" 
        class="p-2 text-gray-500 hover:text-blue-500 rounded-full hover:bg-gray-100 focus:outline-none"
        :title="'Audio call ' + getConversationTitle(conversation)"
        aria-label="Start audio call"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
        </svg>
      </button>
      
      <button 
        @click="$emit('video-call')" 
        class="p-2 text-gray-500 hover:text-blue-500 rounded-full hover:bg-gray-100 focus:outline-none"
        :title="'Video call ' + getConversationTitle(conversation)"
        aria-label="Start video call"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
        </svg>
      </button>
      
      <!-- More options dropdown -->
      <div class="relative">
        <button 
          @click="showDropdown = !showDropdown"
          @blur="onBlurDropdown"
          class="p-2 text-gray-500 hover:text-gray-700 rounded-full hover:bg-gray-100 focus:outline-none"
          aria-label="More options"
          aria-haspopup="true"
          :aria-expanded="showDropdown"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
          </svg>
        </button>
        
        <!-- Dropdown menu -->
        <transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-75"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <div 
            v-show="showDropdown"
            class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="options-menu"
          >
            <div class="py-1" role="none">
              <button 
                @click="viewProfile"
                class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                role="menuitem"
              >
                View Profile
              </button>
              
              <button 
                @click="muteNotifications"
                class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                role="menuitem"
              >
                {{ isMuted ? 'Unmute Notifications' : 'Mute Notifications' }}
              </button>
              
              <button 
                @click="togglePinned"
                class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                role="menuitem"
              >
                {{ isPinned ? 'Unpin Chat' : 'Pin Chat' }}
              </button>
              
              <div class="border-t border-gray-100 my-1"></div>
              
              <button 
                @click="clearChat"
                class="text-gray-700 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                role="menuitem"
              >
                Clear Chat
              </button>
              
              <button 
                @click="deleteChat"
                class="text-red-600 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                role="menuitem"
              >
                Delete Chat
              </button>
              
              <button 
                v-if="isGroup"
                @click="leaveGroup"
                class="text-red-600 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                role="menuitem"
              >
                Leave Group
              </button>
              
              <button 
                v-else
                @click="blockUser"
                class="text-red-600 block w-full text-left px-4 py-2 text-sm hover:bg-gray-100"
                role="menuitem"
              >
                {{ isBlocked ? 'Unblock User' : 'Block User' }}
              </button>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ConversationHeader',
  
  props: {
    conversation: {
      type: Object,
      required: true,
    },
    currentUser: {
      type: Object,
      required: true,
    },
    isTyping: {
      type: Boolean,
      default: false,
    },
  },
  
  data() {
    return {
      showDropdown: false,
      isMuted: false,
      isPinned: false,
      isBlocked: false,
    };
  },
  
  computed: {
    isGroup() {
      return this.conversation.type === 'group';
    },
    
    lastSeenText() {
      if (!this.conversation.last_seen) return '';
      
      const lastSeen = new Date(this.conversation.last_seen);
      const now = new Date();
      const diffInMinutes = Math.floor((now - lastSeen) / (1000 * 60));
      
      if (diffInMinutes < 1) return 'Just now';
      if (diffInMinutes < 60) return `Last seen ${diffInMinutes} min ago`;
      
      const diffInHours = Math.floor(diffInMinutes / 60);
      if (diffInHours < 24) return `Last seen ${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
      
      const diffInDays = Math.floor(diffInHours / 24);
      if (diffInDays === 1) return 'Last seen yesterday';
      if (diffInDays < 7) return `Last seen ${diffInDays} days ago`;
      
      return `Last seen on ${lastSeen.toLocaleDateString()}`;
    },
  },
  
  methods: {
    getConversationTitle(conversation) {
      if (conversation.title) return conversation.title;
      
      // For direct messages, show the other participant's name
      if (conversation.other_participant) {
        return conversation.other_participant.name;
      }
      
      // For groups, show participant names
      if (conversation.participants && conversation.participants.length > 0) {
        return conversation.participants
          .map(p => p.user ? p.user.name : '')
          .filter(Boolean)
          .join(', ');
      }
      
      return 'Unnamed Chat';
    },
    
    getDefaultAvatar(conversation) {
      // Generate a default avatar with initials
      const title = this.getConversationTitle(conversation);
      const nameParts = title.split(' ');
      let initials = '';
      
      if (nameParts.length >= 2) {
        initials = nameParts[0].charAt(0) + nameParts[1].charAt(0);
      } else if (title.length > 0) {
        initials = title.substring(0, 2);
      } else {
        initials = '??';
      }
      
      // Use a colorful gradient based on the conversation ID or title
      const colors = [
        'bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500', 
        'bg-red-500', 'bg-yellow-500', 'bg-indigo-500', 'bg-teal-500'
      ];
      
      // Simple hash function to get a consistent color for the same title
      const hash = title.split('').reduce((acc, char) => {
        return char.charCodeAt(0) + ((acc << 5) - acc);
      }, 0);
      
      const colorIndex = Math.abs(hash) % colors.length;
      
      return `
        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-medium ${colors[colorIndex]}">
          ${initials.toUpperCase()}
        </div>
      `;
    },
    
    isOnline(conversation) {
      if (conversation.type === 'group') return false;
      
      const participant = conversation.other_participant || 
                         (conversation.participants && conversation.participants[0]);
      
      if (!participant) return false;
      
      const user = participant.user || participant;
      return user.is_online || false;
    },
    
    onBlurDropdown() {
      // Small delay to allow click events to fire before hiding the dropdown
      setTimeout(() => {
        this.showDropdown = false;
      }, 200);
    },
    
    viewProfile() {
      this.$emit('view-profile');
      this.showDropdown = false;
    },
    
    muteNotifications() {
      this.isMuted = !this.isMuted;
      this.$emit('mute', this.isMuted);
      this.showDropdown = false;
    },
    
    togglePinned() {
      this.isPinned = !this.isPinned;
      this.$emit('pin', this.isPinned);
      this.showDropdown = false;
    },
    
    clearChat() {
      if (confirm('Are you sure you want to clear this chat? This action cannot be undone.')) {
        this.$emit('clear');
      }
      this.showDropdown = false;
    },
    
    deleteChat() {
      if (confirm('Are you sure you want to delete this chat? This action cannot be undone.')) {
        this.$emit('delete');
      }
      this.showDropdown = false;
    },
    
    leaveGroup() {
      if (confirm('Are you sure you want to leave this group?')) {
        this.$emit('leave');
      }
      this.showDropdown = false;
    },
    
    blockUser() {
      this.isBlocked = !this.isBlocked;
      this.$emit('block', this.isBlocked);
      this.showDropdown = false;
    },
  },
};
</script>
