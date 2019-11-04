<template>
  <div class="pageNotFound">
    <h2>404</h2>
    <p>
      Страница не найдена
      <router-link to="/">
        На главную
      </router-link>
    </p>
  </div>
</template>

<script>
export default {

}
</script>

<style scoped lang="scss">
  .pageNotFound {
    text-align: center;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    h2 {
      font-size: 36px;
    }
  }
</style>
