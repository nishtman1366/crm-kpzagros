<template>
    <Dashboard>
        <template #breadcrumb> / درخواست های عودت دستگاه</template>
        <template #dashboardContent>
            <div class="flex items-center">
                <div class="mx-1" style="flex-grow: 3">
                    <jet-input type="text" class="w-full" placeholder="جستجو در نام، شماره ملی، کد رهگیری و ..."
                               v-model="query"/>
                </div>
                <div class="mx-1">
                    <jet-button @click.native="submitSearchForm" class="bg-blue-500 hover:bg-blue-400">جستجو
                    </jet-button>
                </div>
                <div class="flex-grow text-left" v-if="$page.user.level!=='ACCOUNTING'">
                    <inertia-link :href="route('dashboard.returns.create')">
                        <jet-button class="">ثبت درخواست جدید</jet-button>
                    </inertia-link>
                </div>
            </div>
            <div class="flex">
                <select id="status_id"
                        name="status_id"
                        ref="status_id"
                        v-model="status_id"
                        autocomplete="status_id"
                        v-on:change="submitSearchForm"
                        title="فیلتر بر اساس وضعیت پرونده"
                        v-b-tooltip.hover
                        class="mx-1 mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option :value="null">وضعیت پرونده</option>
                    <option v-for="statusItem in statuses" :key="statusItem.id"
                            :value="statusItem.id">{{ statusItem.name }}
                    </option>
                </select>
                <!-- فیلتر بر اساس کاربر درخواست کننده-->
                <select id="user_id"
                        name="user_id"
                        ref="user_id"
                        v-model="user_id"
                        autocomplete="user_id"
                        v-on:change="submitSearchForm"
                        title="فیلتر بر اساس کاربر درخواست کننده"
                        v-b-tooltip.hover
                        class="mx-1 mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option :value="null">درخواست کننده</option>
                    <option v-for="user in users" :key="user.id"
                            :value="user.id">{{ user.name }}
                    </option>
                </select>
            </div>
            <div>
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
                            مبلغ دستگاه
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
                    <tr v-for="device in returns.data" :key="device.id">
                        <td class="py-4 text-center text-gray-900">{{ device.name }}</td>
                        <td class="py-4 text-center text-gray-900">{{ device.device_type && device.device_type.name }}
                        </td>
                        <td class="py-4 text-center text-gray-900">
                            <p class="text-sm text-gray-900">
                                {{ device.user ? device.user.name : 'نامشخص' }}</p>
                            <p class="text-sm text-indigo-500">
                                {{ device.user && device.user.parent ? 'نماینده: ' + device.user.parent.name : '' }}
                            </p>
                        </td>
                        <td class="py-4 text-center text-gray-900">{{ device.tracking_code }}</td>
                        <td class="py-4 text-center text-gray-900">{{ device.amount }}</td>
                        <td class="py-4 text-center text-gray-900">
                                        <span
                                                :class="statusColors(device.status)"
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{ device.statusText }}
                                        </span>
                        </td>
                        <td class="py-4 text-center text-gray-900">{{ device.jCreatedAt }}</td>
                        <td class="py-4 text-center text-gray-900">{{ device.jUpdatedAt }}</td>
                        <td class="py-4 text-center text-gray-900 flex">
                            <InertiaLink
                                    :href="route('dashboard.returns.view',{returnId: device.id})"
                                    class="tooltip-box text-indigo-600 hover:text-indigo-900">
                                <button title="مشاهده درخواست"
                                        v-b-tooltip.hover>
                                    <i class="material-icons">folder_shared</i>
                                </button>
                            </InertiaLink>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <pagination
                        :urlsArray="paginatedLinks"
                        :previousPageUrl="returns.prev_page_url"
                        :nextPageUrl="returns.next_page_url"
                        :totalRows="returns.total"
                >
                </pagination>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetInput from "@/Jetstream/Input"
    import JetButton from "@/Jetstream/Button"
    import Pagination from "@/Pages/Dashboard/Components/Pagination";
    import {Inertia} from "@inertiajs/inertia";

    export default {
        name: "List",
        components: {Dashboard, JetInput, JetButton, Pagination},
        props: {
            returns: Object,
            paginatedLinks: Array,
            users: Array,
            statuses: Array,
            userId: Number,
            statusId: Number,
            searchQuery: String
        },
        data() {
            return {
                query: this.searchQuery,
                user_id: this.userId,
                status_id: this.statusIs
            }
        },
        methods: {
            statusColors(status) {
                switch (status) {
                    case 0:
                        return 'bg-yellow-100 text-yellow-800';
                    case 1:
                        return 'bg-yellow-100 text-yellow-800';
                    case 2:
                        return 'bg-yellow-100 text-yellow-800';
                    case 3:
                        return 'bg-yellow-100 text-yellow-800';
                    case 4:
                        return 'bg-green-100 text-green-800';
                    case 5:
                        return 'bg-green-100 text-green-800';
                    case 6:
                        return 'bg-red-100 text-red-800';
                }
            },
            submitSearchForm() {
                Inertia.visit(route('dashboard.returns.list'), {
                    method: 'get',
                    data: {
                        query: this.query,
                        userId: this.user_id,
                        statusId: this.status_id,
                    },
                })
            },
        }
    }
</script>

<style scoped>

</style>
