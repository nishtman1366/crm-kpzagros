<radar-chart :chartdata="chartData" :options="chartOptions"/>

<script>
    import {Radar} from 'vue-chartjs'

    export default {
        name: 'RadarChart',
        extends: Radar,
        props: {
            chartData: Object,
            chartOptions: Object
        },
        mounted() {
            this.renderChart(this.chartData, this.chartOptions)
        }
    }
</script>

<style scoped>

</style>
