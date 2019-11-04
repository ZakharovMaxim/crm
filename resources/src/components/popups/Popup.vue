<template>
  <b-modal
    :active="isActive"
    has-modal-card
    @close="$emit('close')"
  >
    <div class="modal-card">
      <div
        class="modal-card"
        style="width: auto"
      >
        <header class="modal-card-head">
          <p class="modal-card-title">
            <slot name="header" />
          </p>
        </header>
        <section class="modal-card-body">
          <slot name="content" />
        </section>
        <footer class="modal-card-foot">
          <slot name="footer" />
        </footer>
      </div>
    </div>
  </b-modal>
</template>

<script>
export default {
  props: {
    isActive: Boolean
  }
}
</script>

<style scoped>
.modal-card-head {
  background: #008fe2;
}
.modal-card-title {
  color: #fff;
}
</style>
