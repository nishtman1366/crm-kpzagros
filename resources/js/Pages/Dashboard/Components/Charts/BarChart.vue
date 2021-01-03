<bar-chart :chartdata="chartData" :options="chartOptions"/>

<script>
    import {Bar} from 'vue-chartjs'

    export default {
        name: 'BarChart',
        extends: Bar,
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
