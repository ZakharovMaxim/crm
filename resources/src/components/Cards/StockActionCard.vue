<template>
  <div
    class="is-flex is-multiline action"
    :class="className"
  >
    <div class="column is-narrow is-narrow-tablet is-narrow-mobile">
      <span class="action-icon">
        <b-icon
          :icon="icon"
          size="is-large"
        />
      </span>
    </div>
    <div class="column">
      <div class="is-flex gapless is-multiline">
        <div class="column is-10 is-10-table is-10-mobile">
          <div class="action-title is-flex">
            <div class="action-name">
              {{ name }} #{{ action.id }}
            </div>
            <div class="action-images">
              <stock-action-images
                :products="action.products"
              />
            </div>
          </div>
          <div class="action-state">
            <span class="action-status">
              {{ status }}
            </span>
            <span class="action-stock"> ({{ action.from_stock ? action.from_stock.name + ' > ': '' }}{{ action.to_stock.name }})</span>
          </div>
          <div class="action-date">
            {{ action.created_at | date }}
          </div>
        </div>
        <div class="column is-narrow is-narrow-tablet is-narrow-mobile">
          {{ action.products.length }} позиций
          <div v-if="action.type === '1'">
            <span class="action-price">{{ totalPrice }}</span> UAH
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="!!action.notice"
      class="column is-12 is-12-tablet is-12-mobile"
    >
      {{ action.notice }}
    </div>
  </div>
</template>

<script>
import StockActionImages from '../stockActionImages'
export default {
  components: { StockActionImages },
  filters: {
    date (dateString) {
      const options = { weekday: 'numberic', year: 'numeric', month: 'short', day: 'numeric' }
      try {
        const date = new Date(dateString)
        return date.toLocaleDateString('en-US', options)
      } catch (e) {
        return dateString
      }
    }
  },
  props: {
    action: {
      type: Object,
      required: true
    }
  },
  computed: {
    name () {
      return this.action.type === '1' ? 'Зачисление' : this.action.type === '2' ? 'Списание' : 'Перемещение'
    },
    status () {
      return !this.action.is_submited ? 'Черновик' : this.action.type === '1' ? 'Зачислено' : this.action.type === '2' ? 'Списано' : 'Перемещено'
    },
    totalPrice () {
      return this.action.products.reduce((acc, item) => acc + item.pivot.purchase_price * item.pivot.count, 0)
    },
    className () {
      let str = this.action.type === '1' ? 'is-enrollment' : this.action.type === '2' ? 'is-writeoff' : 'is-movement'
      str += this.action.is_submited ? ' is-submited' : ''
      return str
    },
    icon () {
      return this.action.type === '3' ? 'arrow-all' : 'arrow-right-bold-box'
    }
  },
  methods: {
  }
}
</script>

<style scoped lang="scss">
  .action {
    transition: all 200ms;
    color: #333;
    display: block;
    align-items: center;
    &:hover {
      color: #000;
      background: #eee;
    }
    .action-icon {
      display: block;
    }
    &.is-enrollment {
      &.is-submited {
        .action-status, .action-icon, .action-price {
          color: #00b057;
        }
      }
      .action-icon {
        transform: rotate(90deg);
      }
    }
    &.is-writeoff {
      &.is-submited {
        .action-status, .action-icon, .action-price {
          color: #ff4e4e;
        }
      }
      .action-icon {
        transform: rotate(-90deg);
      }
    }
    &.is-movement {
      &.is-submited {
        .action-status, .action-icon, .action-price {
          color: #008fe2;
        }
      }
    }
  }
  .action-images {
    margin-left: 15px;
  }
</style>
