<script setup>
import { Head } from '@inertiajs/vue3';
import ChatPage from './ChatPage.vue';
const props = defineProps({
    initialData: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <Head title="Chat" />
    
    <div class="min-h-screen bg-gray-100">
        <ChatPage :initial-data="initialData" :auth="auth" />
    </div>
</template>

<style scoped>
/* Add any component-specific styles here */
</style>
