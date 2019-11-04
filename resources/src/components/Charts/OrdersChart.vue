<template>
  <div>
    <apexchart
      height="300"
      type="bar"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>

<script>
export default {
  props: {
    xaxis: {
      type: Array,
      required: true
    },
    yaxis: {
      type: Array,
      required: true
    }
  },
  data () {
    return {
      chartOptions: {
        chart: {
          id: 'vuechart-example'
        },
        xaxis: {
          categories: this.xaxis
        },
        plotOptions: {
          bar: {
            columnWidth: '50%'
          }
        },
        yaxis: {
          labels: {
            show: false,
            formatter: function (val) {
              return val
            }
          }
        }
      },
      series: [{
        name: 'Количество заказов',
        data: this.yaxis
      }]
    }
  }
}
</script>

<style>

</style>
