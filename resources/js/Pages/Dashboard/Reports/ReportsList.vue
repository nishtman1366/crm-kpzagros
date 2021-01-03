<template>
    <Dashboard>
        <template #breadcrumb> / گزارشات</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg p-1">
                            <div class="grid md:grid-cols-4">
                                <div class="col-1 text-2xl">گزارش پرونده های ثبت شده</div>
                                <div class="md:col-span-3">
                                    <inertia-link :href="route('dashboard.reports.list',{date:'today'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            امروز
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'yesterday'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            دیروز
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'week'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            هفته جاری
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'lastWeek'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            هفته گذشته
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'month'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            ماه جاری
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'lastMonth'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            ماه گذشته
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'3month'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            سه ماه جاری
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'last3month'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            سه ماه گذشته
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'6month'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            شش ماه جاری
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'last6month'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            شش ماه گذشته
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'year'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            سال جاری
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list',{date:'lastYear'})">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            سال گذشته
                                        </button>
                                    </inertia-link>
                                    <inertia-link :href="route('dashboard.reports.list')">
                                        <button
                                            class="my-1 mx-1 px-4 py-2 shadow inline-flex bg-indigo-400 rounded text-white-900 border border-indigo-900 hover:bg-indigo-900 hover:text-white">
                                            همه پرونده ها
                                        </button>
                                    </inertia-link>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4">
                                <div class="self-center">
                                    <div class="grid grid-cols-2">
                                        <div v-for="(status,key) in profileLabels" :key="key"
                                             :class="statusColors(key)"
                                             class="my-3 mx-1 p-1 text-center rounded">
                                            {{status}}:{{profileCounts[key]}}
                                        </div>
                                    </div>
                                </div>
                                <div class="md:col-span-3">
                                    <pie-chart :chartData="chartData" :chartOptions="chartOptions"></pie-chart>
                                </div>
                            </div>
                        </div>
                        <div class="my-1 shadow overflow-hidden border-b border-gray-200 sm:rounded-lg p-1">
                            <div class="grid md:grid-cols-4">
                                <div class="col-1 text-2xl">گزارش وضعیت دستگاه ها</div>
                                <div class="md:col-span-3">

                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4">
                                <div class="md:col-span-4">
                                    <bar-chart :chartData="deviceChartData"
                                                 :chartOptions="chartOptions"></bar-chart>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import BarChart from "@/Pages/Dashboard/Components/Charts/BarChart";
    import PieChart from "@/Pages/Dashboard/Components/Charts/PieChart";
    import RadarChart from "@/Pages/Dashboard/Components/Charts/RadarChart";

    export default {
        name: "ReportsList",
        components: {Dashboard, BarChart, RadarChart, PieChart},
        props: {
            backgrounds: Array,

            profileLabels: Array,
            profileCounts: Array,

            deviceTypes: Array,
            deviceTypesChart: Array,
            deviceTypesChartLabels: Array,
        },
        data() {
            return {
                chartData: {
                    labels: this.profileLabels,
                    datasets: [
                        {
                            label: 'دستگاه های موجود در انبار',
                            backgroundColor: ['#662c91', '#737373', '#f15a60', '#7ac36a', '#5a9bd4', '#faa75b', '#9e67ab', '#ce7058', '#d77fb4', '#ee2e2f', '#008c48', '#f47d23'],
                            data: this.profileCounts,
                        }
                    ]
                },
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
                            defaultFontFamily: 'IRANSans'
                        }
                    }
                },

                deviceChartData: {
                    labels: this.deviceTypesChartLabels,
                    datasets: this.deviceTypesChart
                },
            }
        },
        mounted() {

        },
        methods: {
            statusColors(status) {
                switch (status) {
                    case 0:
                        return 'bg-yellow-100 text-yellow-800 border border-yellow-800';
                    case 1:
                        return 'bg-green-100 text-green-800 border border-green-800';
                    case 2:
                        return 'bg-yellow-100 text-yellow-800 border border-yellow-800';
                    case 3:
                        return 'bg-green-100 text-green-800 border border-green-800';
                    case 4:
                        return 'bg-green-100 text-green-800 border border-green-800';
                    case 5:
                        return 'bg-green-100 text-green-800 border border-green-800';
                    case 6:
                        return 'bg-yellow-100 text-yellow-800 border border-yellow-800';
                    case 7:
                        return 'bg-green-100 text-green-800 border border-green-800';
                    case 8:
                        return 'bg-green-100 text-green-800 border border-green-800';
                    case 9:
                        return 'bg-gray-100 text-gray-800 border border-gray-800';
                    case 10:
                        return 'bg-red-100 text-red-800 border border-red-800';
                    case 11:
                        return 'bg-red-100 text-red-800 border border-red-800';
                }
            }
        }
    }
</script>

<style scoped>

</style>
