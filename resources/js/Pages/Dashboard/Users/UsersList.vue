<template>
    <Dashboard>
        <template #breadcrumb> / لیست {{usersType}}</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <jet-button class="m-5 sm:float-left">
                                <InertiaLink :href="route('dashboard.users.create',{type:type})">
                                    ثبت کاربر جدید
                                </InertiaLink>
                            </jet-button>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تلفن تماس
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام کاربری
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in users.data" :key="user.id">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <div class="text-sm text-gray-900">
                                            {{user.name}}
                                            <span v-if="user.parent" class="text-indigo-600">(نماینده: {{user.parent ? user.parent.name : ''}})</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{user.mobile}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{user.username}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(user.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{user.statusText}}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <InertiaLink :href="route('dashboard.users.view',{type:type,id: user.id})"
                                                     class="text-indigo-600 hover:text-indigo-900">
                                            <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </InertiaLink>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.users.destroy',{type:type,id: user.id})"
                                                     class="text-red-600 hover:text-red-900">
                                            <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                            </svg>
                                        </InertiaLink>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <pagination
                                :urlsArray="paginatedLinks"
                                :previousPageUrl="users.prev_page_url"
                                :nextPageUrl="users.next_page_url"
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
    import JetButton from '@/Jetstream/Button';
    import Pagination from "@/Pages/Dashboard/Components/Pagination";

    export default {
        name: "UsersList",
        components: {Dashboard, JetButton, Pagination},
        props: {
            type: String,
            usersType: String,
            users: Object,
            paginatedLinks: Array,

        },
        methods: {
            statusColors(status) {
                switch (status) {
                    case 0:
                        return 'bg-yellow-100 text-yellow-800';
                    case 1:
                        return 'bg-green-100 text-green-800';
                    case 2:
                        return 'bg-red-100 text-red-800';
                }
            }
        }
    }
</script>

<style scoped>

</style>
