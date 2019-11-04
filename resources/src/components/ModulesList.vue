<template>
  <GetData :callback="fetch">
    <div class="modules-list">
      <div class="modules-list--content is-flex is-multiline">
        <div
          v-for="(item, index) in list"
          :key="index"
          tag="div"
          class="modules-item column is-4 is-4-tablet is-6-mobile"
        >
          <router-link
            :to="item.to"
            class="square"
          >
            <div class="modules-item--content">
              <b-icon
                :icon="item.icon"
                size="is-large"
              />
              <div class="modules-item--name">
                {{ item.name }}
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </GetData>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { MODULES_MODULE } from '@/store/modules/TYPES'
const { GET_MODULES, MODULES } = MODULES_MODULE

export default {
  computed: {
    ...mapState({
      list: store => store.appModule[MODULES]
    })
  },
  methods: {
    ...mapActions([GET_MODULES]),
    fetch () {
      return this[GET_MODULES]()
    }
  }
}
</script>

<style scoped lang="scss">
  .modules-list {
    display: flex;
    justify-content: center;
    &--content {
      width: 650px;
      max-width: 100%;
    }
  }
  .icon {
    color: #c1c1c1;
    transition: color 200ms;
  }
  .modules-item {
    color: #008fe2;
    text-align: center;
    position: relative;
    &--name {
      color: #6d6d6d;
      font-weight: bold;
      font-size: 12px;
    }
    &--content {
      background: #fff;
      box-shadow: 0px 0px 20px rgba(0,0,0,.1);
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      border: 2px solid transparent;
      transition: all 200ms;
      cursor: pointer;
      &:hover {
        border-color: rgba(0,0,0,.1);
        box-shadow: 0px 0px 20px rgba(0,0,0,.2);
        .icon {
          color: #6d6d6d;
        }
      }
    }
  }
</style>
