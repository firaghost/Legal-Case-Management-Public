<template>
  <div 
    ref="messagesContainer" 
    class="flex-1 overflow-y-auto p-4 space-y-4"
    @scroll="handleScroll"
    @contextmenu.prevent
  >
    <!-- Date separator -->
    <div v-if="showDateSeparator" class="text-center my-4">
      <span class="bg-gray-100 text-xs text-gray-500 px-2 py-1 rounded-full">
        {{ formatDateSeparator(messages[0]?.created_at) }}
      </span>
    </div>

    <!-- Loading indicator -->
    <div v-if="loading" class="flex justify-center py-4">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
    </div>

    <!-- Messages -->
    <template v-for="(message, index) in messages" :key="message.id">
      <!-- Date separator if needed -->
      <div 
        v-if="shouldShowDateSeparator(index)" 
        class="text-center my-4"
      >
        <span class="bg-gray-100 text-xs text-gray-500 px-2 py-1 rounded-full">
          {{ formatDateSeparator(message.created_at) }}
        </span>
      </div>

      <!-- Message -->
      <div 
        class="tg-message-row" 
        :class="{
          'tg-message-row--outgoing': isCurrentUser(message),
          'mb-1': shouldGroupWithNext(index)
        }"
      >
        <!-- Avatar (only for received messages) -->
        <div v-if="!isCurrentUser(message) && !shouldGroupWithPrevious(index)" class="flex-shrink-0 mr-2">
          <img 
            :src="getAvatar(message)" 
            :alt="getUserName(message)"
            class="h-8 w-8 rounded-full object-cover"
          >
        </div>
        <div v-else class="w-10"></div> <!-- Spacer -->

        <!-- Message content -->
        <div class="flex flex-col" style="max-width: 90%;">
          <!-- Sender name (only for group chats) -->
          <div 
            v-if="!isCurrentUser(message) && !shouldGroupWithPrevious(index) && isGroupChat" 
            class="text-xs text-gray-500 mb-1 ml-1"
          >
            {{ getUserName(message) }}
          </div>
          
          <!-- Message bubble -->
          <div 
            class="tg-bubble" 
            @contextmenu.stop="showContextMenu($event, message)"
            :class="{
              'tg-bubble--out': isCurrentUser(message),
              'tg-bubble--in': !isCurrentUser(message),
              'rounded-tl-2xl': !isCurrentUser(message) && shouldGroupWithPrevious(index),
              'rounded-tr-2xl': isCurrentUser(message) && shouldGroupWithPrevious(index),
              'rounded-bl-2xl': isCurrentUser(message) && !shouldGroupWithNext(index),
              'rounded-br-2xl': !isCurrentUser(message) && !shouldGroupWithNext(index),
            }"
          >
            <!-- Text content -->
            <div class="message-content" style="
              white-space: normal;
              word-break: break-word;
              overflow-wrap: break-word;
              hyphens: auto;
              max-width: 100%;
              min-width: 0;
              display: inline-block;
              text-align: left;
            ">
              <template v-if="message.reply_to">
                  <div class="tg-reply-block">
                      <span class="tg-reply-author">{{ message.reply_to.user?.name || 'Unknown' }}</span>
                      <span class="tg-reply-text">{{ message.reply_to.body || '📎 Attachment' }}</span>
                </div>
              </template>
              {{ message.content || message.body }}
            </div>
            
            <!-- Attachments -->
            <div v-if="getAttachments(message).length > 0" class="mt-2 space-y-2">
              <div 
                v-for="(attachment, aIndex) in getAttachments(message)" 
                :key="aIndex"
                class="rounded-lg overflow-hidden border border-gray-200"
              >
                <!-- Image attachment -->
                <img 
                  v-if="isImage(attachment)" 
                  :src="attachment.url" 
                  :alt="attachment.name || 'Image attachment'"
                  class="max-w-full h-auto rounded-lg"
                  @load="scrollToBottom"
                >
                
                <!-- Audio attachment -->
                <div v-else-if="isAudio(attachment)" class="p-3">
                  <div class="flex items-center">
                    <button 
                      @click="toggleAudioPlayback(attachment)"
                      class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 hover:bg-blue-200 text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                      <svg v-if="!isAudioPlaying(attachment.url)" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                      </svg>
                      <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                      </svg>
                    </button>
                    <div class="ml-3 flex-1">
                      <div class="text-sm font-medium text-gray-900">
                        {{ attachment.name || 'Audio message' }}
                      </div>
                      <div class="flex items-center mt-1">
                        <div class="h-1 flex-1 bg-gray-200 rounded-full overflow-hidden">
                          <div 
                            class="h-full bg-blue-500 rounded-full" 
                            :style="{ width: getAudioProgress(attachment.url) + '%' }"
                          ></div>
                        </div>
                        <span class="ml-2 text-xs text-gray-500 w-16 text-right">
                          {{ formatAudioTime(getAudioProgress(attachment.url) * (attachment.duration || 30) / 100) }} / {{ formatAudioTime(attachment.duration || 0) }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Other file types -->
                <div v-else class="p-3 flex items-center">
                  <div class="p-2 bg-gray-100 rounded-lg mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">
                      {{ attachment.name || 'File' }}
                    </p>
                    <p class="text-xs text-gray-500">
                      {{ formatFileSize(attachment.size) }}
                    </p>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Audio message -->
            <div v-if="message.audio_url" class="mt-2">
              <audio 
                controls 
                :src="message.audio_url" 
                class="w-full h-10"
                @play="onAudioPlay(message)"
              ></audio>
            </div>
            
            <!-- Message status and time -->
            <div class="flex items-center justify-end mt-1 space-x-1">
              <span class="text-xs opacity-75" :class="{
                'text-white text-opacity-70': isCurrentUser(message),
                'text-gray-500': !isCurrentUser(message)
              }">
                {{ formatMessageTime(message.created_at) }}
              </span>
              
              <!-- Message status indicator -->
              <span v-if="isCurrentUser(message)" class="ml-1">
                <template v-if="message.status === 'sending'">
                  <svg class="h-3 w-3 text-gray-400 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <circle cx="10" cy="10" r="2" />
                  </svg>
                </template>
                <template v-else-if="message.status === 'sent'">
                  <svg class="h-3 w-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </template>
                <template v-else-if="message.status === 'delivered'">
                  <svg class="h-3 w-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 11.586l7.293-7.293a1 1 0 011.414 1.414l-8 8z" />
                  </svg>
                </template>
                <template v-else-if="message.status === 'read'">
                  <svg class="h-3 w-3 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L9 11.586l7.293-7.293a1 1 0 011.414 1.414l-8 8z" />
                  </svg>
                </template>
                <template v-else-if="message.status === 'error'">
                  <svg class="h-3 w-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </template>
              </span>
            </div>
          </div>
        </div>
      </div>
    </template>
    
    <!-- Typing indicator -->
    <div v-if="typingUsers.length > 0" class="flex items-center mt-2">
      <div class="flex-shrink-0 mr-2">
        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
          <span class="text-xs text-gray-500">👤</span>
        </div>
      </div>
      <div class="bg-gray-100 text-gray-800 px-4 py-2 rounded-2xl rounded-tl-none">
        <div class="flex space-x-1">
          <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
          <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
          <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
        </div>
      </div>
    </div>
    
    <div ref="bottomMarker"></div>
  </div>

  <!-- Context Menu -->
  <MessageContextMenu
    v-if="contextMenu.visible"
    :x="contextMenu.x"
    :y="contextMenu.y"
    :message="contextMenu.message"
    :visible="contextMenu.visible"
    @action="handleContextAction"
    @close="hideContextMenu"
  />
</template>

<script>
import MessageContextMenu from './MessageContextMenu.vue';
export default {
  name: 'MessageList',
  
  props: {
    messages: {
      type: Array,
      default: () => [],
    },
    currentUser: {
      type: Object,
      required: true,
    },
    loading: {
      type: Boolean,
      default: false,
    },
    typingUsers: {
      type: Array,
      default: () => [],
    },
    isGroupChat: {
      type: Boolean,
      default: false,
    },
  },
  
  data() {
    return {
      defaultAvatar: '/images/default-avatar.png',
      loading: false,
      
      audioPlayers: {},
      audioProgress: {},
      currentAudio: null,
      lastScrollHeight: 0,
      typingUsers: [],
      contextMenu: { visible: false, x: 0, y: 0, message: null },
    };
  
  },

  computed: {
    showDateSeparator() {
      return this.messages.length > 0 && !this.loading;
    },
  },
  
  watch: {
    messages: {
      immediate: true,
      handler() {
        this.$nextTick(() => {
          this.scrollToBottom();
        });
      },
    },
  },
  
  methods: {
    handleContextAction({ action, message }) {
      this.$emit('context-action', { action, message });
    },
    getAttachments(message) {
      if (Array.isArray(message.attachments) && message.attachments.length) {
        return message.attachments;
      }
      if (message.attachment) {
        return [message.attachment];
      }
      return [];
    },
    isCurrentUser(message) {
      return message.user_id === this.currentUser.id;
    },
    
    isImage(attachment) {
      if (!attachment) return false;
      const type = attachment.type || attachment.mime_type || '';
      return type.startsWith('image/');
    },
    
    getUserName(message) {
      return message.user?.name || 'Unknown User';
    },

    getAvatar(message) {
      if (message.user?.avatar) return message.user.avatar;
      const name = this.getUserName(message);
      return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=random`;
    },

    isAudio(attachment) {
      if (!attachment) return false;
      const type = attachment.type || attachment.mime_type || '';
      if (type.startsWith('audio') || type === 'audio') return true;
      if (attachment.url && attachment.url.match(/\.(mp3|wav|ogg|m4a|webm)$/i)) return true;
      return false;
    },
    
    getAudioProgress(audioUrl) {
      if (!audioUrl || !this.audioProgress[audioUrl]) return 0;
      const { currentTime, duration } = this.audioProgress[audioUrl];
      if (!duration) return 0;
      return Math.min(100, Math.round((currentTime / duration) * 100));
    },
    
    formatAudioTime(seconds) {
      if (isNaN(seconds)) return '0:00';
      const mins = Math.floor(seconds / 60);
      const secs = Math.floor(seconds % 60);
      return `${mins}:${secs.toString().padStart(2, '0')}`;
    },
    
    toggleAudioPlayback(attachment) {
      const audioUrl = attachment.url;
      let audio = this.audioPlayers[audioUrl];
      
      if (!audio) {
        audio = new Audio(audioUrl);
        this.$set(this.audioPlayers, audioUrl, audio);
        
        audio.addEventListener('timeupdate', () => {
          this.$set(this.audioProgress, audioUrl, {
            currentTime: audio.currentTime,
            duration: audio.duration || attachment.duration || 0
          });
        });
        
        audio.addEventListener('ended', () => {
          this.currentAudio = null;
          this.$set(this.audioProgress, audioUrl, {
            currentTime: 0,
            duration: audio.duration || attachment.duration || 0
          });
        });
      }
      
      if (this.currentAudio && this.currentAudio !== audio) {
        this.currentAudio.pause();
        this.currentAudio.currentTime = 0;
      }
      
      if (audio.paused) {
        audio.play().then(() => {
          this.currentAudio = audio;
        }).catch(error => {
          console.error('Error playing audio:', error);
        });
      } else {
        audio.pause();
        audio.currentTime = 0;
        this.currentAudio = null;
      }
    },
    
    isAudioPlaying(audioUrl) {
      const audio = this.audioPlayers[audioUrl];
      return audio && !audio.paused;
    },
    
    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    formatMessageTime(timestamp) {
      if (!timestamp) return '';
      const date = new Date(timestamp);
      return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    },
    
    formatDateSeparator(timestamp) {
      if (!timestamp) return '';
      const date = new Date(timestamp);
      const today = new Date();
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);
      
      if (date.toDateString() === today.toDateString()) {
        return 'Today';
      } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
      } else {
        return date.toLocaleDateString([], { 
          year: 'numeric', 
          month: 'long', 
          day: 'numeric' 
        });
      }
    },
    
    shouldShowDateSeparator(index) {
      if (index === 0) return true;
      
      const currentDate = new Date(this.messages[index].created_at).toDateString();
      const prevDate = new Date(this.messages[index - 1].created_at).toDateString();
      
      return currentDate !== prevDate;
    },
    
    shouldGroupWithPrevious(index) {
      if (index === 0) return false;
      
      const currentMsg = this.messages[index];
      const prevMsg = this.messages[index - 1];
      
      // Don't group if different senders
      if (currentMsg.user_id !== prevMsg.user_id) return false;
      
      // Don't group if time difference is more than 5 minutes
      const timeDiff = (new Date(currentMsg.created_at) - new Date(prevMsg.created_at)) / (1000 * 60);
      return timeDiff < 5;
    },
    
    shouldGroupWithNext(index) {
      if (index === this.messages.length - 1) return false;
      
      const currentMsg = this.messages[index];
      const nextMsg = this.messages[index + 1];
      
      // Don't group if different senders
      if (currentMsg.user_id !== nextMsg.user_id) return false;
      
      // Don't group if time difference is more than 5 minutes
      const timeDiff = (new Date(nextMsg.created_at) - new Date(currentMsg.created_at)) / (1000 * 60);
      return timeDiff < 5;
    },
    
    scrollToBottom() {
      this.$nextTick(() => {
        const container = this.$refs.messagesContainer;
        if (container) {
          container.scrollTop = container.scrollHeight;
        }
      });
    },
    
    hideContextMenu() {
      this.contextMenu.visible = false;
      this.contextMenu.message = null;
    },
    handleContextAction({ action, message }) {
      switch (action) {
        case 'reply':
          this.$emit('reply', message);
          this.hideContextMenu();
          break;
        case 'edit':
          this.$emit('edit', message);
          this.hideContextMenu();
          break;
        case 'copy':
          navigator.clipboard.writeText(message.content || message.body || '');
          this.hideContextMenu();
          break;
        case 'forward':
          this.$emit('forward', message);
          this.hideContextMenu();
          break;
        case 'delete':
           if (confirm('Are you sure you want to delete this message?')) {
             this.$emit('delete', message);
           }
           this.hideContextMenu();
           break;
      }
    },
    showContextMenu(event, message) {
      // Prevent the default context menu
      event.preventDefault();
      
      // Close any existing context menu
      this.hideContextMenu();
      
      // Set the new context menu position and message
      this.contextMenu = {
        visible: true,
        x: event.clientX,
        y: event.clientY,
        message
      };
      
      // Prevent the default browser context menu
      return false;
    },

    onAudioPlay(message) {
      // Pause other audio elements when a new one is played
      const audioElements = document.querySelectorAll('audio');
      audioElements.forEach(audio => {
        if (audio !== event.target) {
          audio.pause();
        }
      });
      
      // Mark as played/read if needed
      if (!this.isCurrentUser(message)) {
        this.$emit('message-read', message.id);
      }
    },
  },
  components: {
    MessageContextMenu,
  }
};
</script>

<style scoped>
/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

::-webkit-scrollbar-track {
  background: #807c7c;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Animation for message appearance */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.message-enter-active {
  animation: fadeIn 0.2s ease-out;
}
</style>
