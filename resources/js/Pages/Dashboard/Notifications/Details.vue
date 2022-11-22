<template>
    <Dashboard>
        <template #breadcrumb> / اعلان های گروهی / {{notification.title}}</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="mx-1 my-2 p-2 rounded border border-gray-200 bg-gray-50 flex justify-between">
                                <div>کد پیامک: <span class="font-bold">{{notification.bulk_id}}</span></div>
                                <div>تعداد گیرندگان: <span class="font-bold">{{notification.receptions_count}}</span>
                                </div>
                                <div>گیرندگان صحیح: <span class="font-bold">{{notification.valid_recipients}}</span>
                                </div>
                                <div>هزینه: <span class="font-bold">{{notification.cost}} ریال</span></div>
                                <div>هزینه بازگشتی: <span class="font-bold">{{notification.payback_cost}} ریال</span>
                                </div>
                                <div>وضعیت: <span class="font-bold">{{notification.statusText}}</span></div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200 mt-3">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        گیرنده
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                </tr>
                                </thead>
                                <tbody v-if="receptions" class="bg-white divide-y divide-gray-200">
                                <tr v-for="reception in receptions.data" :key="reception.reception">
                                    <td style="direction: ltr" class="px-6 py-4 text-center text-gray-900">
                                        {{reception.reception}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{reception.statusText}}
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <pagination
                                :urlsArray="paginatedLinks"
                                :total-rows="receptions.total"
                                :previousPageUrl="receptions.prev_page_url"
                                :nextPageUrl="receptions.next_page_url"
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
    import Pagination from "@/Pages/Dashboard/Components/Pagination";

    export default {
        name: "Details",
        components: {
            Dashboard,Pagination
        },
        props: {
            notification: Object,
            receptions: Object,
            paginatedLinks: Object,
        },
        data() {
            return {

            }
        },
        computed: {

        },
        methods: {

        }
    }
</script>

<style scoped>

</style>
