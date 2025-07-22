import ChatContainer from './ChatContainer.vue';
import ConversationList from './ConversationList.vue';
import ConversationHeader from './ConversationHeader.vue';
import MessageList from './MessageList.vue';
import MessageInput from './MessageInput.vue';
import EmojiPicker from './EmojiPicker.vue';

export {
  ChatContainer,
  ConversationList,
  ConversationHeader,
  MessageList,
  MessageInput,
  EmojiPicker,
};

export default {
  install(Vue) {
    Vue.component('ChatContainer', ChatContainer);
    Vue.component('ConversationList', ConversationList);
    Vue.component('ConversationHeader', ConversationHeader);
    Vue.component('MessageList', MessageList);
    Vue.component('MessageInput', MessageInput);
    Vue.component('EmojiPicker', EmojiPicker);
  },
};
