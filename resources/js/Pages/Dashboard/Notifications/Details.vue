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
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="reception in receptions" :key="reception.recipient">
                                    <td style="direction: ltr" class="px-6 py-4 text-center text-gray-900">
                                        {{reception.recipient}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{reception.status}}
                                    </td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="2">
                                        <div class="mx-1 my-2 bg-gray-50 flex justify-between items-center w-full">
                                            <div></div>
                                            <div>
                                                <span v-for="item in numbers" :key="item"
                                                      @click="viewPage(item)"
                                                      :class="(item-1)===pagination.page ? 'bg-blue-600 text-white' : 'bg-blue-200'"
                                                      class="mx-1 p-1 hover:bg-blue-300 transition duration-300 ease-in-out cursor-pointer rounded inline-flex items-center justify-center w-12 h-12"
                                                >
                                                    {{item}}
                                                </span>
                                            </div>
                                            <div>
                                                <select id="limit"
                                                        @change="viewPage()"
                                                        name="limit"
                                                        v-model="form.limit">
                                                    <option :value="10">10</option>
                                                    <option :value="25">25</option>
                                                    <option :value="50">50</option>
                                                    <option :value="100">100</option>
                                                    <option :value="500">500</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";

    export default {
        name: "Details",
        components: {
            Dashboard
        },
        props: {
            notification: Object,
            receptions: Array,
            pagination: Object
        },
        data() {
            return {
                form: this.$inertia.form({
                    limit: this.pagination.limit,
                    page: this.pagination.page
                })
            }
        },
        computed: {
            numbers: function () {
                let pages = this.pagination.pages;
                let current = this.pagination.page;
                console.log(current);
                if (current < 9) {
                    return [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
                } else if (current >= (pages - 10)) {
                    return [(pages - 10), (pages - 9), (pages - 8), (pages - 7), (pages - 6), (pages - 5), (pages - 4), (pages - 3), (pages - 2), (pages - 1)];
                } else {
                    return [
                        (current - 4),
                        (current - 3),
                        (current - 2),
                        (current - 1),
                        current,
                        (current + 1),
                        (current + 2),
                        (current + 3),
                        (current + 4),
                        (current + 8)
                    ];
                }
            }
        },
        methods: {
            viewPage(page) {
                this.form.page = page;
                this.form.submit('get', route('dashboard.notifications.details', {id: this.notification.id}));
            }
        }
    }
</script>

<style scoped>

</style>
