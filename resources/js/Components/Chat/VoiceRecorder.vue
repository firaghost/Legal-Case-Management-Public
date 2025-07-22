<template>
  <div class="voice-recorder">
    <button 
      @mousedown="startRecording"
      @mouseup="stopRecording"
      @mouseleave="stopRecording"
      @touchstart="startRecording"
      @touchend="stopRecording"
      class="p-2 rounded-full hover:bg-gray-100 focus:outline-none"
      :class="{ 'bg-red-100': isRecording }"
      :disabled="isProcessing"
      :title="isRecording ? 'Release to stop recording' : 'Hold to record'"
    >
      <svg 
        v-if="!isRecording"
        xmlns="http://www.w3.org/2000/svg" 
        class="h-6 w-6 text-gray-600" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke="currentColor"
      >
        <path 
          stroke-linecap="round" 
          stroke-linejoin="round" 
          stroke-width="2" 
          d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" 
        />
      </svg>
      <svg 
        v-else
        xmlns="http://www.w3.org/2000/svg" 
        class="h-6 w-6 text-red-600 animate-pulse" 
        viewBox="0 0 20 20" 
        fill="currentColor"
      >
        <path 
          fill-rule="evenodd" 
          d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" 
          clip-rule="evenodd" 
        />
      </svg>
    </button>
    
    <div v-if="isRecording" class="recording-indicator">
      <div class="pulse"></div>
      <span class="text-sm text-red-600 ml-2">Recording...</span>
    </div>
    
    <audio 
      v-if="audioUrl" 
      :src="audioUrl" 
      controls 
      class="mt-2 w-full"
      ref="audioPlayer"
    ></audio>
  </div>
</template>

<script>
export default {
  name: 'VoiceRecorder',
  props: {
    maxDuration: {
      type: Number,
      default: 120 // 2 minutes max recording
    }
  },
  data() {
    return {
      isRecording: false,
      isProcessing: false,
      mediaRecorder: null,
      audioChunks: [],
      audioUrl: null,
      audioBlob: null,
      timer: null,
      secondsElapsed: 0
    };
  },
  methods: {
    async startRecording(event) {
      // Prevent default to avoid any default behavior
      event.preventDefault();
      
      // If already recording, do nothing
      if (this.isRecording) return;
      
      try {
        // Request microphone access
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        
        // Initialize MediaRecorder
        this.mediaRecorder = new MediaRecorder(stream);
        this.audioChunks = [];
        
        // Handle data available event
        this.mediaRecorder.ondataavailable = (event) => {
          if (event.data.size > 0) {
            this.audioChunks.push(event.data);
          }
        };
        
        // Handle recording stop
        this.mediaRecorder.onstop = () => {
          // Create audio blob from chunks
          this.audioBlob = new Blob(this.audioChunks, { type: 'audio/wav' });
          this.audioUrl = URL.createObjectURL(this.audioBlob);
          
          // Emit the audio blob to parent component
          this.$emit('recording-complete', this.audioBlob);
          
          // Stop all tracks in the stream
          stream.getTracks().forEach(track => track.stop());
          
          // Reset timer
          clearInterval(this.timer);
          this.secondsElapsed = 0;
        };
        
        // Start recording
        this.mediaRecorder.start();
        this.isRecording = true;
        
        // Start timer
        this.timer = setInterval(() => {
          this.secondsElapsed++;
          
          // Stop recording if max duration reached
          if (this.secondsElapsed >= this.maxDuration) {
            this.stopRecording();
          }
        }, 1000);
        
      } catch (error) {
        console.error('Error accessing microphone:', error);
        this.$emit('error', 'Could not access microphone. Please ensure you have granted microphone permissions.');
      }
    },
    
    stopRecording() {
      if (!this.isRecording || !this.mediaRecorder) return;
      
      // Stop recording
      this.mediaRecorder.stop();
      this.isRecording = false;
      this.isProcessing = true;
      
      // Reset processing state after a short delay
      setTimeout(() => {
        this.isProcessing = false;
      }, 500);
    },
    
    // Clean up resources
    beforeUnmount() {
      if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
        this.mediaRecorder.stop();
      }
      clearInterval(this.timer);
      
      // Revoke object URL to prevent memory leaks
      if (this.audioUrl) {
        URL.revokeObjectURL(this.audioUrl);
      }
    }
  }
};
</script>

<style scoped>
.voice-recorder {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 8px;
}

.recording-indicator {
  display: flex;
  align-items: center;
  margin-top: 4px;
}

.pulse {
  width: 10px;
  height: 10px;
  background-color: #ef4444;
  border-radius: 50%;
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
  }
  70% {
    transform: scale(1);
    box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
  }
  100% {
    transform: scale(0.95);
    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
  }
}

button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
