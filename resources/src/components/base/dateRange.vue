<template>
  <div
    class="daterange"
    @click.stop
  >
    <b-datepicker
      :month-names="monthes"
      :day-names="days"
      :value="dateFrom"
      :date-formatter="simpleFormat"
      @input="openSecondPicker"
    />
    <span class="daterange-delimiter" />
    <b-datepicker
      ref="secondPicker"
      :month-names="monthes"
      :day-names="days"
      :value="dateTo"
      :date-formatter="simpleFormat"
      @input="changeDateRange"
    />
  </div>
</template>

<script>
import { monthes, days, simpleFormat } from '@/helpers/DateFormat'

export default {
  props: {
    dateFrom: {
      type: Date,
      default: null
    },
    dateTo: {
      type: Date,
      default: null
    }
  },
  data () {
    return {
      from: this.dateFrom,
      to: this.dateTo,
      monthes,
      days
    }
  },
  watch: {
    'dateFrom': function (v) {
      this.from = v
    },
    'dateTo': function (v) {
      this.changeDateRange(v, true)
    }
  },
  methods: {
    openSecondPicker (v) {
      this.from = v
      this.$refs.secondPicker.toggle()
    },
    simpleFormat,
    changeDateRange (v, changedFromProps) {
      console.log(changedFromProps)
      this.to = v
      this.$emit('input', {
        from: this.from,
        to: this.to,
        changedDirectly: !changedFromProps
      })
    }
  }
}
</script>

<style scoped lang="scss">
.daterange {
  display: flex;
  align-items: center;
  width: 100%;
  &-delimiter {
    display: block;
    margin: 0 5px;
    width: 7px;
    height: 1px;
    background: #333;
  }
}
</style>
