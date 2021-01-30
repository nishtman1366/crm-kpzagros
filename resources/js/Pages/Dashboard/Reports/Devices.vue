<template>
    <Dashboard>
        <template #breadcrumb> / گزارشات / دستگاه ها</template>
        <template #dashboardContent>
            <div class="grid md:grid-cols-4 gap-3">
                <div class="bg-red-500 rounded text-white p-3">
                    <p class="text-xl mb-3">وضعیت سلامت</p>
                    <p class="inline-block mr-1 float-right">سالم: {{ devicesSummary.physicalStatus[0] | persianDigit }}
                        عدد</p>
                    <p class="inline-block ml-1 float-left">خراب: {{ devicesSummary.physicalStatus[1] | persianDigit }}
                        عدد</p>
                </div>
                <div class="bg-blue-500 rounded text-white p-3 md:col-span-2 ">
                    <p class="text-xl mb-3">وضعیت انبار</p>
                    <div class="flex justify-center">
                        <div class="mx-3">در انبار: {{ devicesSummary.transportStatus[0] | persianDigit
                            }} عدد</div>
                        <div class="mx-3">درانتظار: {{ devicesSummary.transportStatus[1] | persianDigit
                            }} عدد</div>
                        <div class="mx-3">نصب شده: {{ devicesSummary.transportStatus[2] | persianDigit }}
                            عدد</div>
                    </div>

                </div>
                <div class="bg-green-500 rounded text-white p-3">
                    <p class="text-xl mb-3">وضعیت سرویس دهنده</p>
                    <p class="inline-block mr-1 float-right">تخصیص نیافته: {{ devicesSummary.pspStatus[0] | persianDigit }}
                        عدد</p>
                    <p class="inline-block ml-1 float-left">تخصیص یافته: {{ devicesSummary.pspStatus[1] | persianDigit }}
                        عدد</p>
                </div>
                <div class="md:col-span-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th scope="col"
                                class="list-table-header-cell">
                                مدل دستگاه
                            </th>
                            <th scope="col"
                                class="list-table-header-cell">
                                تعداد
                            </th>
                            <th scope="col"
                                class="list-table-header-cell">
                                وضعیت سلامت
                            </th>
                            <th scope="col"
                                class="list-table-header-cell">
                                وضعیت حمل و نقل
                            </th>
                            <th scope="col"
                                class="list-table-header-cell">
                                وضعیت سرویس دهنده
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="type in deviceTypes" :key="type.id">
                            <td class="list-table-body-cell">{{type.name}}</td>
                            <td class="list-table-body-cell">{{type.total | persianDigit}}</td>
                            <td class="list-table-body-cell">
                                <span
                                    class="badge badge-green">{{type.devicePhysicalStatus1 | persianDigit}} سالم</span>
                                <span class="badge badge-red">{{type.devicePhysicalStatus2 | persianDigit}} خراب</span>
                            </td>
                            <td class="list-table-body-cell">
                                <span
                                    class="badge badge-green">{{type.deviceTransportStatus1 | persianDigit}} در انبار</span>
                                <span class="badge badge-yellow">{{type.deviceTransportStatus2 | persianDigit}} درانتظار نصب</span>
                                <span
                                    class="badge badge-blue">{{type.deviceTransportStatus3 | persianDigit}} نصب شده</span>
                            </td>
                            <td class="list-table-body-cell">
                                <span
                                    class="badge badge-red">{{type.devicePspStatus1 | persianDigit}} در انتظار تخصیص</span>
                                <span
                                    class="badge badge-green">{{type.devicePspStatus2 | persianDigit}} تخصیص یافته</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="md:col-span-4">

                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import BarChart from "@/Pages/Dashboard/Components/Charts/BarChart";

    export default {
        name: "Devices",
        components: {Dashboard, BarChart},
        props: {
            devicesSummary: Object,
            deviceModelsChartData: Object
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
        },
        computed: {
            deviceTypes: function () {
                return this.deviceModelsChartData.table;
            }
        }
    }
</script>

<style scoped>
    .list-table-header-cell {
        @apply py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider;
    }

    .list-table-body-cell {
        @apply py-4 text-center text-gray-900;
    }

    .sub-text {
        @apply text-sm text-indigo-600
    }
</style>
