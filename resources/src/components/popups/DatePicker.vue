<template>
  <span>
    <span
      class="calendar-title"
      @click="open"
    >
      <b-icon
        size="is-medium"
        icon="calendar-range"
      />
      <div class="calendar-title_period">
        {{ dateLabel }}
      </div>
    </span>
    <Popup
      :is-active="isActive"
      @close="close"
    >
      <template slot="header">
        Выберите период
      </template>
      <template slot="content">
        <div>
          <ul class="datepicker-select">
            <li @click="setToday">За сегодня</li>
            <li @click="setYestarday">За вчера</li>
            <li @click="setMonth">За этот месяц</li>
            <li @click="setPastMonth">За прошлый месяц</li>
          </ul>
        </div>
        <div>Произвольный период</div>
        <b-daterange
          :date-from="dateFrom"
          :date-to="dateTo"
          @input="input"
        />
      </template>
    </Popup>
  </span>
</template>

<script>
import { setMonth, simpleFormat, monthName, isToday, isYesterday, isMonthPeriod } from '@/helpers/DateFormat'

export default {
  data () {
    return {
      isActive: false,
      dateFrom: this.$route.query.date_from ? new Date(this.$route.query.date_from) : setMonth()[0],
      dateTo: this.$route.query.date_to ? new Date(this.$route.query.date_to) : setMonth()[1]
    }
  },
  computed: {
    dateLabel () {
      const today = new Date()
      if (isToday(this.dateFrom) && isToday(this.dateTo)) {
        return `Сегодня ${simpleFormat(this.dateFrom)}`
      } else if (isYesterday(this.dateFrom) && isYesterday(this.dateTo)) {
        return `Вчера ${simpleFormat(this.dateFrom)}`
      } else if (isMonthPeriod(this.dateFrom, this.dateTo)) {
        return monthName(today.getMonth() - this.dateFrom.getMonth())
      } else {
        return `${simpleFormat(this.dateFrom)} - ${simpleFormat(this.dateTo)}`
      }
    }
  },
  methods: {
    open () {
      this.isActive = true
    },
    close () {
      this.isActive = false
    },
    setToday () {
      this.dateFrom = new Date()
      this.dateTo = new Date()
    },
    setYestarday () {
      const date = new Date()
      date.setDate(date.getDate() - 1)
      this.dateFrom = date
      this.dateTo = date
    },
    setMonth () {
      this.dateFrom = setMonth()[0]
      this.dateTo = setMonth()[1]
    },
    setPastMonth () {
      const today = new Date()
      const firstDate = new Date(today.getFullYear(), today.getMonth() - 1, 1)
      const secondDate = new Date()
      secondDate.setDate(0)
      this.dateFrom = firstDate
      this.dateTo = secondDate
    },
    input (v) {
      this.dateFrom = v.from
      this.dateTo = v.to
      this.close()
      this.$emit('input', v)
    }
  }
}
</script>

<style scoped lang="scss">
.calendar-title {
  display: flex;
  align-items: center;
  font-size: 12px;
  font-weight: bold;
  &_period {
    margin-left: 5px;
    display: inline-block;
  }
}
.datepicker-select {
  li {
    display: block;
    width: 100%;
    padding: 5px 15px;
    background: #eee;
    cursor: pointer;
    transition: all 200ms;
    &:not(:last-child) {
      margin-bottom: 2px;
    }
    &:hover {
      background: #999;
    }
  }
}
</style>
