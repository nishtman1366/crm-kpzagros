<pie-chart :chartdata="chartData" :options="chartOptions"/>

<script>
    import {Pie} from 'vue-chartjs'

    export default {
        name: 'PieChart',
        extends: Pie,
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
