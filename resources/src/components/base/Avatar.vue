<template>
  <div
    class="avatar"
    @click="$emit('click')"
  >
    <div
      :style="{'backgroundImage': `url(${src || '/boy.svg'})`}"
      class="avatar-content"
    />
  </div>
</template>

<script>
export default {
  props: {
    src: {
      type: String,
      default: '/boy.svg'
    }
  }
}
</script>

<style lang="scss" scoped>
  .avatar {
    width: 100%;
    min-width: 35px;
    &-content {
      width: 100%;
      padding-top: 100%;
      background-size: cover;
      background-repeat: no-repeat;
      background-position: 50% 50%;
      border-radius: 50%;
      background-color: #fff;
    }
  }
</style>
