<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                داشبورد
                <slot name="breadcrumb"></slot>
            </h2>
        </template>
        <template #contents>
            <main>
                <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                    <div class="px-4 py-6 sm:px-0">
                        <div class="border-4 border-dashed border-gray-200 rounded-lg p-1">
                            <div v-if="slotIsLoaded">
                                <slot name="dashboardContent"></slot>
                            </div>
                            <div v-else>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 m-3">
                                    <div class="">
                                        <div
                                            class="p-1 h-20 bg-gradient-to-l from-teal-400 to-blue-500 text-white text-2xl">
                                            <svg class="inline float-left w-15 h-15"
                                                 viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M19 17V19H7V17S7 13 13 13 19 17 19 17M16 8A3 3 0 1 0 13 11A3 3 0 0 0 16 8M19.2 13.06A5.6 5.6 0 0 1 21 17V19H24V17S24 13.55 19.2 13.06M18 5A2.91 2.91 0 0 0 17.11 5.14A5 5 0 0 1 17.11 10.86A2.91 2.91 0 0 0 18 11A3 3 0 0 0 18 5M8 10H5V7H3V10H0V12H3V15H5V12H8Z"/>
                                            </svg>
                                            <h3 class="text-xl">پرونده های امروز</h3>
                                            <p class="text-3xl pr-5">{{profileStatus.status1 | persianDigit}} پرونده</p>
                                        </div>
                                        <div class="h-5 bg-blue-700 text-left text-white px-3">
                                            <inertia-link :href="route('dashboard.profiles.list',{statusId:1})">
                                                مشاهده لیست
                                            </inertia-link>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div
                                            class="p-1 h-20 bg-gradient-to-l from-green-500 to-green-700 text-white text-2xl">
                                            <svg class="inline float-left w-15 h-15"
                                                 viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M5,3H19A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5A2,2 0 0,1 5,3M15,5V19H19V5H15M5,5V9H13V5H5M5,11V13H7V11H5M8,11V13H10V11H8M11,11V13H13V11H11M5,14V16H7V14H5M8,14V16H10V14H8M11,14V16H13V14H11M11,17V19H13V17H11M8,17V19H10V17H8M5,17V19H7V17H5Z"/>
                                            </svg>
                                            <h3 class="text-xl">در انتظار نصب</h3>
                                            <p class="text-3xl pr-5">{{profileStatus.status7 | persianDigit}} دستگاه</p>
                                        </div>
                                        <div class="h-5 bg-green-700 text-left text-white px-3">
                                            <inertia-link :href="route('dashboard.profiles.list',{statusId:7})">
                                                مشاهده لیست
                                            </inertia-link>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div
                                            class="p-1 h-20 bg-gradient-to-l from-purple-500 to-indigo-700 text-white text-2xl">
                                            <svg class="inline float-left w-15 h-15"
                                                 viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23 7V13H21V7M21 15H23V17H21M12 2A2 2 0 0 0 10 4A2 2 0 0 0 10 4.29C7.12 5.14 5 7.82 5 11V17L3 19V20H21V19L19 17V11C19 7.82 16.88 5.14 14 4.29A2 2 0 0 0 14 4A2 2 0 0 0 12 2M10 21A2 2 0 0 0 12 23A2 2 0 0 0 14 21Z"/>
                                            </svg>
                                            <h3 class="text-xl">در انتظار بررسی</h3>
                                            <p class="text-3xl pr-5">{{profileStatus.status2 | persianDigit}} پرونده</p>
                                        </div>
                                        <div class="h-5 bg-indigo-900 text-left text-white px-3">
                                            <inertia-link :href="route('dashboard.profiles.list',{statusId:2})">
                                                مشاهده لیست
                                            </inertia-link>
                                        </div>
                                    </div>
                                    <div class="">
                                        <div
                                            class="p-1 h-20 bg-gradient-to-l from-red-500 to-red-700 text-white text-2xl">
                                            <svg class="inline float-left w-15 h-15"
                                                 viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M13,13H11V7H13M13,17H11V15H13M12,2A10,10 0 0,0 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"/>
                                            </svg>
                                            <h3 class="text-xl">عدم تایید شاپرک</h3>
                                            <p class="text-3xl pr-5">{{profileStatus.status11 | persianDigit}}
                                                پرونده</p>
                                        </div>
                                        <div class="h-5 bg-red-700 text-left text-white px-3">
                                            <inertia-link :href="route('dashboard.profiles.list',{statusId:11})">
                                                مشاهده لیست
                                            </inertia-link>
                                        </div>
                                    </div>
                                    <!--                                    <div class="col-1 sm:col-span-2 md:col-span-4" aria-hidden="true">-->
                                    <!--                                        <div class="py-5">-->
                                    <!--                                            <div class="border-t border-gray-200"></div>-->
                                    <!--                                        </div>-->
                                    <!--                                    </div>-->
                                    <div class="col-1 sm:col-span-2">
                                        <div>
                                            <div class="bg-pink-600 text-white p-1 text-lg">جدیدترین رخدادها</div>
                                            <div class="h-80 overflow-y-auto">
                                                <div v-for="event in events"
                                                     :class="'border-'+event.color+'-600 bg-'+event.color+'-100'"
                                                     class="my-1 p-3 border-r-4 col-2 sm:col-span-8">
                                                    <inertia-link
                                                        :href="route('dashboard.profiles.view',{id:event.profile_id})">
                                                        <p :class="'text-'+event.color+'-900'">{{event.title}}<span
                                                            class="text-xs text-gray-600">{{event.jDate | persianDigit}} - {{event.profile ? event.profile.customer.fullName : ''}}</span>
                                                        </p>
                                                    </inertia-link>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-if="$page.user.level!=='MARKETER'" class="my-1">
                                            <div class="bg-green-300 text-gray-900 p-1 text-lg">وضعیت انبار</div>
                                            <div class="grid grid-cols-6 gap-2 p-1 text-md md:text-lg">
                                                <div
                                                    class="col-span-3 bg-gradient-to-r from-green-300 to-green-900 text-white text-center py-3">
                                                    سالم: {{devicesStatus.physicalStatus1 | persianDigit}}
                                                </div>
                                                <div
                                                    class="col-span-3 bg-gradient-to-l from-red-200 to-red-600 text-white text-center py-3">
                                                    خراب: {{devicesStatus.physicalStatus2 | persianDigit}}
                                                </div>
                                                <div
                                                    class="col-span-2 bg-gradient-to-r from-green-300 to-green-900 text-white text-center py-3">
                                                    آزاد: {{devicesStatus.transportStatus1 | persianDigit}}
                                                </div>
                                                <div
                                                    class="col-span-2 bg-gradient-to-r from-orange-400 to-yellow-500 text-white text-center py-3">
                                                    در انتظار نصب: {{devicesStatus.transportStatus2 | persianDigit}}
                                                </div>
                                                <div
                                                    class="col-span-2 bg-gradient-to-l from-teal-400 to-blue-600 text-white text-center py-3">
                                                    نصب شده: {{devicesStatus.transportStatus3 | persianDigit}}
                                                </div>
                                                <div class="col-span-3 bg-green-600 text-white text-center py-3">در
                                                    انتظار تخصیص: {{devicesStatus.pspStatus1 | persianDigit}}
                                                </div>
                                                <div
                                                    class="col-span-3 bg-gradient-to-l from-teal-400 to-blue-600 text-white text-center py-3">
                                                    تخصیص
                                                    داده شده: {{devicesStatus.pspStatus2 | persianDigit}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="$page.user.level!=='MARKETER'" class="col-1 sm:col-span-2">
                                        <div>
                                            <div class="bg-green-600 text-white p-1 text-lg">بازاریابان فعال</div>
                                            <bar-chart :chartData="topMarketersChartData"
                                                       :chartOptions="chartOptions"></bar-chart>
                                        </div>
                                        <div>
                                            <div class="bg-purple-600 text-white p-1 text-lg">دستگاه های نصب شده</div>
                                            <pie-chart :chartData="devicesChartData" :chartOptions="chartOptions"></pie-chart>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </template>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout'
    import Welcome from '@/Jetstream/Welcome'
    import BarChart from "@/Pages/Dashboard/Components/Charts/BarChart";
    import PieChart from "@/Pages/Dashboard/Components/Charts/PieChart";
    import VuePersianDigit from 'vue-persian-digit';

    export default {
        components: {
            AppLayout,
            Welcome,
            BarChart,
            PieChart,
            VuePersianDigit,
        },
        props: {
            profileStatus: Object,

            topMarketersChartDatasets: Object,
            topMarketersChartLabels: Array,

            events: Array,

            devicesStatus: Object,

            devicesChartDatasets: Object,
            devicesChartLabels: Array,
        },
        data() {
            return {
                topMarketersChartData: {
                    labels: this.topMarketersChartLabels,
                    datasets: [this.topMarketersChartDatasets]
                },
                devicesChartData: {
                    labels: this.devicesChartLabels,
                    datasets: [this.devicesChartDatasets]
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
                            defaultFontFamily: 'IRANSans',
                        }
                    }
                }
            }
        },
        computed: {
            slotIsLoaded() {
                return this.$slots.dashboardContent;
            }
        }
    }
</script>
