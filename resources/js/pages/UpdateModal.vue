<template>
 <transition name="modal">
    <div class="modal-mask" v-if="isOpen">
      <div class="modal-wrapper">
        <div class="modal-container">
          <!-- Header -->
          <div class="modal-header">
            <slot name="header">
                <h2 class="text-lg font-bold mb-4">{{ title }}</h2>
            </slot>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <slot>
              Default modal content
            </slot>
          </div>

          <!-- Footer -->
          <div class="modal-footer">
            <slot name="footer"></slot>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  props: {
    visible: Boolean,
    title: String,
  },
  data() {
    return {
      status: '',
    };
  },
  computed: {
    isOpen() {
      return this.visible;
    }
  },
  methods: {
    saveStatus() {
      this.$emit('save', this.status); // emit to parent
      this.status = '';
    },
    closeModal() {
      this.$emit('close');
    }
  }
};
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
  width: 600px;
  max-height: 80vh;
  margin: 0 auto;
  padding: 20px 30px;
  background-color: #fff;
  border-radius: 6px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.33);
  display: flex;
  flex-direction: column; /* allows modal-body to grow and scroll */
}


.modal-header h3 {
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
