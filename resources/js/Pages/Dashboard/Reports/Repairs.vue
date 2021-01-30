<template>
    <Dashboard>
        <template #breadcrumb> / گزارشات / تعمیرات</template>
        <template #dashboardContent>
            <div class="grid md:grid-cols-4 gap-3">
                <div class="rounded border bg-gray-300 p-3" v-for="item in repairStatuses" :key="item.id">
                    <p class="text-xl text-right">{{item.name}}</p>
                    <p class="text-xl text-left">{{item.count | persianDigit}} پرونده</p>
                </div>
                <div class="md:col-span-4">
                    <jet-section-border/>
                </div>
                <div class="md:col-span-4">
                    <bar-chart :chartData="typeChartData"
                               :chartOptions="chartOptions"/>
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import SectionBorder from '@/Jetstream/SectionBorder';
    import BarChart from "@/Pages/Dashboard/Components/Charts/BarChart";

    export default {
        name: "Repairs",
        components: {
            BarChart,
            Dashboard,
            SectionBorder,
        },
        props: {
            repairStatuses: Array,
            typeChartData: Object,
        },
        data() {
            return {
                chartOptions: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{
                            display: true,
                            ticks: {
                                beginAtZero: true,
                                // steps: 10,
                                // stepValue: 2,
                                // max: 100
                            }
                        }]
                    },
                    legend: {
                        labels: {
                            defaultFontFamily: 'IRANSans',
                        }
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
