<template>
    <Dashboard>
        <template #breadcrumb> / گزارشات / پرونده ها</template>
        <template #dashboardContent>
            <div class="grid md:grid-cols-8 gap-3">
                <div class="md:col-span-2">
                    <p class="text-lg font-bold text-indigo-500 mr-3 my-2">سال های فعالیت</p>
                    <jet-button class="mx-3 my-1 bg-red-300 hover:bg-red-500"
                                :class="{'bg-red-400':year===thisYear}"
                                @click.native="selectYear(year)"
                                v-for="(year,index) in years" :key="index">
                        {{year}}
                    </jet-button>
                    <p class="text-base my-2 mr-3">کل پرونده های ثبت شده: <span class="badge badge-green">{{totalProfilesCount}}</span>
                        پرونده</p>
                    <p class="text-base my-2 mr-3">پرونده های ثبت شده در این سال: <span class="badge badge-red">{{thisYearProfilesCount}}</span>
                        پرونده</p>
                    <p class="text-lg font-bold text-indigo-500 mr-3 my-2">ماه های فعالیت</p>
                    <jet-button class="mx-3 my-1 bg-green-300 hover:bg-green-500 w-24 text-center"
                                :class="{'bg-green-400':(index+1)===thisMonth}"
                                @click.native="selectMonth(index+1)"
                                v-for="(month,index) in monthLabels" :key="index">
                        {{month}}
                    </jet-button>
                    <p class="text-base my-2 mr-3">پرونده های ثبت شده در این ماه: <span class="badge badge-red">{{thisYearProfilesCount}}</span>
                        پرونده</p>

                </div>
                <div class="md:col-span-6">
                    <bar-chart :chartData="yearChartData"
                               :chartOptions="chartOptions"/>
                    <bar-chart :chartData="monthChartData"
                               :chartOptions="chartOptions"/>
                </div>
                <div class="md:col-span-2">
                    <p class="text-lg font-bold text-indigo-500 mr-3 my-2">فعالیت کاربران</p>
                    <jet-label for="agent">انتخاب نماینده</jet-label>
                    <select class="form-input w-full block pr-8"
                            ref="agent"
                            name="agent"
                            id="agent"
                            v-model="agent"
                            @change="submitSearchForm">
                        <option v-for="agent in agentsChartData.agents"
                                :key="agent.id"
                                :value="agent.id">{{agent.name}}
                        </option>
                    </select>
                </div>
                <div class="md:col-span-6">
                    <bar-chart :chartData="agentsChartData"
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
            totalProfilesCount: Number,
            yearChartData: Object,
            monthChartData: Object,
            agentsChartData: Object,
            thisAgent: Number,
        },
        data() {
            return {
                year: this.thisYear,
                month: this.thisMonth,
                agent: this.thisAgent,
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
            submitSearchForm() {
                Inertia.visit(route('dashboard.reports.profiles'), {
                    method: 'get',
                    data: {
                        year: this.year,
                        month: this.month,
                        agent: this.agent,
                    },
                })
            }
        }
    }
</script>

<style scoped>

</style>
