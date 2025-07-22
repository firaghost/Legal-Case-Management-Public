<template>
  <div class="emoji-picker w-64 h-64 bg-white rounded-lg shadow-lg overflow-hidden flex flex-col">
    <!-- Categories -->
    <div class="flex-shrink-0 px-3 pt-2 flex justify-between border-b border-gray-200">
      <button 
        v-for="category in categories" 
        :key="category.key"
        @click="activeCategory = category.key"
        class="p-2 rounded-md focus:outline-none"
        :class="{ 'text-blue-500 bg-blue-50': activeCategory === category.key, 'text-gray-500 hover:bg-gray-100': activeCategory !== category.key }"
        :aria-label="category.label"
      >
        <span class="text-xl">{{ category.icon }}</span>
      </button>
    </div>
    
    <!-- Search -->
    <div class="p-2 border-b border-gray-200">
      <div class="relative">
        <input
          ref="searchInput"
          v-model="searchQuery"
          type="text"
          placeholder="Search emojis..."
          class="w-full px-3 py-2 text-sm border border-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          @input="handleSearch"
        >
        <div class="absolute right-3 top-2.5 text-gray-400">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
      </div>
    </div>
    
    <!-- Emoji Grid -->
    <div class="flex-1 overflow-y-auto p-2">
      <template v-if="filteredEmojis.length > 0">
        <div 
          v-for="(emoji, index) in filteredEmojis" 
          :key="index"
          @click="selectEmoji(emoji)"
          class="inline-flex items-center justify-center w-8 h-8 rounded-md text-xl hover:bg-gray-100 cursor-pointer"
          :title="emoji.name"
          :aria-label="emoji.name"
        >
          {{ emoji.char }}
        </div>
      </template>
      <div v-else class="flex items-center justify-center h-full text-gray-500">
        No emojis found
      </div>
    </div>
    
    <!-- Footer with emoji preview -->
    <div v-if="!searchQuery" class="p-2 border-t border-gray-200 text-sm text-gray-600">
      {{ activeCategoryLabel }}
    </div>
  </div>
</template>

<script>
// Emoji data - in a real app, you might want to use a proper emoji library
const EMOJIS = [
  // Smileys & Emotion
  { char: '😀', name: 'Grinning Face', category: 'smileys' },
  { char: '😃', name: 'Grinning Face with Big Eyes', category: 'smileys' },
  { char: '😄', name: 'Grinning Face with Smiling Eyes', category: 'smileys' },
  { char: '😁', name: 'Beaming Face with Smiling Eyes', category: 'smileys' },
  { char: '😆', name: 'Grinning Squinting Face', category: 'smileys' },
  { char: '😅', name: 'Grinning Face with Sweat', category: 'smileys' },
  { char: '🤣', name: 'Rolling on the Floor Laughing', category: 'smileys' },
  { char: '😂', name: 'Face with Tears of Joy', category: 'smileys' },
  { char: '🙂', name: 'Slightly Smiling Face', category: 'smileys' },
  { char: '🙃', name: 'Upside-Down Face', category: 'smileys' },
  { char: '😉', name: 'Winking Face', category: 'smileys' },
  { char: '😊', name: 'Smiling Face with Smiling Eyes', category: 'smileys' },
  { char: '😇', name: 'Smiling Face with Halo', category: 'smileys' },
  
  // Gestures and Body Parts
  { char: '👍', name: 'Thumbs Up', category: 'gestures' },
  { char: '👎', name: 'Thumbs Down', category: 'gestures' },
  { char: '👌', name: 'OK Hand', category: 'gestures' },
  { char: '✌️', name: 'Victory Hand', category: 'gestures' },
  { char: '🤞', name: 'Crossed Fingers', category: 'gestures' },
  { char: '🤝', name: 'Handshake', category: 'gestures' },
  { char: '👏', name: 'Clapping Hands', category: 'gestures' },
  { char: '🙌', name: 'Raising Hands', category: 'gestures' },
  { char: '👋', name: 'Waving Hand', category: 'gestures' },
  
  // Hearts
  { char: '❤️', name: 'Red Heart', category: 'hearts' },
  { char: '🧡', name: 'Orange Heart', category: 'hearts' },
  { char: '💛', name: 'Yellow Heart', category: 'hearts' },
  { char: '💚', name: 'Green Heart', category: 'hearts' },
  { char: '💙', name: 'Blue Heart', category: 'hearts' },
  { char: '💜', name: 'Purple Heart', category: 'hearts' },
  { char: '🖤', name: 'Black Heart', category: 'hearts' },
  { char: '🤍', name: 'White Heart', category: 'hearts' },
  { char: '🤎', name: 'Brown Heart', category: 'hearts' },
  { char: '💔', name: 'Broken Heart', category: 'hearts' },
  
  // Objects
  { char: '📱', name: 'Mobile Phone', category: 'objects' },
  { char: '💻', name: 'Laptop', category: 'objects' },
  { char: '⌚', name: 'Watch', category: 'objects' },
  { char: '📷', name: 'Camera', category: 'objects' },
  { char: '🎥', name: 'Movie Camera', category: 'objects' },
  { char: '🎮', name: 'Video Game', category: 'objects' },
  { char: '🎧', name: 'Headphone', category: 'objects' },
  { char: '📚', name: 'Books', category: 'objects' },
  
  // Nature
  { char: '🐶', name: 'Dog Face', category: 'nature' },
  { char: '🐱', name: 'Cat Face', category: 'nature' },
  { char: '🐭', name: 'Mouse Face', category: 'nature' },
  { char: '🐹', name: 'Hamster', category: 'nature' },
  { char: '🐰', name: 'Rabbit Face', category: 'nature' },
  { char: '🦊', name: 'Fox', category: 'nature' },
  { char: '🐻', name: 'Bear', category: 'nature' },
  { char: '🐼', name: 'Panda', category: 'nature' },
  { char: '🐨', name: 'Koala', category: 'nature' },
  { char: '🐯', name: 'Tiger Face', category: 'nature' },
  
  // Food
  { char: '🍎', name: 'Red Apple', category: 'food' },
  { char: '🍐', name: 'Pear', category: 'food' },
  { char: '🍊', name: 'Tangerine', category: 'food' },
  { char: '🍋', name: 'Lemon', category: 'food' },
  { char: '🍌', name: 'Banana', category: 'food' },
  { char: '🍉', name: 'Watermelon', category: 'food' },
  { char: '🍇', name: 'Grapes', category: 'food' },
  { char: '🍓', name: 'Strawberry', category: 'food' },
  
  // Travel & Places
  { char: '🚗', name: 'Car', category: 'travel' },
  { char: '🚕', name: 'Taxi', category: 'travel' },
  { char: '🚲', name: 'Bicycle', category: 'travel' },
  { char: '✈️', name: 'Airplane', category: 'travel' },
  { char: '🚀', name: 'Rocket', category: 'travel' },
  { char: '🏠', name: 'House', category: 'travel' },
  { char: '🏢', name: 'Office Building', category: 'travel' },
  { char: '🏖️', name: 'Beach with Umbrella', category: 'travel' },
];

export default {
  name: 'EmojiPicker',
  
  data() {
    return {
      activeCategory: 'smileys',
      searchQuery: '',
      emojis: EMOJIS,
      categories: [
        { key: 'smileys', label: 'Smileys & Emotion', icon: '😀' },
        { key: 'gestures', label: 'Gestures', icon: '👍' },
        { key: 'hearts', label: 'Hearts', icon: '❤️' },
        { key: 'nature', label: 'Animals & Nature', icon: '🐶' },
        { key: 'food', label: 'Food & Drink', icon: '🍎' },
        { key: 'travel', label: 'Travel & Places', icon: '✈️' },
        { key: 'objects', label: 'Objects', icon: '📱' },
      ],
    };
  },
  
  computed: {
    filteredEmojis() {
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        return this.emojis.filter(emoji => 
          emoji.name.toLowerCase().includes(query)
        );
      }
      
      return this.emojis.filter(emoji => emoji.category === this.activeCategory);
    },
    
    activeCategoryLabel() {
      const category = this.categories.find(c => c.key === this.activeCategory);
      return category ? category.label : '';
    },
  },
  
  mounted() {
    // Focus search input when component is mounted
    this.$nextTick(() => {
      if (this.$refs.searchInput) {
        this.$refs.searchInput.focus();
      }
    });
  },
  
  methods: {
    selectEmoji(emoji) {
      this.$emit('select', emoji.char);
    },
    
    handleSearch() {
      // Reset active category when searching
      if (this.searchQuery) {
        this.activeCategory = '';
      } else {
        this.activeCategory = 'smileys';
      }
    },
  },
};
</script>

<style scoped>
.emoji-picker {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

.emoji-picker::-webkit-scrollbar {
  width: 6px;
  height: 6px;
}

.emoji-picker::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.emoji-picker::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.emoji-picker::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
