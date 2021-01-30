<template>
    <Dashboard>
        <template #breadcrumb> / گزارشات</template>
        <template #dashboardContent>
            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <div class="report-box">
                        <p class="report-box-title">پرونده های ثبت شده</p>
                        <bar-chart :chartData="profilesChartData"
                                   :chartOptions="chartOptions"/>
                        <p class="text-left">
                            <Inertia-link :href="route('dashboard.reports.profiles')">
                                <jet-button class="bg-green-500 hover:bg-green-400">گزارش کامل</jet-button>
                            </Inertia-link>
                        </p>
                    </div>
                    <div class="report-box">
                        <p class="report-box-title">تعمیرات</p>
                        <bar-chart :chartData="repairChartData"
                                   :chartOptions="chartOptions"/>
                        <p class="text-left">
                            <Inertia-link :href="route('dashboard.reports.repairs')">
                                <jet-button class="bg-green-500 hover:bg-green-400">گزارش کامل</jet-button>
                            </Inertia-link>
                        </p>
                    </div>
                </div>
                <div>
                    <div class="report-box">
                        <p class="report-box-title">دستگاه ها</p>
                        <div class="grid sm:grid-cols-2 gap-3">
                            <div>
                                <p class="report-box-subtitle">وضعیت سلامت</p>
                                <pie-chart :chart-data="devicePhysicalStatusChartData" :chart-options="chartOptions"/>
                            </div>
                            <div>
                                <p class="report-box-subtitle">وضعیت انبار</p>
                                <pie-chart :chart-data="deviceTransportStatusChartData" :chart-options="chartOptions"/>
                            </div>
                            <div>
                                <p class="report-box-subtitle">وضعیت سرویس دهنده</p>
                                <pie-chart :chart-data="devicePspStatusChartData" :chart-options="chartOptions"/>
                            </div>
                        </div>
                        <p class="text-left">
                            <Inertia-link :href="route('dashboard.reports.devices')">
                                <jet-button class="bg-green-500 hover:bg-green-400">گزارش کامل</jet-button>
                            </Inertia-link>
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
import Dashboard from "@/Pages/Dashboard";
import BarChart from "@/Pages/Dashboard/Components/Charts/BarChart";
import RadarChart from "@/Pages/Dashboard/Components/Charts/RadarChart";
import PieChart from "@/Pages/Dashboard/Components/Charts/PieChart";
import JetButton from "@/Jetstream/Button"

export default {
    name: "Main",
    components: {PieChart, BarChart, RadarChart, Dashboard, JetButton},
    props: {
        profilesChartData: Object,
        repairChartData: Object,
        deviceChartData: Object
    },
    data() {
        return {
            devicePhysicalStatusChartData: this.deviceChartData.physicalStatus,
            deviceTransportStatusChartData: this.deviceChartData.transportStatus,
            devicePspStatusChartData: this.deviceChartData.pspStatus,
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
    },
}
</script>

<style scoped>
.report-box {
    @apply mx-2 my-2 p-3 rounded border border-gray-300
}

.report-box-title {
    @apply text-lg font-bold text-indigo-600 mr-3 border-b border-gray-300
}

.report-box-subtitle {
    @apply text-base font-bold
}
</style>
