<template>
    <Dashboard>
        <template #breadcrumb> / اخبار / لیست اخبار</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="col-1 sm:col-span-4">
                                    <InertiaLink :href="route('dashboard.posts.create')">
                                        <jet-button class="my-5 mx-1 sm:float-left">
                                            <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M10,4L12,6H20A2,2 0 0,1 22,8V18A2,2 0 0,1 20,20H4C2.89,20 2,19.1 2,18V6C2,4.89 2.89,4 4,4H10M15,9V12H12V14H15V17H17V14H20V12H17V9H15Z"/>
                                            </svg>
                                            ثبت خبر جدید
                                        </jet-button>
                                    </InertiaLink>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        عنوان
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        دسته بندی
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تاریخ ارسال
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="post in posts.data" :key="post.id">
                                    <td class="py-4 text-center text-gray-900">{{post.title}}</td>
                                    <td class="py-4 text-center text-gray-900">{{post.category ? post.category.name
                                        : ''}}
                                    </td>
                                    <td class="py-4 text-center text-gray-900">{{post.date}}</td>
                                    <td class="py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(post.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{post.statusText}}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        <InertiaLink :href="route('dashboard.posts.edit',{postId: post.id})"
                                                     class="text-indigo-600 hover:text-indigo-900"
                                                     v-b-tooltip.hover
                                                     title="ویرایش">
                                            <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </InertiaLink>
                                        <InertiaLink
                                            method="DELETE"
                                            :href="route('dashboard.posts.destroy',{id: post.id})"
                                            class="text-red-600 hover:text-red-900"
                                            v-b-tooltip.hover
                                            title="حذف">
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
                                :total-rows="posts.total"
                                :previousPageUrl="posts.prev_page_url"
                                :nextPageUrl="posts.next_page_url"
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
    import JetButton from '@/Jetstream/Button';

    export default {
        name: "ListPosts",
        components: {
            Dashboard,
            Pagination,
            JetButton,
        },
        props: {
            posts: Object,
            paginatedLinks: Array
        },
        methods:{
            statusColors(status) {
                switch (status) {
                    case 1:
                        return 'bg-green-100 text-green-800';
                    case 0:
                        return 'bg-red-100 text-red-800';
                }
            },
        }
    }
</script>

<style scoped>

</style>
