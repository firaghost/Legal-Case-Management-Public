<script setup>
import { ref, reactive, computed, onMounted, watch, nextTick, onBeforeUnmount, provide } from 'vue';
import { usePage } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import axios from 'axios';
import ChatContainer from '@/Components/Chat/ChatContainer.vue';
import UserPickerModal from '@/Components/Chat/UserPickerModal.vue';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Props
const props = defineProps({
    initialData: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    },

});

// Reactive State
const isLoading = ref(true);
const error = ref(null);
const showUserPicker = ref(false);
// Prefill conversations from the server (initialData) if present
const normalizeConvs = (convSrc) => {
    if (!convSrc) return [];
    if (Array.isArray(convSrc)) return convSrc;
    if (Array.isArray(convSrc.data)) return convSrc.data;
    return [];
};
const conversations = ref(normalizeConvs(props.initialData?.conversations));
const currentConversation = ref(null);
const currentUser = ref(props.auth.user);
const isSending = ref(false);
const isLoadingMessages = ref(false);
const chatContainer = ref(null);
const echo = ref(null);
const isConnected = ref(false);
const isConnecting = ref(true);
const connectionError = ref(null);
const showNewMessageNotification = ref(false);
const unreadCount = ref(0);
const isTyping = ref(false);
const audioRecorder = ref(null);
const audioChunks = ref([]);
const isRecording = ref(false);
const recordingTime = ref(0);
const recordingTimer = ref(null);
const onlineUsers = ref(new Set());

// Make onlineUsers available to nested components
provide('onlineUsers', onlineUsers);

// Computed Properties
const currentConversationId = computed(() => currentConversation.value?.id || null);
const connectionStatus = computed(() => {
    if (isConnecting.value) return 'connecting';
    if (connectionError.value) return 'error';
    if (isConnected.value) return 'connected';
    return 'disconnected';
});

// --- Methods ---

const scrollToBottom = () => {
    nextTick(() => {
        const container = document.querySelector('.chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });
};

const playNotificationSound = () => {
    const audio = new Audio('/sounds/notification.mp3');
    audio.play().catch(e => console.error("Error playing notification sound:", e));
};

const showDesktopNotification = (message) => {
    if (Notification.permission === 'granted' && document.hidden) {
        const notification = new Notification(`New message from ${message.user?.name || 'Someone'}`, {
            body: message.content || 'Sent an attachment',
            icon: message.user?.avatar || '/images/logo.png',
        });
        notification.onclick = () => {
            window.focus();
            if (currentConversation.value?.id !== message.conversation_id) {
                const conversation = conversations.value.find(c => c.id === message.conversation_id);
                if (conversation) {
                    selectConversation(conversation);
                }
            }
            notification.close();
        };
    }
};

const addMessageToConversation = (message) => {
    console.log('=== addMessageToConversation called ===');
    console.log('Message to add:', JSON.parse(JSON.stringify(message)));
    
    try {
        if (!message) {
            console.error('Message is null or undefined');
            return;
        }
        
        if (!message.conversation_id) {
            console.error('Invalid message format - missing conversation_id:', message);
            return;
        }

        console.log('Looking for conversation with ID:', message.conversation_id);
        console.log('Available conversations:', conversations.value.map(c => ({ id: c.id, title: c.title || 'No title' })));
        
        const conversation = conversations.value.find(c => c.id == message.conversation_id);
        
        if (!conversation) {
            console.warn('Conversation not found for message. Conversation ID:', message.conversation_id);
            console.warn('Available conversation IDs:', conversations.value.map(c => c.id));
            return;
        }
        
        console.log('Found conversation:', { 
            id: conversation.id, 
            title: conversation.title || 'No title',
            messagesCount: conversation.messages?.length || 0 
        });

        // Ensure messages array exists
        if (!Array.isArray(conversation.messages)) {
            console.log('Initializing messages array for conversation');
            conversation.messages = [];
        } else {
            console.log(`Conversation has ${conversation.messages.length} existing messages`);
        }

        // Generate a stable ID for the message if it doesn't have one
        const messageId = message.id || `temp-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        console.log('Using message ID:', messageId);
        
        // Check if message already exists
        const existingMessageIndex = conversation.messages.findIndex(m => m.id === messageId || 
            (m.id && message.id && m.id === message.id));
            
        console.log('Existing message index:', existingMessageIndex);

        if (existingMessageIndex !== -1) {
            // Update existing message
            console.log('Updating existing message at index:', existingMessageIndex);
            const existingMessage = conversation.messages[existingMessageIndex];
            const isCurrentUserMessage = (message.user_id === currentUser.value?.id) || 
                                     (message.user?.id === currentUser.value?.id) ||
                                     existingMessage.isCurrentUser;
            
            conversation.messages[existingMessageIndex] = {
                ...existingMessage,
                ...message,
                id: messageId, // Ensure we use the correct ID
                // Ensure content is set (fallback to body for backward compatibility)
                content: message.content || message.body || existingMessage.content || existingMessage.body || '',
                // Ensure user object is complete
                user: {
                    ...(existingMessage.user || {}),
                    ...(message.user || {}),
                    id: message.user_id || message.user?.id || existingMessage.user_id || existingMessage.user?.id || null,
                    name: message.user?.name || existingMessage.user?.name || 'Unknown',
                    avatar: message.user?.avatar || existingMessage.user?.avatar || null,
                    email: message.user?.email || existingMessage.user?.email || null
                },
                // Ensure direct properties are set
                user_id: message.user_id || existingMessage.user_id,
                user_name: message.user?.name || existingMessage.user_name || 'Unknown',
                status: message.status || existingMessage.status || 'sent',
                isCurrentUser: isCurrentUserMessage
            };
            console.log('Updated message:', conversation.messages[existingMessageIndex]);
        } else {
            // Add new message
            console.log('Adding new message to conversation');
            const isCurrentUserMessage = (message.user_id === currentUser.value?.id) || 
                                     (message.user?.id === currentUser.value?.id);
            
            const newMessage = {
                id: messageId,
                content: message.content || message.body || '', // Use content for MessageList compatibility
                type: message.type || 'text',
                user_id: message.user_id || message.user?.id || currentUser.value?.id,
                user_name: message.user?.name || currentUser.value?.name || 'Unknown',
                conversation_id: message.conversation_id,
                created_at: message.created_at || new Date().toISOString(),
                status: message.status || 'sent',
                // Include both user object and direct properties for compatibility
                user: {
                    id: message.user_id || message.user?.id || currentUser.value?.id,
                    name: message.user?.name || currentUser.value?.name || 'Unknown',
                    avatar: message.user?.avatar || currentUser.value?.avatar || null,
                    email: message.user?.email || currentUser.value?.email || null
                },
                // Direct properties for easier access in templates
                isCurrentUser: isCurrentUserMessage
            };
            
            console.log('New message object:', newMessage);
            conversation.messages.push(newMessage);
        }

        // Update conversation's last message if this is the newest
        const lastMessage = [...conversation.messages].sort((a, b) => 
            new Date(b.created_at) - new Date(a.created_at))[0];
            
        console.log('Last message in conversation after update:', lastMessage);
        
        if (lastMessage) {
            console.log('Updating conversation last message');
            conversation.last_message = { ...lastMessage };
        }

        // Trigger UI update by creating a new reference
        console.log('Triggering UI update with new conversations array');
        const conversationIndex = conversations.value.findIndex(c => c.id === conversation.id);
        if (conversationIndex !== -1) {
            conversations.value[conversationIndex] = { ...conversation };
            conversations.value = [...conversations.value];
        } else {
            console.warn('Conversation not found in conversations array during update');
        }
        
        console.log('Conversation after update:', {
            id: conversation.id,
            messagesCount: conversation.messages?.length,
            lastMessage: conversation.last_message
        });

        // Scroll to bottom if this is the current conversation
        if (currentConversation.value?.id === message.conversation_id) {
            console.log('Scrolling to bottom for current conversation');
            nextTick(() => {
                scrollToBottom();
            });
        }
    } catch (error) {
        console.error('=== Error in addMessageToConversation ===');
        console.error('Error:', error);
        console.error('Message that caused the error:', message);
        console.error('Current user:', currentUser.value);
        console.error('Current conversations state:', conversations.value);
    }
    
    console.log('=== End of addMessageToConversation ===');
};

const markMessageAsDelivered = (messageId) => {
    if (currentConversation.value) {
        const message = currentConversation.value.messages.find(m => m.id === messageId);
        if (message) {
            message.status = 'delivered';
        }
    }
};

const markMessageAsRead = (messageId) => {
    if (currentConversation.value) {
        currentConversation.value.messages.forEach(message => {
            if(message.status !== 'read') {
                message.status = 'read';
            }
        });
    }
};

const handleNewMessage = (message) => {
    if (currentConversation.value?.id === message.conversation_id) {
        addMessageToConversation(message);
        if (document.hasFocus()) {
            markAsRead(message.conversation_id);
        } 
    } else {
        const conversation = conversations.value.find(c => c.id === message.conversation_id);
        if (conversation) {
            conversation.unread_count = (conversation.unread_count || 0) + 1;
            conversation.last_message = message;
            conversations.value = [
                conversation,
                ...conversations.value.filter(c => c.id !== conversation.id)
            ];
        }
        unreadCount.value++;
        showNewMessageNotification.value = true;
        showDesktopNotification(message);
    }
    playNotificationSound();
};

const handleMessageDelivered = ({ message_id, conversation_id }) => {
    if (currentConversation.value?.id === conversation_id) {
        markMessageAsDelivered(message_id);
    }
};

const handleMessageRead = ({ conversation_id }) => {
    if (currentConversation.value?.id === conversation_id) {
        markMessageAsRead();
    }
};

const handleTyping = ({ user }) => {
    if (user.id !== currentUser.value.id) {
        isTyping.value = true;
    }
};

const handleStopTyping = () => {
    isTyping.value = false;
};

const leaveConversationChannel = (conversationId) => {
    if (echo.value) {
        echo.value.leave(`conversation.${conversationId}`);
    }
};

const joinConversationChannel = (conversationId) => {
    if (echo.value) {
        const channel = echo.value.private(`conversation.${conversationId}`);
        channel.listen('.MessageSent', handleNewMessage)
            .listen('.MessageDelivered', handleMessageDelivered)
            .listen('.MessageRead', handleMessageRead)
            .listenForWhisper('typing', handleTyping)
            .listenForWhisper('stop-typing', handleStopTyping);
    }
};

const markAsRead = async (conversationId) => {
    try {
        // Get CSRF token from meta tag
        const token = document.head.querySelector('meta[name="csrf-token"]');
        
        // Get the current user's session cookie
        const sessionCookie = document.cookie
            .split('; ')
            .find(row => row.startsWith('laravel_session='));
            
        // Make the request with proper headers and credentials
        await axios.post(`/api/conversations/${conversationId}/mark-as-seen`, {}, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token ? token.content : '',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${props.auth.token || ''}`,
                'Cookie': sessionCookie || ''
            },
            withCredentials: true, // Important for session cookies
            // Ensure cookies are sent with the request
            withXSRFToken: true
        });
        
        // Update the local state to reflect messages are read
        const conversation = conversations.value.find(c => c.id === conversationId);
        if (conversation) {
            conversation.unread_count = 0;
        }
    } catch (err) {
        console.error('Failed to mark messages as read:', err);
        if (err.response) {
            console.error('Response status:', err.response.status);
            console.error('Response data:', err.response.data);
            console.error('Response headers:', err.response.headers);
        }
        // Don't show error to user for read receipts
    }
};

const loadMessages = async (conversationId) => {
    if (!conversationId) {
        console.error('No conversation ID provided to loadMessages');
        return;
    }
    
    console.log('Loading messages for conversation:', conversationId);
    
    try {
        isLoadingMessages.value = true;
        error.value = null;
        
        // Check if we already have this conversation in our list
        let conversation = conversations.value.find(c => c.id === conversationId);
        
        if (!conversation) {
            console.log('Conversation not found in local list, fetching from server...');
            // If we don't have this conversation, fetch it first
            const convResponse = await axios.get(`/api/conversations/${conversationId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                withCredentials: true
            });
            
            const convData = convResponse?.data?.data || convResponse?.data;
            if (convData) {
                // Add the conversation to our list
                conversations.value.unshift(convData);
                conversation = convData;
            } else {
                throw new Error('Could not load conversation details');
            }
        }
        
        // Fetch messages for the conversation
        const response = await axios.get(`/api/conversations/${conversationId}/messages`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            withCredentials: true
        });
        
        console.log('Messages API response:', response.data);
        
        // Update the conversation with the loaded messages
        const messages = response.data.data || [];
        
        // Find the conversation in our list (in case it was added above)
        const convIndex = conversations.value.findIndex(c => c.id === conversationId);
        if (convIndex >= 0) {
            // Preserve any existing messages that aren't in the response
            const existingMessages = conversations.value[convIndex].messages || [];
            const existingMessageIds = new Set(existingMessages.map(m => m.id));
            const newMessages = messages.filter(m => !existingMessageIds.has(m.id));
            
            // Update the conversation with combined messages
            conversations.value[convIndex].messages = [...newMessages, ...existingMessages]
                .sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
                
            console.log(`Loaded ${messages.length} messages (${newMessages.length} new) for conversation ${conversationId}`);
            
            // Mark messages as read if needed
            if (conversations.value[convIndex].unread_count > 0) {
                console.log(`Marking ${conversations.value[convIndex].unread_count} messages as read`);
                await markAsRead(conversationId);
                // Reset unread count after marking as read
                conversations.value[convIndex].unread_count = 0;
            }
        }
        
        return messages;
        
    } catch (err) {
        console.error('Failed to load messages:', {
            error: err,
            response: err.response?.data,
            conversationId
        });
        
        error.value = 'Could not load messages for this conversation. ' + 
            (err.response?.data?.message || err.message || 'Please try again.');
            
        // Show error to user
        if (window.toast) {
            window.toast.error(error.value);
        }
        
        throw err; // Re-throw to allow caller to handle the error
    } finally {
        isLoadingMessages.value = false;
    }
};

const selectConversation = async (conversation) => {
    console.log('selectConversation called with:', { 
        id: conversation?.id, 
        type: typeof conversation,
        currentId: currentConversation.value?.id 
    });
    
    if (!conversation || !conversation.id) {
        const errorMsg = 'Invalid conversation object: ' + JSON.stringify(conversation);
        console.error(errorMsg);
        if (window.toast) window.toast.error('Invalid conversation');
        return;
    }
    
    // Don't do anything if we're already on this conversation
    if (currentConversation.value?.id === conversation.id) {
        console.log('Already on conversation:', conversation.id);
        return;
    }
    
    console.log('Selecting conversation:', conversation.id);
    
    try {
        // Show loading state
        isLoading.value = true;
        error.value = null;
        
        // Leave current conversation channel if we're in one
        if (currentConversation.value?.id && echo.value) {
            console.log('Leaving channel for conversation:', currentConversation.value.id);
            await leaveConversationChannel(currentConversation.value.id);
        }
        
        // Update current conversation reference
        console.log('Setting current conversation to:', conversation.id);
        currentConversation.value = conversation;
        
        // Reset UI states
        showNewMessageNotification.value = false;
        
        // Join the new conversation channel
        if (echo.value) {
            console.log('Joining channel for conversation:', conversation.id);
            await joinConversationChannel(conversation.id);
        } else {
            console.warn('Echo is not initialized');
        }
        
        // Load messages for the selected conversation
        console.log('Loading messages for conversation:', conversation.id);
        await loadMessages(conversation.id);
        
        // Mark conversation as read
        try {
            console.log('Marking conversation as read:', conversation.id);
            await markAsRead(conversation.id);
        } catch (markReadError) {
            console.warn('Failed to mark conversation as read:', markReadError);
            // Don't fail the whole operation if marking as read fails
        }
        
        // Update unread counts
        updateUnreadCounts();
        
        // Force UI update and scroll to bottom
        await nextTick();
        console.log('Scrolling to bottom for conversation:', conversation.id);
        scrollToBottom();
        
        console.log('Successfully selected conversation:', conversation.id);
        
    } catch (err) {
        const errorDetails = {
            error: err.message,
            response: err.response?.data,
            stack: err.stack,
            conversationId: conversation?.id,
            currentConversationId: currentConversation.value?.id
        };
        
        console.error('Error in selectConversation:', errorDetails);
        
        const errorMessage = 'Failed to select conversation. ' + 
            (err.response?.data?.message || err.message || 'Please try again.');
        
        error.value = errorMessage;
        
        // Show error to user
        if (window.toast) {
            window.toast.error(errorMessage);
        }
    } finally {
        console.log('Clearing loading state for conversation:', conversation.id);
        isLoading.value = false;
        
        // Force a re-render to ensure UI is in sync
        await nextTick();
    }
};

const sendTypingEvent = debounce(() => {
    if (currentConversation.value && echo.value) {
        echo.value.private(`conversation.${currentConversation.value.id}`)
            .whisper('typing', { user: currentUser.value });
    }
}, 300);

const sendMessage = async (content, attachments = [], type = 'text') => {
    // Ensure content is a string before calling trim
    const safeContent = typeof content === 'string' ? content : String(content || '');
    
    // For audio messages, we might not have text content, but we should have attachments
    const hasContent = safeContent.trim().length > 0;
    const hasAttachments = attachments && attachments.length > 0;
    
    if ((!hasContent && !hasAttachments) || !currentConversation.value) {
        console.error('Cannot send message: No content/attachments or no conversation selected');
        console.log('Content:', content);
        console.log('Attachments:', attachments);
        console.log('Current conversation:', currentConversation.value);
        return;
    }
    
    isSending.value = true;
    const messageContent = safeContent.trim();
    const conversationId = currentConversation.value.id;
    
    console.log('=== Sending message ===');
    console.log('Conversation ID:', conversationId);
    console.log('Message type:', type);
    console.log('Message content:', messageContent);
    console.log('Attachments:', attachments);
    
    // Generate a unique temporary ID for the message
    const tempMessageId = `temp-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
    
    // Optimistically add the message to the UI
    const tempMessage = {
        id: tempMessageId,
        body: messageContent,
        type: type,
        user_id: currentUser.value.id,
        conversation_id: conversationId,
        created_at: new Date().toISOString(),
        status: 'sending',
        user: {
            id: currentUser.value.id,
            name: currentUser.value.name,
            avatar: currentUser.value.avatar
        },
        // Include attachments in the temp message if any
        ...(hasAttachments && { attachments: attachments })
    };
    
    console.log('Temporary message created:', tempMessage);
    
    try {
        // Add the temporary message to the conversation
        console.log('Adding temporary message to conversation');
        addMessageToConversation(tempMessage);
        
        // Scroll to bottom to show the new message
        nextTick(() => {
            scrollToBottom();
        });
        
        // Prepare the request data
        let requestData;
        let config = {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            withCredentials: true,
            timeout: 30000 // 30 second timeout for file uploads
        };

        // Always use FormData for consistency
        const formData = new FormData();
        
        // Always include the message body, even if empty
        formData.append('body', messageContent || '');
        formData.append('type', type);
        formData.append('temp_message_id', tempMessageId);
        
        // Add attachments if present
        if (hasAttachments) {
            attachments.forEach((file, index) => {
                formData.append(`attachments[${index}]`, file);
            });
        }
        
        requestData = formData;
        
        // Remove Content-Type header to let the browser set it with the correct boundary
        delete config.headers['Content-Type'];
        
        console.log('Sending message to server...');
        const response = await axios.post(
            `/api/conversations/${conversationId}/messages`,
            requestData,
            config
        );
        
        console.log('=== Server response ===');
        console.log('Status:', response.status);
        console.log('Response data:', response.data);
        
        if (response.status >= 200 && response.status < 300 && response.data?.data) {
            const serverMessage = response.data.data;
            console.log('Server message received:', serverMessage);
            
            // Update the message status to delivered
            updateMessageStatus(tempMessageId, {
                ...serverMessage,
                status: 'delivered',
                // Ensure user data is properly set
                user: serverMessage.user || tempMessage.user
            });
            
            // Update the conversation's last message
            updateConversationLastMessage(conversationId, serverMessage);
        } else {
            console.warn('Unexpected server response format:', response);
            // Update status to indicate error
            updateMessageStatus(tempMessageId, {
                status: 'error',
                error: 'Unexpected server response'
            });
        }
    } catch (error) {
        console.error('=== Error sending message ===');
        console.error('Error object:', error);
        
        if (error.response) {
            console.error('Response status:', error.response.status);
            console.error('Response data:', error.response.data);
            console.error('Response headers:', error.response.headers);
        }
        
        // Update the message to show error state
        updateMessageStatus(tempMessageId, {
            status: 'error',
            error: error.response?.data?.message || error.message
        });
        
        // If it's a 401 Unauthorized, we might need to refresh the CSRF token
        if (error.response?.status === 401) {
            console.warn('Authentication error - may need to refresh CSRF token');
            await refreshCsrfToken();
        }
    } finally {
        // Update the current conversation reference
        if (currentConversation.value) {
            console.log('Updating current conversation reference');
            currentConversation.value = { ...currentConversation.value };
        }
        
        console.log('Final conversation state:', JSON.parse(JSON.stringify(currentConversation.value)));
        
        // Notify other participants that we've stopped typing
        if (echo.value && currentConversation.value?.id) {
            console.log('Sending stop-typing event');
            echo.value.private(`conversation.${currentConversation.value.id}`)
                .whisper('stop-typing', { user: currentUser.value });
        }
        
        isSending.value = false;
    }
    }


const setupAudioRecorder = async () => {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        audioRecorder.value = new MediaRecorder(stream);
        audioRecorder.value.ondataavailable = (event) => {
            audioChunks.value.push(event.data);
        };
        audioRecorder.value.onstop = () => {
            const audioBlob = new Blob(audioChunks.value, { type: 'audio/webm' });
            sendAudioMessage(audioBlob);
            audioChunks.value = [];
        };
    } catch (err) {
        console.error('Audio recording setup failed:', err);
        error.value = 'Audio recording is not available or permission was denied.';
    }
};

const startRecording = () => {
    if (audioRecorder.value) {
        audioChunks.value = [];
        audioRecorder.value.start();
        isRecording.value = true;
        recordingTime.value = 0;
        recordingTimer.value = setInterval(() => {
            recordingTime.value++;
        }, 1000);
    }
};

const stopRecording = () => {
    if (audioRecorder.value && isRecording.value) {
        audioRecorder.value.stop();
        isRecording.value = false;
        clearInterval(recordingTimer.value);
    }
};

const sendAudioMessage = async (audioBlob) => {
    if (!currentConversation.value) return;
    isSending.value = true;
    const formData = new FormData();
    formData.append('conversation_id', currentConversation.value.id);
    formData.append('audio', audioBlob, 'recording.webm');

    try {
        const response = await axios.post(route('chat.messages.store'), formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        addMessageToConversation(response.data);
    } catch (err) {
        console.error('Failed to send audio message:', err);
        error.value = 'Could not send audio message.';
    } finally {
        isSending.value = false;
    }
};

const loadConversations = async () => {
    // If conversations already populated (inertia initial props) skip API request
    if (conversations.value.length > 0) {
        return;
    }
    try {
        const response = await axios.get('/api/conversations');
        let convArray;
        if (Array.isArray(response.data?.data)) {
            convArray = response.data.data;
        } else if (Array.isArray(response.data)) {
            convArray = response.data;
        } else {
            convArray = [response.data];
        }
        conversations.value = convArray.filter(Boolean).map(c => ({ ...c, messages: c.messages || [] }));
        if (conversations.value.length > 0) {
            selectConversation(conversations.value[0]);
        }
    } catch (err) {
        console.error('Failed to load conversations:', err);
        error.value = 'Could not load conversations.';
    }
};

const initializeEcho = () => {
    isConnecting.value = true;
    connectionError.value = null;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const ziggy = usePage().props.ziggy || {};
    const pusherCfg = props.initialData?.pusher || { 
        key: ziggy?.pusher?.key || import.meta.env.VITE_PUSHER_APP_KEY, 
        cluster: ziggy?.pusher?.cluster || import.meta.env.VITE_PUSHER_APP_CLUSTER 
    };

    if (!pusherCfg.key) {
        console.error('Pusher app key is missing');
        connectionError.value = 'Pusher configuration error. Please check your environment variables.';
        return;
    }

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
        
        // Join presence channel for online users
        echo.value.join('online')
            .here((users) => {
                console.log('Online users:', users);
                onlineUsers.value = new Set(users.map(u => u.id));
            })
            .joining((user) => {
                console.log('User joined:', user);
                onlineUsers.value.add(user.id);
            })
            .leaving((user) => {
                console.log('User left:', user);
                onlineUsers.value.delete(user.id);
            })
            .error((error) => {
                console.error('Error joining presence channel:', error);
            });
    });

    // Connection error
    pusher.connection.bind('error', (err) => {
        console.error('Pusher connection error:', err);
        isConnected.value = false;
        isConnecting.value = false;
        connectionError.value = err.error?.data?.message || 'Connection error. Trying to reconnect...';
        
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
        
        // Attempt to reconnect
        setTimeout(() => {
            if (!isConnected.value) {
                console.log('Attempting to reconnect to Pusher...');
                pusher.connect();
            }
        }, 1000);
    });

    // Subscribe to private channel for user notifications
    try {
        const privateChannel = echo.value.private(`App.Models.User.${currentUser.value.id}`);
        
        // Listen for new message notifications
        privateChannel.notification((notification) => {
            console.log('Received notification:', notification);
            if (notification.type === 'App\\\\Notifications\\\\NewMessageNotification') {
                handleNewMessage(notification.message);
            }
        });
        
        // Listen for subscription success
        privateChannel.subscribed(() => {
            console.log('Subscribed to private channel');
        }).error((error) => {
            console.error('Error subscribing to private channel:', error);
        });
        
    } catch (error) {
        console.error('Error setting up private channel:', error);
    }
    
    // Add global error handler
    window.onPusherError = (error) => {
        console.error('Global Pusher error:', error);
    };
};

const cleanup = () => {
    if (echo.value) {
        echo.value.disconnect();
    }
    if (recordingTimer.value) {
        clearInterval(recordingTimer.value);
    }
};

const initializeApp = async () => {
    isLoading.value = true;
    error.value = null;
    try {
        initializeEcho();
        await loadConversations();
        await setupAudioRecorder();
        // If still no conversation selected, auto-select first
        if (conversations.value.length > 0 && !currentConversationId.value) {
            currentConversationId.value = conversations.value[0].id;
        }
        if (Notification.permission !== 'granted') {
            await Notification.requestPermission();
        }
    } catch (err) {
        console.error('Initialization error:', err);
        error.value = 'Failed to initialize chat. Please refresh the page.';
    } finally {
        isLoading.value = false;
    }
};

// Fired when user clicks the "Start new chat" button in ChatContainer
const openNewChatModal = () => {
    showUserPicker.value = true;
};

// Update unread message counts across conversations
const updateUnreadCounts = () => {
    unreadCount.value = conversations.value.reduce((total, conv) => {
        return total + (conv.unread_count || 0);
    }, 0);    
};

// Helper function to update the last message in a conversation
const updateConversationLastMessage = (conversationId, message) => {
    console.log(`Updating last message for conversation ${conversationId}:`, message);
    
    if (!conversationId || !message) {
        console.error('Cannot update conversation last message: missing conversationId or message');
        return false;
    }
    
    // Find the conversation
    const conversationIndex = conversations.value.findIndex(conv => conv.id === conversationId);
    if (conversationIndex === -1) {
        console.error(`Conversation ${conversationId} not found`);
        return false;
    }
    
    // Create updated conversation with new last message
    const updatedConversation = {
        ...conversations.value[conversationIndex],
        last_message: {
            id: message.id,
            content: message.content,
            created_at: message.created_at,
            user_id: message.user_id,
            user: message.user || {
                id: message.user_id,
                name: message.user_name || 'Unknown User'
            }
        },
        last_message_at: message.created_at || new Date().toISOString(),
        updated_at: new Date().toISOString()
    };
    
    // Update the conversation in the array
    conversations.value[conversationIndex] = updatedConversation;
    
    // If this is the current conversation, update the reference
    if (currentConversation.value?.id === conversationId) {
        currentConversation.value = { ...updatedConversation };
    }
    
    // Force reactivity update
    conversations.value = [...conversations.value];
    
    console.log(`Conversation ${conversationId} last message updated successfully`);
    return true;
};

// Helper function to update message status in a conversation
const updateMessageStatus = (messageId, updates) => {
    console.log(`Updating message ${messageId} with:`, updates);
    
    if (!messageId) {
        console.error('Cannot update message status: missing messageId');
        return false;
    }
    
    // Find the conversation containing this message
    const conversation = conversations.value.find(conv => 
        conv.messages?.some(msg => msg.id === messageId || msg.temp_id === messageId)
    );
    
    if (!conversation?.messages) {
        console.error(`Cannot find conversation containing message ${messageId}`);
        return false;
    }
    
    // Find and update the message
    const messageIndex = conversation.messages.findIndex(
        msg => msg.id === messageId || msg.temp_id === messageId
    );
    
    if (messageIndex === -1) {
        console.error(`Message ${messageId} not found in conversation ${conversation.id}`);
        return false;
    }
    
    // Create an updated message object
    const updatedMessage = {
        ...conversation.messages[messageIndex],
        ...updates,
        // Preserve the original ID if we're updating a temporary message
        id: conversation.messages[messageIndex].id || updates.id || messageId,
        updated_at: updates.updated_at || new Date().toISOString()
    };
    
    // Update the message in the array
    conversation.messages[messageIndex] = updatedMessage;
    
    // If this is the last message, update the conversation's last message
    if (messageIndex === conversation.messages.length - 1) {
        updateConversationLastMessage(conversation.id, updatedMessage);
    }
    
    // Force reactivity update
    conversation.messages = [...conversation.messages];
    
    console.log(`Message ${messageId} updated successfully`);
    return true;
};

// Helper function to normalize participant data from different response formats
const normalizeParticipant = (participant) => {
    if (!participant) return null;
    
    // Handle different response formats
    if (participant.attributes) {
        return {
            id: participant.id,
            ...participant.attributes
        };
    }
    
    return participant;
};

// Helper function to normalize conversation data from different response formats
const normalizeConversation = (conv) => {
    if (!conv) return null;
    
    // Extract participants from different possible locations in the response
    const participantsData = conv.relationships?.participants?.data || 
                           conv.relationships?.participants || 
                           conv.participants || [];
    
    // Normalize each participant
    const participants = Array.isArray(participantsData) 
        ? participantsData.map(normalizeParticipant).filter(Boolean)
        : [];
    
    // Find the other participant for 1:1 chats
    const otherParticipant = participants.find(p => p.id !== props.auth.user.id) || null;
    
    return {
        id: conv.id,
        type: conv.type || 'private',
        created_at: conv.attributes?.created_at || conv.created_at,
        updated_at: conv.attributes?.updated_at || conv.updated_at,
        unread_count: conv.unread_count || 0,
        participants,
        other_participant: otherParticipant,
        messages: [] // Start with empty messages, they'll be loaded when selected
    };
};

// Called when a user is chosen in the picker modal
const handleUserSelected = async (user) => {
    console.log('handleUserSelected called with user:', user);
    
    try {
        isSending.value = true;
        error.value = null;
        showUserPicker.value = false;
        
        if (!user || !user.id) {
            throw new Error('Invalid user selected');
        }
        
        // Check if we already have a conversation with this user
        const existingConv = conversations.value.find(conv => {
            // Check direct participant match
            const hasParticipant = conv.participants?.some(p => p.id === user.id);
            
            // Check relationships match
            const hasRelationship = conv.relationships?.participants?.some(
                p => (p.id || p.attributes?.id) === user.id
            );
            
            return hasParticipant || hasRelationship;
        });
        
        if (existingConv) {
            console.log('Found existing conversation:', existingConv.id);
            await selectConversation(existingConv);
            return;
        }
        
        console.log('Creating new conversation with participant:', user.id);
        
        // Create new conversation
        const response = await axios.post('/api/conversations', {
            participants: [user.id],
            type: 'private'
        }, {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': document.cookie.match(/XSRF-TOKEN=([^;]+)/)?.[1] || ''
            },
            withCredentials: true,
            validateStatus: status => status >= 200 && status < 500
        });
        
        console.log('API Response:', response);
        
        if (response.status >= 400) {
            const errorMsg = response.data?.message || 'Failed to create conversation';
            console.error('API Error:', response.status, response.data);
            throw new Error(errorMsg);
        }
        
        // Handle both wrapped and unwrapped responses
        const responseData = response?.data;
        const conv = responseData?.data || responseData;
        
        if (!conv) {
            console.error('No conversation data in response:', response);
            throw new Error('No conversation data received from server');
        }
        
        console.log('Processed conversation data:', conv);
        
        // Normalize the conversation data
        const normalizedConv = normalizeConversation(conv);
        
        if (!normalizedConv) {
            console.error('Failed to normalize conversation data:', conv);
            throw new Error('Failed to normalize conversation data');
        }
        
        console.log('Normalized conversation:', normalizedConv);
        
        // Add to the beginning of the conversations list
        conversations.value.unshift(normalizedConv);
        
        // Select the new conversation
        console.log('Attempting to select conversation:', normalizedConv.id);
        await selectConversation(normalizedConv);
        
        console.log('Conversation created and selected successfully');
        
        // Force update the UI by triggering a re-render
        await nextTick();
        
        // Show success message
        if (window.toast) {
            window.toast.success('Conversation created successfully');
        }
        
    } catch (err) {
        console.error('Failed to create/select conversation', {
            error: err,
            response: err.response?.data,
            stack: err.stack
        });
        
        const errorMessage = err.response?.data?.message || 
                           err.message || 
                           'An unexpected error occurred while creating the conversation';
        
        error.value = errorMessage;
        
        // Show error to user
        if (window.toast) {
            window.toast.error(errorMessage);
        }
        
        // Re-throw to allow parent components to handle the error if needed
        throw err;
    } finally {
        isSending.value = false;
    }
};

// Watch for changes to the current conversation
watch(currentConversation, (newVal, oldVal) => {
    console.log('=== Current Conversation Changed ===');
    console.log('From:', oldVal ? { id: oldVal.id, title: oldVal.title } : 'null');
    console.log('To:', newVal ? { 
        id: newVal.id, 
        title: newVal.title,
        messagesCount: newVal.messages?.length || 0,
        lastMessage: newVal.last_message ? { 
            id: newVal.last_message.id,
            body: newVal.last_message.body?.substring(0, 30) + '...' 
        } : null
    } : 'null');
    
    if (newVal?.id !== oldVal?.id) {
        console.log('Conversation ID changed, loading messages...');
        if (newVal?.id) {
            loadMessages(newVal.id);
        }
    }
}, { deep: true });

// Watch for changes to the conversations array
watch(conversations, (newVal) => {
    console.log('=== Conversations Updated ===');
    console.log('Total conversations:', newVal.length);
    console.log('Conversations:', newVal.map(c => ({
        id: c.id,
        title: c.title || 'No title',
        messagesCount: c.messages?.length || 0,
        lastMessage: c.last_message ? {
            id: c.last_message.id,
            body: c.last_message.body?.substring(0, 30) + '...'
        } : null
    })));
}, { deep: true });

onMounted(() => {
  const waitForConnector = () => {
    if (window.Echo?.connector?.pusher) {
      // safe to use pusher here
      const { pusher } = window.Echo.connector;
      pusher.connection.bind('state_change', ({ previous, current }) => {
        console.log('Pusher state', previous, '→', current);
      });
      
      // Log all Pusher events for debugging
      window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('Pusher connected successfully');
      });
      
      window.Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('Pusher error:', err);
      });
    } else {
      // retry in 100 ms until Echo finished initialising
      setTimeout(waitForConnector, 100);
    }
  };

  waitForConnector();
  // Now initialize the main chat app (fetch conversations, etc.)
  initializeApp();
  
  // Log initial state
  console.log('=== Initial Chat State ===');
  console.log('Current User:', currentUser.value);
  console.log('Current Conversation:', currentConversation.value);
  console.log('Conversations:', conversations.value);
});

onBeforeUnmount(() => {
    cleanup();
});

watch(currentConversationId, (newId, oldId) => {
    if (oldId) {
        leaveConversationChannel(oldId);
    }
    if (newId) {
        joinConversationChannel(newId);
        // markAsRead(newId)
    }
});

</script>

<template>
  <div class="chat-page-container">
    <!-- Connection status indicator -->
    <div v-if="!isConnected" class="connection-status-bar">
      <div class="flex justify-center items-center space-x-2">
        <div class="connection-spinner"></div>
        <span class="text-sm font-medium">Connecting to chat server...</span>
      </div>
    </div>

    <!-- Loading overlay -->
    <div v-if="isLoading" class="loading-overlay">
      <div class="loading-card">
        <div class="flex items-center space-x-4">
          <div class="loading-spinner"></div>
          <div>
            <div class="text-lg font-semibold text-gray-800 mb-1">Loading Chat</div>
            <div class="text-sm text-gray-600">Preparing your conversations...</div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Error message -->
    <div v-if="error" class="error-notification" role="alert">
      <div class="error-card">
        <div class="flex justify-between items-start">
          <div class="flex items-start space-x-3">
            <div class="error-icon">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <div>
              <p class="font-semibold text-red-800">Connection Error</p>
              <p class="text-red-700 text-sm mt-1">{{ error }}</p>
            </div>
          </div>
          <button @click="error = null" class="error-close-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
          </button>
        </div>
        <div class="mt-4">
          <button @click="handleRetry" class="retry-btn">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Retry Connection
          </button>
        </div>
      </div>
    </div>
    
    <!-- Main chat interface -->
    <div v-else class="chat-main-container">
      <ChatContainer
        ref="chatContainer"
        :conversations="conversations"
        :current-conversation="currentConversation"
        :current-user="currentUser"
        :is-typing="isTyping"
        :is-sending="isSending"
        :is-recording="isRecording"
        :connection-status="connectionStatus"
        :messages="currentConversation?.messages || []"
        @select-conversation="selectConversation"
        @send-message="sendMessage"
        @start-audio-recording="startAudioRecording"
        @stop-audio-recording="stopAudioRecording"
        @typing-started="handleTypingStarted"
        @typing-stopped="handleTypingStopped"
        @load-more-messages="loadMoreMessages"
        @message-seen="handleMessageSeen"
        @message-delivered="handleMessageDelivered"
        @create-conversation="openNewChatModal"
      />
    </div>
    
    <!-- Audio recorder indicator -->
    <div v-if="isRecording" class="recording-indicator">
      <div class="recording-pulse"></div>
      <div class="recording-content">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M7 4a3 3 0 016 0v4a3 3 0 11-6 0V4zm4 10.93A7.001 7.001 0 0017 8a1 1 0 10-2 0A5 5 0 015 8a1 1 0 00-2 0 7.001 7.001 0 006 6.93V17H6a1 1 0 100 2h8a1 1 0 100-2h-3v-2.07z" clip-rule="evenodd" />
        </svg>
        <span class="font-medium">Recording</span>
        <span class="recording-time">{{ formatRecordingTime(recordingTime) }}</span>
      </div>
    </div>
    
    <!-- New message notification -->
    <transition
      enter-active-class="transform ease-out duration-300 transition"
      enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
      enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
      leave-active-class="transition ease-in duration-100"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div 
        v-if="showNewMessageNotification" 
        @click="scrollToNewMessage"
        class="new-message-notification"
      >
        <div class="notification-content">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
          </svg>
          <span class="font-medium">New message{{ unreadCount > 1 ? 's' : '' }}</span>
          <span class="notification-badge">{{ unreadCount }}</span>
        </div>
      </div>
    </transition>

    <!-- User Picker Modal -->
    <UserPickerModal
      :show="showUserPicker"
      @close="showUserPicker = false"
      @user-selected="handleUserSelected"
    />
  </div>
</template>

<style scoped>
/* Chat Page Container */
.chat-page-container {
  @apply h-screen;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%);
  position: relative;
  overflow: hidden;
}

.chat-page-container::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image: 
    radial-gradient(circle at 25% 25%, rgba(60, 164, 76, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 75% 75%, rgba(30, 58, 46, 0.1) 0%, transparent 50%);
  pointer-events: none;
  z-index: 0;
}

/* Connection Status Bar */
.connection-status-bar {
  @apply fixed top-0 left-0 right-0 text-white text-center py-3 z-50;
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.connection-spinner {
  @apply w-4 h-4 rounded-full animate-spin;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
}

/* Loading Overlay */
.loading-overlay {
  @apply fixed inset-0 flex items-center justify-center z-40;
  background: rgba(15, 23, 42, 0.8);
  backdrop-filter: blur(8px);
}

.loading-card {
  @apply p-8 rounded-2xl shadow-2xl;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.25),
    0 0 0 1px rgba(255, 255, 255, 0.1);
}

.loading-spinner {
  @apply w-10 h-10 rounded-full animate-spin;
  background: conic-gradient(from 0deg, #3ca44c, #1e3a2e, #3ca44c);
  mask: radial-gradient(circle at center, transparent 30%, black 31%);
  -webkit-mask: radial-gradient(circle at center, transparent 30%, black 31%);
}

/* Error Notification */
.error-notification {
  @apply fixed top-4 right-4 max-w-md z-50;
}

.error-card {
  @apply p-6 rounded-xl shadow-2xl;
  background: rgba(254, 242, 242, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(248, 113, 113, 0.2);
  box-shadow: 
    0 20px 25px -5px rgba(239, 68, 68, 0.1),
    0 10px 10px -5px rgba(239, 68, 68, 0.04);
}

.error-icon {
  @apply w-10 h-10 rounded-full flex items-center justify-center;
  background: linear-gradient(135deg, #fca5a5 0%, #ef4444 100%);
  color: white;
}

.error-close-btn {
  @apply p-2 rounded-full transition-all duration-200;
  color: #dc2626;
}

.error-close-btn:hover {
  @apply bg-red-100;
  transform: scale(1.1);
}

.retry-btn {
  @apply px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
}

.retry-btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 8px -1px rgba(239, 68, 68, 0.4);
}

/* Main Chat Container */
.chat-main-container {
  @apply h-full flex flex-col relative z-10;
}

/* Recording Indicator */
.recording-indicator {
  @apply fixed bottom-6 right-6 z-40;
  background: rgba(239, 68, 68, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 50px;
  padding: 16px 24px;
  box-shadow: 
    0 20px 25px -5px rgba(239, 68, 68, 0.3),
    0 10px 10px -5px rgba(239, 68, 68, 0.1);
  color: white;
  position: relative;
  overflow: hidden;
}

.recording-pulse {
  @apply absolute inset-0 rounded-full animate-ping;
  background: rgba(239, 68, 68, 0.4);
}

.recording-content {
  @apply flex items-center space-x-3 relative z-10;
}

.recording-time {
  @apply font-mono text-sm;
  color: rgba(255, 255, 255, 0.9);
}

/* New Message Notification */
.new-message-notification {
  @apply fixed bottom-6 left-1/2 transform -translate-x-1/2 z-40 cursor-pointer;
  background: rgba(60, 164, 76, 0.95);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  border-radius: 50px;
  padding: 12px 20px;
  box-shadow: 
    0 20px 25px -5px rgba(60, 164, 76, 0.3),
    0 10px 10px -5px rgba(60, 164, 76, 0.1);
  color: white;
  transition: all 0.3s ease;
}

.new-message-notification:hover {
  transform: translate(-50%, -2px);
  box-shadow: 
    0 25px 30px -5px rgba(60, 164, 76, 0.4),
    0 15px 15px -5px rgba(60, 164, 76, 0.15);
}

.notification-content {
  @apply flex items-center space-x-3;
}

.notification-badge {
  @apply w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold;
  background: rgba(255, 255, 255, 0.9);
  color: #3ca44c;
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes slideInRight {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.loading-card {
  animation: fadeInUp 0.5s ease-out;
}

.error-card {
  animation: slideInRight 0.5s ease-out;
}

/* Dark theme support */
@media (prefers-color-scheme: dark) {
  .chat-page-container {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
  }
  
  .loading-card {
    background: rgba(30, 41, 59, 0.95);
    color: #f1f5f9;
  }
  
  .error-card {
    background: rgba(127, 29, 29, 0.95);
  }
}

/* Responsive Design */
@media (max-width: 768px) {
  .error-notification {
    @apply top-2 right-2 left-2 max-w-none;
  }
  
  .new-message-notification {
    @apply bottom-4 left-4 right-4 transform-none;
  }
  
  .recording-indicator {
    @apply bottom-4 right-4;
  }
}
</style>
