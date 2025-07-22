<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const emit = defineEmits(['close', 'user-selected']);

const props = defineProps({
  show: {
    type: Boolean,
    default: false,
  },
});

const query = ref('');
const results = ref([]);
const loading = ref(false);
const error = ref(null);

watch(query, async (val) => {
  if (!val) {
    results.value = [];
    return;
  }
  loading.value = true;
  error.value = null;
  try {
    const { data } = await axios.get('/api/users', { params: { search: val } });
    // Expect [{ id, name, email }]
    results.value = data;
  } catch (err) {
    console.error('User search failed', err);
    error.value = 'Search error';
  } finally {
    loading.value = false;
  }
});

const selectUser = (user) => {
  emit('user-selected', user);
};
</script>

<template>
  <div v-if="show" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Start New Chat</h2>
        <button @click="emit('close')" class="text-gray-500 hover:text-gray-700">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 011.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
          </svg>
        </button>
      </div>

      <input
        v-model="query"
        type="text"
        placeholder="Search user by name or email"
        class="w-full border rounded px-3 py-2 mb-4 focus:outline-none focus:ring"
      />

      <div v-if="loading" class="text-center text-sm text-gray-500">Searching...</div>
      <div v-if="error" class="text-red-500 text-sm mb-2">{{ error }}</div>

      <ul>
        <li
          v-for="user in results"
          :key="user.id"
          @click="selectUser(user)"
          class="p-2 hover:bg-gray-100 cursor-pointer rounded"
        >
          <div class="font-medium">{{ user.name }}</div>
          <div class="text-sm text-gray-500">{{ user.email }}</div>
        </li>
      </ul>
    </div>
  </div>
</template>
