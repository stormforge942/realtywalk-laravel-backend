<template>
  <div class="d-block position-relative">
    <div v-if="isLoading" class="chart-loader">
      <div role="status" aria-hidden="false" aria-label="Loading" class="spinner-grow spinner-grow-lg text-primary"></div>
    </div>

    <doughnut-chart :styles="styles" :chart-data="chartData" :options="options"></doughnut-chart>
  </div>
</template>

<script>
import DoughnutChart from './charts/Doughnut.js'
import { Classic20 } from 'chartjs-plugin-colorschemes/src/colorschemes/colorschemes.tableau'

export default {
  name: "ChartDoughnut",
  components: { DoughnutChart },
  props: {
    fetchUrl: { type: String, required: true },
  },
  data () {
    return {
      isLoading: true,
      chartData: {},
      styles: {
        width: '100%',
        height: '350px',
        position: 'relative'
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          colorschemes: {
            scheme: Classic20
          }
        }
      }
    }
  },
  mounted () {
    this.fillData(this.fetchUrl)
  },
  methods: {
    fillData(url, label) {
      this.isLoading = true

      axios.get(url)
        .then(response => {
          this.isLoading = false

          let data = response.data.data,
              labels = response.data.labels

          this.chartData = {
            labels: labels,
            datasets: [{
              data: data
            }]
          }
        })
    }
  }
}
</script>

<style>
.chart-loader {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}
</style>
