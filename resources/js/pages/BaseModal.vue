<template>
<transition name="modal">
  <div class="modal-mask" v-if="isOpen">
    <div class="modal-wrapper">
      <div class="modal-container">
        <div class="modal-header flex  justify-between mb-4">
          <slot name="header">
            <h4 class="text-lg font-semibold m-0 leading-none">Modal Title</h4>
          </slot>

          <svg
            @click="$emit('close')"
            xmlns="http://www.w3.org/2000/svg"
            class="h-6 w-6 text-gray-500 hover:text-gray-700 cursor-pointer "
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </div>
        <div class="modal-body">
          <slot>
            Default modal content
          </slot>
        </div>

        <div class="modal-footer">
          <slot name="footer"></slot>
        </div>
      </div>
    </div>
  </div>
</transition>
</template>

<script setup>
import { watch } from 'vue';

const props = defineProps({
  isOpen: {
    type: Boolean,
    required: true
  }
});

watch(
  () => props.isOpen,
  (val) => {
    if (val) {
      const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.overflow = 'hidden';
      document.body.style.paddingRight = `${scrollbarWidth}px`;
    } else {
      document.body.style.overflow = '';
      document.body.style.paddingRight = '';
    }
  }
);
</script>



<style scoped>
.modal-mask {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  display: table;
  transition: opacity 0.3s ease;
}

.modal-wrapper {
  display: table-cell;
  vertical-align: middle;
}

.modal-container {
  width: 800px;
  max-height: 80vh;
  margin: 0 auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.33);
  display: flex;
  flex-direction: column; /* allows modal-body to grow and scroll */
}


.modal-header h4 {
  margin-top: 0;
  color: #2d2d2d;
}

.modal-body {
  max-height: 70vh; /* modal body will not exceed 70% of viewport height */
  overflow-y: auto;  /* allow vertical scroll if content is taller */
  padding-right: 10px; /* optional, for scrollbar spacing */
}

.modal-footer {
  display: flex;
  justify-content: center; /* center the buttons */
  gap: 12px;              /* space between buttons */
  padding: 16px;
  border-top: 1px solid #ddd;
  margin-top: 30px;
}

.modal-enter {
  opacity: 0;
}

.modal-leave-active {
  opacity: 0;
}

.modal-enter .modal-container,
.modal-leave-active .modal-container {
  transform: scale(1.1);
}
</style>
