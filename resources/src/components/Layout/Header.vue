<template>
  <header>
    <div
      ref="container"
      class="container header-container"
      :class="{'fixed': isSticked}"
    >
      <div class="is-flex">
        <div class="column is-3 is-narrow-tablet is-narrow-mobile sidebar-title">
          <slot />
          <span
            class="sidebar-toggle is-centered"
            :title="sideBarIconTitle"
            @click="$emit('toggleSidebar')"
          >
            <b-icon
              :icon="sidebarIcon"
            />
          </span>
        </div>
        <div class="column is-9">
          <div class="header-content gapless">
            <div class="column">
              <div class="title">
                <router-link to="/">
                  Minimal CRM
                </router-link>
              </div>
            </div>
            <div class="column is-narrow-desktop is-narrow-tablet is-narrow-mobile is-flex right-navbar">
              <b-dropdown
                v-if="user"
                aria-role="list"
              >
                <div
                  slot="trigger"
                  class="is-flex user-navbar"
                >
                  <!-- <span class="user-navbar_name">
                    {{ user.name }} {{ user.surname }}
                  </span> -->
                  <span class="avatar-container">
                    <b-avatar :src="user.image || ''" />
                  </span>
                </div>
                <b-dropdown-item has-link>
                  <router-link :to="`/users/${user.id}`">
                    <span class="link">
                      Личный кабинет
                    </span>
                  </router-link>
                </b-dropdown-item>
                <b-dropdown-item
                  aria-role="listitem"
                  @click="logout"
                >
                  Выйти
                </b-dropdown-item>
              </b-dropdown>
              <span
                class="action-button"
                @click="$emit('openModulesBar')"
              >
                <b-icon
                  icon="apps"
                  size="is-large"
                />
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<script>
import bAvatar from '@/components/base/avatar'
import { mapState, mapActions } from 'vuex'
import { USER_MODULE } from '@/store/modules/TYPES'
const { USER, LOGOUT } = USER_MODULE

export default {
  components: {
    bAvatar
  },
  props: {
    isSidebarOpened: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      isSticked: false
    }
  },
  computed: {
    ...mapState({
      user: store => store.userModule[USER]
    }),
    sidebarIcon () {
      return this.isSidebarOpened ? 'menu-left' : 'menu-right'
    },
    sideBarIconTitle () {
      return this.isSidebarOpened ? 'Закрыть меню' : 'Открыть меню'
    }
  },
  watch: {
    'isSticked': function () {
      this.$emit('sticked', this.isSticked)
    }
  },
  mounted () {
    window.addEventListener('scroll', () => {
      if (window.innerWidth > 860) return

      if (window.scrollY > 50) {
        this.isSticked = true
      } else {
        this.isSticked = false
      }
    })
  },
  methods: {
    ...mapActions([LOGOUT]),
    logout () {
      this.$router.replace('/login')
      this[LOGOUT]()
    }
  }
}
</script>

<style scoped lang="scss">
  header {
    color: #fff;
    width: 100%;
    .header-container {
      padding: 0;
      background: linear-gradient(154deg,#008fe2 0,#00b29c 100%);
    }
    .header-content {
      justify-content: space-between;
      display: flex;
      align-items: center;
      padding: 15px;
      transition: all 200ms;
    }
    .title {
      color: #fff;
      a {
        color: #fff;
      }
    }
  }
  .link {
    color: #4a4a4a;
  }
  .action-button {
    cursor: pointer;
  }
  .right-navbar {
    align-items: center;
  }
  .avatar-container {
    margin-left: 5px;
    position: relative;
    &::after {
      position: absolute;
      bottom: 1px;
      right: 1px;
      content: '';
      width: 10px;
      height: 10px;
      border-radius: 50%;
      display: block;
      background: #00b057;
      z-index: 2;
    }
  }
  .user-navbar {
    cursor: pointer;
    align-items: center;
    display: flex;
    margin-right: 10px;
  }
  .sidebar-title {
    align-items: center;
    display: flex;
    justify-content: space-between;
    padding-right: 10px;
    background: rgba(255, 255, 255, .1);
    color: #fff;
    text-transform: uppercase;
    font-size: 16px;
    font-weight: 300;
    min-width: 180px;
  }
  .sidebar-toggle {
    cursor: pointer;
    display: inline-block;
    padding: 1px;
    transition: all 200ms;
    border-radius: 3px;
    &:hover {
      background: rgba(255, 255, 255, .2);
    }
  }
  @media screen and (max-width: 860px) {
    header {
      height: 102px;
      position: relative;
      .header-container {
        position: fixed;
        z-index: 12;
        top: 0;
        left: 0;
        right: 0;
        padding: 0;
        &.fixed {
          .header-content {
            padding-top: 0;
            padding-bottom: 0;
          }
        }
      }
    }
  }
  @media screen and (max-width: 590px) {
    .title {
      font-size: 1rem;
    }
  }
  @media screen and (max-width: 450px) {
    .title {
      display: none;
    }
  }
</style>
