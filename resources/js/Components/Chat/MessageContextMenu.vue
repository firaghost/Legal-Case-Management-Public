<template>
  <teleport to="body">
    <transition name="fade">
      <div 
        v-if="visible" 
        ref="menu"
        class="fixed z-[9999] context-menu-container"
        :style="{ 
          top: `${posY}px`, 
          left: `${posX}px`,
          'pointer-events': visible ? 'auto' : 'none'
        }"
      >
        <div 
          class="bg-white dark:bg-gray-800 shadow-xl rounded-lg w-44 py-2 text-sm text-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-700 select-none"
          @click.stop
        >
          <button 
            v-for="item in items" 
            :key="item.action" 
            @click="emitAction(item.action)" 
            class="flex items-center w-full px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 text-left whitespace-nowrap"
          >
            <span class="mr-2 text-base" v-html="item.icon"></span>
            <span>{{ item.label }}</span>
          </button>
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup>
import { defineProps, defineEmits, onMounted, onUnmounted, ref, watch, nextTick } from 'vue';

const props = defineProps({
  visible: Boolean,
  x: Number,
  y: Number,
  message: Object,
});

const emit = defineEmits(['action', 'close']);

// Reactive position
const posX = ref(0);
const posY = ref(0);
const menu = ref(null);

const items = [
  { label: 'Reply', action: 'reply', icon: '&#x21A9;&#xFE0F;' },
  { label: 'Edit', action: 'edit', icon: '&#9998;&#xFE0F;' },
  { label: 'Copy', action: 'copy', icon: '&#128203;' },
  { label: 'Forward', action: 'forward', icon: '&#10145;&#xFE0F;' },
  { label: 'Delete', action: 'delete', icon: '&#128465;&#xFE0F;' },
];

function adjustPosition() {
  nextTick(() => {
    if (!menu.value) return;
    let x = props.x;
    let y = props.y;
    const rect = menu.value.getBoundingClientRect();
    if (rect.right > window.innerWidth) {
      x = Math.max(8, window.innerWidth - rect.width - 8);
    }
    if (rect.bottom > window.innerHeight) {
      y = Math.max(8, window.innerHeight - rect.height - 8);
    }
    posX.value = x;
    posY.value = y;
  });
}

function emitAction(action) {
  emit('action', { action, message: props.message });
  close();
}

function close() {
  emit('close');
}

function handleClickOutside(event) {
  if (props.visible && !event.target.closest('.context-menu-container')) {
    close();
  }
}

watch(() => [props.visible, props.x, props.y], () => {
  if (props.visible) {
    adjustPosition();
  }
});

onMounted(() => {
  posX.value = props.x;
  posY.value = props.y;
  adjustPosition();
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('contextmenu', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('contextmenu', handleClickOutside);
});
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
