<template>
  <div class="tg-composer">
    <!-- Reply preview -->
    <div 
      v-if="replyTo" 
      class="bg-blue-50 border-l-4 border-blue-500 rounded-r-lg p-3 mb-3 text-sm text-gray-700 flex justify-between items-start"
    >
      <div class="flex-1 min-w-0">
        <div class="font-medium text-blue-700 flex items-center">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd" />
          </svg>
          Replying to {{ replyTo.user?.name || 'message' }}
        </div>
        <div class="truncate text-gray-600 mt-0.5">
          {{ replyTo.content || '📎 Attachment' }}
        </div>
      </div>
      <button 
        @click="$emit('cancel-reply')" 
        class="text-gray-400 hover:text-gray-600 ml-2 p-1 rounded-full hover:bg-gray-100 transition-colors duration-150"
        aria-label="Cancel reply"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
    </div>

    <div class="flex items-end space-x-2 w-full">
      <!-- Attachment button -->
      <button 
        @click="openFileInput"
        class="p-2 text-gray-500 hover:text-blue-600 focus:outline-none transition-colors duration-200 rounded-full hover:bg-blue-50"
        aria-label="Add attachment"
        title="Attach file"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
        </svg>
      </button>
      
      <!-- Hidden file input -->
      <input 
        ref="fileInput"
        type="file" 
        class="hidden" 
        multiple
        @change="handleFileSelect"
      >
      
      <!-- Message input -->
      <div class="flex-1 relative">
        <div 
          ref="editable"
          contenteditable="true"
          @input="handleInput"
          @keydown.enter.exact.prevent="handleEnterKey"
          @paste="handlePaste"
          @focus="handleFocus"
          @blur="handleBlur"
          class="tg-composer__input"
          role="textbox"
          aria-multiline="true"
          aria-label="Type a message"
          :placeholder="attachments.length ? 'Add a caption...' : 'Type a message...'"
        ></div>
        
        <!-- Emoji picker toggle -->
        <button 
          @click="toggleEmojiPicker"
          class="absolute right-11 bottom-2 text-gray-500 hover:text-blue-600 focus:outline-none p-1.5 rounded-full hover:bg-blue-50 transition-colors duration-200"
          aria-label="Add emoji"
          type="button"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </button>
      </div>
      
      <!-- Send/Voice Recorder container -->
      <div class="flex items-center space-x-1">
        <!-- Voice recorder slot -->
        <slot name="voice-recorder"></slot>
        
        <!-- Send button -->
        <button
          @click="sendMessage"
          :disabled="!hasContent && !attachments.length"
          class="p-2 rounded-full text-white focus:outline-none transition-colors duration-200 flex-shrink-0"
          :class="{
            'bg-blue-500 hover:bg-blue-600 shadow-sm': hasContent || attachments.length,
            'bg-gray-300 cursor-not-allowed': !hasContent && !attachments.length
          }"
          aria-label="Send message"
          type="button"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Selected files preview -->
    <div v-if="attachments.length > 0" class="mt-3 flex flex-wrap gap-2">
      <div 
        v-for="(file, index) in attachments" 
        :key="index"
        class="relative border rounded-lg p-2 bg-gray-50 flex items-center"
      >
        <div class="flex-shrink-0 mr-2">
          <div v-if="file.type.startsWith('image/')" class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
            <img :src="file.preview" :alt="file.name" class="max-h-full max-w-full object-contain">
          </div>
          <div v-else class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 truncate">{{ file.name }}</p>
          <p class="text-xs text-gray-500">{{ formatFileSize(file.size) }}</p>
        </div>
        <button 
          @click="removeAttachment(index)"
          class="ml-2 text-gray-400 hover:text-gray-600"
          aria-label="Remove attachment"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>
    </div>
    
    <!-- Emoji picker -->
    <div v-if="showEmojiPicker" class="absolute bottom-16 right-4 z-10">
      <div class="bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden">
        <emoji-picker @select="addEmoji" />
      </div>
    </div>
  </div>
</template>

<script>
import EmojiPicker from './EmojiPicker.vue';

export default {
  name: 'MessageInput',
  
  components: {
    EmojiPicker,
  },
  
  props: {
    replyTo: {
      type: Object,
      default: null,
    },
  },
  
  data() {
    return {
      content: '',
      showEmojiPicker: false,
      attachments: [],
    };
  },
  
  computed: {
    hasContent() {
      return this.content.trim().length > 0;
    },
  },
  
  methods: {
    detectAttachmentType(file) {
      if (!file || !file.type) return 'document';
      const mime = file.type;
      if (mime.startsWith('image/')) return 'image';
      if (mime.startsWith('audio/')) return 'audio';
      if (mime.startsWith('video/')) return 'video';
      return 'document';
    },
    handleInput(event) {
      this.content = event.target.innerText;
      this.$emit('update:content', this.content);
      this.$emit('typing');
    },
    
    handleFocus() {
      this.$emit('focus');
    },
    
    handleBlur() {
      this.$emit('blur');
    },
    
    updateContent(event) {
      this.content = event.target.innerText;
      this.$emit('update:content', this.content);
    },
    
    handleEnterKey(event) {
      if (event.shiftKey) {
        // Insert a new line
        document.execCommand('insertLineBreak');
      } else {
        this.sendMessage();
      }
    },
    
    async sendMessage() {
      if (!this.content.trim() && this.attachments.length === 0) return;
      
      const detectedType = this.attachments.length ? this.detectAttachmentType(this.attachments[0]) : 'text';
      const message = {
        content: this.content.trim(),
        attachments: this.attachments,
        type: detectedType,
        replyToId: this.replyTo?.id,
      };
      
      this.$emit('send-message', message);
      
      // Reset input
      this.content = '';
      this.attachments = [];
      this.$refs.editable.textContent = '';
      this.$emit('cancel-reply');
      
      // Focus back on the input
      this.$nextTick(() => {
        this.$refs.editable.focus();
      });
    },
    
    toggleEmojiPicker() {
      this.showEmojiPicker = !this.showEmojiPicker;
      if (this.showEmojiPicker) {
        // Close when clicking outside
        this.$nextTick(() => {
          document.addEventListener('click', this.handleClickOutside);
        });
      } else {
        document.removeEventListener('click', this.handleClickOutside);
      }
    },
    
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.showEmojiPicker = false;
        document.removeEventListener('click', this.handleClickOutside);
      }
    },
    
    addEmoji(emoji) {
      const selection = window.getSelection();
      const range = selection.getRangeAt(0);
      const textNode = document.createTextNode(emoji);
      
      range.deleteContents();
      range.insertNode(textNode);
      
      // Move cursor after the emoji
      range.setStartAfter(textNode);
      range.setEndAfter(textNode);
      selection.removeAllRanges();
      selection.addRange(range);
      
      // Update content
      this.content = this.$refs.editable.textContent || '';
      
      // Focus back on the input
      this.$refs.editable.focus();
      
      // Close the picker after selection
      this.showEmojiPicker = false;
      document.removeEventListener('click', this.handleClickOutside);
    },
    
    openFileInput() {
      this.$refs.fileInput.click();
    },
    
    handleFileSelect(event) {
      const files = Array.from(event.target.files);
      
      files.forEach(file => {
        // Check file size (max 10MB)
        if (file.size > 10 * 1024 * 1024) {
          alert(`File ${file.name} is too large. Maximum size is 10MB.`);
          return;
        }
        
        // Create a preview URL for images
        if (file.type.startsWith('image/')) {
          file.preview = URL.createObjectURL(file);
        }
        
        this.attachments.push(file);
      });
      
      // Reset file input
      event.target.value = '';
    },
    
    removeAttachment(index) {
      // Revoke object URL if it exists
      if (this.attachments[index].preview) {
        URL.revokeObjectURL(this.attachments[index].preview);
      }
      this.attachments.splice(index, 1);
    },
    
    formatFileSize(bytes) {
      if (bytes === 0) return '0 Bytes';
      const k = 1024;
      const sizes = ['Bytes', 'KB', 'MB', 'GB'];
      const i = Math.floor(Math.log(bytes) / Math.log(k));
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    },
    
    handlePaste(event) {
      // Handle pasted files (e.g., screenshots)
      const items = (event.clipboardData || event.originalEvent.clipboardData).items;
      
      for (const item of items) {
        if (item.kind === 'file') {
          const blob = item.getAsFile();
          if (blob && blob.type.startsWith('image/')) {
            // Create a file from the blob
            const file = new File(
              [blob],
              `pasted-${Date.now()}.${blob.type.split('/')[1]}`,
              { type: blob.type }
            );
            
            file.preview = URL.createObjectURL(blob);
            this.attachments.push(file);
            
            // Prevent the default paste behavior
            event.preventDefault();
          }
        }
      }
    },
  },
  
  beforeDestroy() {
    // Clean up event listeners
    document.removeEventListener('click', this.handleClickOutside);
    
    // Revoke object URLs for previews
    this.attachments.forEach(file => {
      if (file.preview) {
        URL.revokeObjectURL(file.preview);
      }
    });
  },
};
</script>
