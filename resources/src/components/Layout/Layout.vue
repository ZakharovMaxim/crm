<template>
  <div class="h-100">
    <div class="columns is-multiline layout-outer is-gapless">
      <div class="header-outer is-narrow column">
        <Header
          :is-sidebar-opened="isSidebarOpen"
          @openModulesBar="isModulesBarActive = true"
          @toggleSidebar="toggleSidebar"
          @sticked="headerChanged"
        >
          <slot name="sidebar-title" />
        </Header>
      </div>
      <div
        class="main-container column"
        :class="{'closed': !isSidebarOpen}"
      >
        <div class="container h-100">
          <div class="columns gapless h-100">
            <div
              class="column is-3 sidebar-outer h-100"
              :class="{'sticked': isHeaderSticked}"
            >
              <div class="sidebar h-100">
                <div class="sidebar-inner">
                  <slot name="sidebar" />
                </div>
              </div>
            </div>
            <div class="column is-9 content-outer">
              <div class="content-inner">
                <slot name="content" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <transition name="drawer">
      <div
        v-if="isModulesBarActive"
        class="drawer"
      >
        <div
          class="drawer-bg"
          @click.self="isModulesBarActive = false"
        />
        <aside>
          <div
            class="drawer-close"
            @click="isModulesBarActive = false"
          >
            <b-icon
              icon="close-box"
            />
          </div>
          <ModulesList />
        </aside>
      </div>
    </transition>
  </div>
</template>

<script>
/**
 * Main layout, includes left sidebar, right sidebar and content parts
 */
import { mapState, mapActions } from 'vuex'
import { IS_SIDEBAR_OPEN, SET_SIDEBAR_STATE } from '@/store/modules/TYPES'
import Header from './Header'
import ModulesList from '../ModulesList'

export default {
  components: { Header, ModulesList },
  data () {
    return {
      isModulesBarActive: false,
      isHeaderSticked: false
    }
  },
  computed: {
    ...mapState({
      isSidebarOpen: store => store.appModule[IS_SIDEBAR_OPEN]
    })
  },
  created () {
    if (window.innerWidth < 860) {
      this[SET_SIDEBAR_STATE](false)
    }
  },
  methods: {
    ...mapActions([SET_SIDEBAR_STATE]),
    toggleSidebar () {
      this[SET_SIDEBAR_STATE](!this.isSidebarOpen)
    },
    headerChanged (v) {
      this.isHeaderSticked = v
    }
  }
}
</script>

<style scoped lang="scss">
.layout-outer {
  flex-direction: column;
  min-height: 100%;
}
.main-container {
  flex: 1 0 auto;
  width: 100%;
  padding: 0 15px;
  position: relative;
  overflow: hidden;
  .container {
    padding: 15px 15px 0 15px;
  }
  .columns {
    justify-content: flex-end;
  }
}
.content-inner {
  padding: 15px;
}
.drawer-enter-active aside, .drawer-leave-active aside {
  transition: transform .5s;
}
.drawer-enter aside, .drawer-leave-to aside /* .fade-leave-active до версии 2.1.8 */ {
  transform: translateX(100%);
}
.drawer-enter-active .drawer-bg, .drawer-leave-active .drawer-bg {
  transition: opacity .5s;
}
.drawer-enter .drawer-bg, .drawer-leave-to .drawer-bg /* .fade-leave-active до версии 2.1.8 */ {
  opacity: 0;
}
.drawer-close {
  cursor: pointer;
  margin-bottom: 15px;
  display: inline-block;
}
.sidebar {
  background: #fff;
  height: 100%;
  overflow-x: hidden;
  overflow-y: auto;
  &-inner {
    padding: 10px 0;
  }
}
.drawer {
  height: 100%;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 12;
  &-bg {
    background: rgba(0,0,0,.5);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transition: all .2s;
  }
  aside {
    background: #fff;
    position: absolute;
    top: 0;
    right: 0;
    width: 400px;
    max-width: 100%;
    height: 100%;
    transition: all .2s;
    padding: 5px;
    z-index: 1;
    overflow-y: auto;
    overflow-x: hidden;
  }
}
.content-outer {
  padding: 0 15px;
}
.main-container.closed {
  .content-outer {
    width: 100%;
  }
  .sidebar-outer {
    transform: translateX(-100%);
    opacity: 0;
  }
}
.sidebar-outer, .content-outer {
  transition: all 200ms;
}
@media screen and (max-width: 860px) {
  .content-outer {
    width: 100%;
  }
  .sidebar-outer {
    position: fixed;
    top: 102px;
    left: 15px;
    background: #fff;
    z-index: 12;
    height: 100%;
    padding-right: 10px;
    overflow: auto;
    transition: all 200ms;
    padding-top: 0;
    width: auto;
    flex: 1 1 auto;
    &.sticked {
      top: 72px;
    }
  }
}
</style>
