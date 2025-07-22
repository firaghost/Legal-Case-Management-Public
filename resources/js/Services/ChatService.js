import axios from 'axios';

class ChatService {
  constructor() {
    this.api = axios.create({
      baseURL: '/api/chat',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      withCredentials: true,
    });
    
    // Add request interceptor to include auth token
    this.api.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem('auth_token');
        if (token) {
          config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
      },
      (error) => {
        return Promise.reject(error);
      }
    );
    
    // Add response interceptor to handle errors
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response) {
          // Handle specific status codes
          if (error.response.status === 401) {
            // Unauthorized - redirect to login
            window.location.href = '/login';
          } else if (error.response.status === 403) {
            // Forbidden - show access denied
            console.error('Access denied:', error.response.data);
          } else if (error.response.status === 422) {
            // Validation errors - handled by the component
            console.error('Validation errors:', error.response.data.errors);
          } else if (error.response.status >= 500) {
            // Server error
            console.error('Server error:', error.response.data);
          }
        } else if (error.request) {
          // The request was made but no response was received
          console.error('No response received:', error.request);
        } else {
          // Something happened in setting up the request
          console.error('Request error:', error.message);
        }
        
        return Promise.reject(error);
      }
    );
  }
  
  // Conversations
  async getConversations(params = {}) {
    const response = await this.api.get('/conversations', { params });
    return response.data;
  }
  
  async getConversation(conversationId) {
    const response = await this.api.get(`/conversations/${conversationId}`);
    return response.data;
  }
  
  async createConversation(participantIds, title = null, isGroup = false) {
    const response = await this.api.post('/conversations', {
      participant_ids: Array.isArray(participantIds) ? participantIds : [participantIds],
      title,
      is_group: isGroup,
    });
    return response.data;
  }
  
  async updateConversation(conversationId, data) {
    const response = await this.api.put(`/conversations/${conversationId}`, data);
    return response.data;
  }
  
  async deleteConversation(conversationId) {
    const response = await this.api.delete(`/conversations/${conversationId}`);
    return response.data;
  }
  
  async markAsRead(conversationId) {
    const response = await this.api.post(`/conversations/${conversationId}/read`);
    return response.data;
  }
  
  async addParticipants(conversationId, participantIds) {
    const response = await this.api.post(`/conversations/${conversationId}/participants`, {
      participant_ids: Array.isArray(participantIds) ? participantIds : [participantIds],
    });
    return response.data;
  }
  
  async removeParticipant(conversationId, participantId) {
    const response = await this.api.delete(`/conversations/${conversationId}/participants/${participantId}`);
    return response.data;
  }
  
  // Messages
  async getMessages(conversationId, params = {}) {
    const response = await this.api.get(`/conversations/${conversationId}/messages`, { params });
    return response.data;
  }
  
  async sendMessage(conversationId, content, replyToId = null, attachments = []) {
    const formData = new FormData();
    // Backend expects 'body' field for message text
    formData.append('body', content);
    formData.append('conversation_id', conversationId);
    
    if (replyToId) {
      formData.append('reply_to', replyToId);
    }
    
    // Add attachments to form data
    attachments.forEach((file, index) => {
      formData.append(`attachments[${index}]`, file);
    });
    
    const response = await this.api.post('/messages', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    
    return response.data;
  }
  
  async sendAudioMessage(conversationId, audioBlob) {
    const formData = new FormData();
    formData.append('audio', audioBlob, `audio-${Date.now()}.webm`);
    formData.append('conversation_id', conversationId);
    
    const response = await this.api.post('/messages/audio', formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    
    return response.data;
  }
  
  async deleteMessage(messageId) {
    const response = await this.api.delete(`/messages/${messageId}`);
    return response.data;
  }
  
  async markMessageAsRead(messageId) {
    const response = await this.api.post(`/messages/${messageId}/read`);
    return response.data;
  }
  
  // Reactions
  async addReaction(messageId, emoji) {
    const response = await this.api.post(`/messages/${messageId}/reactions`, { emoji });
    return response.data;
  }
  
  async removeReaction(messageId, emoji) {
    const response = await this.api.delete(`/messages/${messageId}/reactions/${encodeURIComponent(emoji)}`);
    return response.data;
  }
  
  // Typing indicators
  async sendTypingStatus(conversationId, isTyping = true) {
    const response = await this.api.post(`/conversations/${conversationId}/typing`, { typing: isTyping });
    return response.data;
  }
  
  // Search
  async searchMessages(query, conversationId = null) {
    const params = { q: query };
    if (conversationId) {
      params.conversation_id = conversationId;
    }
    
    const response = await this.api.get('/search', { params });
    return response.data;
  }
  
  // Users
  async searchUsers(query) {
    const response = await this.api.get('/users/search', { params: { q: query } });
    return response.data;
  }
  
  async getUserStatus(userId) {
    const response = await this.api.get(`/users/${userId}/status`);
    return response.data;
  }
  
  // Uploads
  async uploadFile(file, onUploadProgress = null) {
    const formData = new FormData();
    formData.append('file', file);
    
    const config = {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    };
    
    if (onUploadProgress) {
      config.onUploadProgress = onUploadProgress;
    }
    
    const response = await this.api.post('/uploads', formData, config);
    return response.data;
  }
  
  // Notifications
  async getUnreadCount() {
    const response = await this.api.get('/notifications/unread-count');
    return response.data;
  }
  
  async markNotificationsAsRead(notificationIds = []) {
    const response = await this.api.post('/notifications/mark-as-read', { ids: notificationIds });
    return response.data;
  }
}

export default new ChatService();
