<template>
    <Dashboard>
        <template #breadcrumb> / گزارشات / پرونده ها</template>
        <template #dashboardContent>
            <div class="grid md:grid-cols-8 gap-3">
                <div class="md:col-span-2">
                    <p class="text-lg font-bold text-indigo-500 mr-3 mt-15">سال های فعالیت</p>
                    <jet-button class="mx-3 my-1 bg-red-300 hover:bg-red-500"
                                :class="{'bg-red-400':year===thisYear}"
                                @click.native="selectYear(year)"
                                v-for="(year,index) in years" :key="index">
                        {{ year | persianDigit }}
                    </jet-button>
                    <p class="text-base my-2 mr-3">کل پرونده های ثبت شده</p>
                    <p class="rounded-full bg-green-200 text-green-600 text-center mx-1 my-2 text-lg font-bold">
                        {{ totalProfilesCount | persianDigit }} پرونده</p>
                    <p class="text-base my-2 mr-3">پرونده های ثبت شده در سال {{ thisYear | persianDigit }}</p>
                    <p class="rounded-full bg-red-200 text-red-600 text-center mx-1 my-2 text-lg font-bold">
                        {{ thisYearProfilesCount | persianDigit }} پرونده</p>
                    <p class="text-lg font-bold text-indigo-500 mr-3 mt-15">ماه های فعالیت</p>
                    <jet-button class="mx-3 my-1 bg-green-300 hover:bg-green-500 w-24 text-center"
                                :class="{'bg-green-400':(index+1)===thisMonth}"
                                @click.native="selectMonth(index+1)"
                                v-for="(month,index) in monthLabels" :key="index">
                        {{ month }}
                    </jet-button>
                    <p class="text-base my-2 mr-3">پرونده های ثبت شده در این ماه</p>
                    <p class="rounded-full bg-blue-200 text-blue-600 text-center mx-1 my-2 text-lg font-bold">
                        {{ thisMonthProfilesCount | persianDigit }} پرونده</p>
                    <p class="text-lg font-bold text-indigo-500 mr-3 mt-15">فعالیت نمایندگان</p>
                    <div class="report-info-box">
                        <jet-label for="agent">انتخاب نماینده</jet-label>
                        <select class="form-input w-full block pr-8"
                                ref="agent"
                                name="agent"
                                id="agent"
                                v-model="agent"
                                @change="submitSearchForm">
                            <option v-for="agent in agentsChartData.agents"
                                    :key="agent.id"
                                    :value="agent.id">{{ agent.name }}
                            </option>
                        </select>
                        <jet-button v-if="agent!=0"
                                    class="report-filter-clear-button"
                                    @click.native="clearAgent">همه نماینده ها
                        </jet-button>
                        <p v-if="agent!=0" class="text-base my-2 mr-3 text-center">پرونده های ثبت شده توسط {{ thisAgent.name }}</p>
                        <p v-if="agent!=0" class="rounded-full bg-green-200 text-green-600 text-center mx-1 my-2 text-lg font-bold">
                            {{ thisAgentProfilesCount | persianDigit }} پرونده</p>
                    </div>
                    <div class="report-info-box">
                        <jet-label for="marketer">انتخاب بازاریاب</jet-label>
                        <select class="form-input w-full block pr-8"
                                ref="marketer"
                                name="marketer"
                                id="marketer"
                                v-model="marketer"
                                @change="submitSearchForm">
                            <option v-for="marketer in marketersChartData.marketers"
                                    :key="marketer.id"
                                    :value="marketer.id">{{ marketer.name }}
                            </option>
                        </select>
                        <jet-button v-show="marketer!=0"
                                    class="report-filter-clear-button"
                                    @click.native="clearMarketer">همه بازاریاب ها
                        </jet-button>
                    </div>
                </div>
                <div class="md:col-span-6">
                    <bar-chart :chartData="yearChartData"
                               :chartOptions="chartOptions"/>
                    <bar-chart :chartData="monthChartData"
                               :chartOptions="chartOptions"/>
                    <bar-chart :chartData="agentsChartData"
                               :chartOptions="chartOptions"/>
                    <bar-chart :chartData="marketersChartData"
                               :chartOptions="chartOptions"/>
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
import Dashboard from "@/Pages/Dashboard";
import JetButton from "@/Jetstream/Button";
import JetLabel from "@/Jetstream/Label";
import {Inertia} from "@inertiajs/inertia";
import BarChart from "@/Pages/Dashboard/Components/Charts/BarChart";

export default {
    name: "Profiles",
    components: {
        Dashboard,
        JetButton,
        JetLabel,
        BarChart,
    },
    props: {
        years: Array,
        monthLabels: Array,
        thisYear: Number,
        thisMonth: Number,
        thisYearProfilesCount: Number,
        thisMonthProfilesCount: Number,
        totalProfilesCount: Number,
        thisAgentProfilesCount: Number,
        yearChartData: Object,
        monthChartData: Object,
        agentsChartData: Object,
        thisAgent: Object,
        thisMarketer: Number,
        marketersChartData: Object,
    },
    data() {
        return {
            year: this.thisYear,
            month: this.thisMonth,
            agent: this.thisAgent ? this.thisAgent.id : 0,
            marketer: this.thisMarketer,
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
    methods: {
        selectYear(year) {
            this.year = year;
            this.submitSearchForm();
        },
        selectMonth(month) {
            this.month = month;
            this.submitSearchForm();
        },
        clearAgent() {
            this.agent = 0;
            this.submitSearchForm();
        },
        clearMarketer() {
            this.marketer = 0;
            this.submitSearchForm();
        },
        submitSearchForm() {
            Inertia.visit(route('dashboard.reports.profiles'), {
                method: 'get',
                data: {
                    year: this.year,
                    month: this.month,
                    agent: this.agent,
                    marketer: this.marketer,
                },
            })
        }
    }
}
</script>

<style scoped>
.report-info-box {
    @apply border rounded mx-1 my-2 p-3 border-gray-300
}

.report-filter-clear-button {
    @apply bg-red-500 text-xs my-2 mx-auto
}

.report-filter-clear-button:hover {
    @apply bg-red-400
}
</style>
