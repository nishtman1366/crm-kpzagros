<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست های تعمیرات</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="col-1 md:col-span-2">
                                    <input type="text"
                                           v-model="query"
                                           placeholder="جستجو در کدملی، شماره موبایل و اسم مشتری یا سریال دستگاه و کد رهگیری"
                                           class="w-3/4 inline shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border">
                                    <button v-on:click="submitSearchForm"
                                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        جستجو
                                    </button>
                                </div>
                                <div class="col-1 sm:col-span-2">
                                    <InertiaLink :href="route('dashboard.repairs.create',)">
                                        <jet-button class="my-5 mx-1 sm:float-left">
                                            <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M10,4L12,6H20A2,2 0 0,1 22,8V18A2,2 0 0,1 20,20H4C2.89,20 2,19.1 2,18V6C2,4.89 2.89,4 4,4H10M15,9V12H12V14H15V17H17V14H20V12H17V9H15Z"/>
                                            </svg>
                                            ثبت درخواست جدید
                                        </jet-button>
                                    </InertiaLink>
                                    <a v-show="$page.user.level=='ADMIN' || $page.user.level=='SUPERUSER'"
                                       target="_blank"
                                       :href="route('dashboard.repairs.downloadExcel',{
                                                query: query,
                                                statusId: status_id,
                                                toDate: to_date,
                                                fromDate: from_date,
                                            })">
                                        <jet-button class="my-5 mx-1 bg-yellow-600 hover:bg-yellow-500 sm:float-left">
                                            <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M15.8,20H14L12,16.6L10,20H8.2L11.1,15.5L8.2,11H10L12,14.4L14,11H15.8L12.9,15.5L15.8,20M13,9V3.5L18.5,9H13Z"/>
                                            </svg>
                                            دریافت لیست
                                        </jet-button>
                                    </a>
                                </div>
                                <div class="col-1 md:col-span-4">
                                    <select id="status_id"
                                            name="status_id"
                                            ref="status_id"
                                            v-model="status_id"
                                            autocomplete="status_id"
                                            v-on:change="submitSearchForm"
                                            title="فیلتر بر اساس وضعیت پرونده"
                                            v-b-tooltip.hover
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option :value="null">وضعیت پرونده</option>
                                        <option v-for="statusItem in statuses" :key="statusItem.id"
                                                :value="statusItem.id">{{statusItem.name}}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-1 md:col-span-4">
                                    <date-picker
                                        @change="submitFromDate"
                                        v-model="from_date"
                                        element="from_date"
                                        ref="from_date_cal"></date-picker>
                                    <jet-input placeholder="تاریخ شروع"
                                               name="from_date"
                                               id="from_date"
                                               ref="from_date"
                                               v-model="from_date"
                                               readonly/>
                                    <date-picker
                                        @change="submitToDate"
                                        v-model="to_date"
                                        element="to_date"
                                        ref="to_date_cal"></date-picker>
                                    <jet-input placeholder="تاریخ پایان"
                                               name="to_date"
                                               id="to_date"
                                               ref="to_date"
                                               v-model="to_date"
                                               readonly/>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام پذیرنده
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        مدل دستگاه
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        درخواست کننده
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        کد رهگیری
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        زمان ثبت
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        آخرین تغییرات
                                    </th>
                                    <th scope="col" class="py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="repair in repairs.data" :key="repair.id">
                                    <td class="py-4 text-center text-gray-900">{{repair.name}}</td>
                                    <td class="py-4 text-center text-gray-900">
                                        {{repair.device_type.name}}
                                        <p
                                            class="text-indigo-600">({{repair.psp ? repair.psp.name : 'نامشخص'}})</p>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        <p class="text-sm text-gray-900">{{repair.user ? repair.user.name : 'نامشخص'}}</p>
                                        <p class="text-sm text-indigo-500">
                                            {{repair.user && repair.user.parent ? 'نماینده: '+repair.user.parent.name : ''}}
                                        </p>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">{{repair.tracking_code}}</td>
                                    <td class="py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(repair.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{repair.statusText}}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">{{repair.jCreatedAt}}</td>
                                    <td class="py-4 text-center text-gray-900">{{repair.jUpdatedAt}}</td>
                                    <td class="py-4 text-center text-gray-900">
                                        <InertiaLink
                                            :href="route('dashboard.repairs.view',{repairId: repair.id})"
                                            class="tooltip-box text-indigo-600 hover:text-indigo-900">
                                            <button title="مشاهده درخواست"
                                                    v-b-tooltip.hover>
                                                <i id="test" class="material-icons">folder_shared</i>
                                            </button>
                                        </InertiaLink>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <pagination
                                :urlsArray="paginatedLinks"
                                :previousPageUrl="repairs.prev_page_url"
                                :nextPageUrl="repairs.next_page_url"
                            >
                            </pagination>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from '@/Jetstream/Button'
    import {Inertia} from "@inertiajs/inertia";
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetInputError from '@/Jetstream/InputError';
    import JetInput from '@/Jetstream/Input';
    import Pagination from "@/Pages/Dashboard/Components/Pagination";
    import VuePersianDatetimePicker from 'vue-persian-datetime-picker';

    export default {
        name: "List",
        components: {
            Dashboard,
            JetButton,
            JetConfirmationModal,
            JetDangerButton,
            JetSecondaryButton,
            JetInputError,
            JetInput,
            Pagination,
            datePicker: VuePersianDatetimePicker,
        },
        props: {
            searchQuery: String,

            repairs: Object,

            statuses: Array,
            statusId: String,

            fromDate: String,
            toDate: String,

            paginatedLinks: Array
        },
        data() {
            return {
                status_id: null,
                from_date: null,
                to_date: null,
                query: null,
            }
        },
        mounted(){
            this.query = this.searchQuery;
            this.status_id = this.statusId;
            this.from_date = this.fromDate;
            this.to_date = this.toDate;
        },
        methods:{
            statusColors(status) {
                switch (status) {
                    case 0:
                        return 'bg-yellow-100 text-yellow-800';
                    case 1:
                        return 'bg-green-100 text-green-800';
                    case 2:
                        return 'bg-yellow-100 text-yellow-800';
                    case 3:
                        return 'bg-green-100 text-green-800';
                    case 4:
                        return 'bg-green-100 text-green-800';
                    case 5:
                        return 'bg-green-100 text-green-800';
                    case 6:
                        return 'bg-yellow-100 text-yellow-800';
                    case 7:
                        return 'bg-green-100 text-green-800';
                }
            },
            submitSearchForm() {
                Inertia.visit(route('dashboard.repairs.list'), {
                    method: 'get',
                    data: {
                        query: this.query,
                        statusId: this.status_id,
                        agentId: this.agent_id,
                        marketerId: this.marketer_id,
                        fromDate: this.from_date,
                        toDate: this.to_date,
                    },
                })
            },
            submitFromDate(e) {
                this.submitSearchForm();
            },
            submitToDate(e) {
                this.submitSearchForm();
            }
        }
    }
</script>

<style scoped>

</style>
