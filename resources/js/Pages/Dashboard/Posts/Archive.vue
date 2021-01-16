<template>
    <Dashboard>
        <template #breadcrumb> / اخبار و اطلاعیه ها</template>
        <template #dashboardContent>
            <div>
                <jet-input type="text"
                           class="w-full md:w-1/2"
                           v-model="query"
                           placeholder="جستجو در اخبار"/>
                <jet-button @click.native="submitSearchForm">
                    جستجو
                </jet-button>
            </div>
            <div>
                <inertia-link :href="route('dashboard.posts.archive')">
                    <jet-button
                        :class="{'bg-green-500':categoryId===0}"
                        class="mx-3 my-1 bg-green-300 hover:bg-green-400 active:bg-green-600">
                        همه دسته بندی ها
                    </jet-button>
                </inertia-link>
                <inertia-link v-for="category in categories" :key="category.id"
                              :href="route('dashboard.posts.archive',{category:category.id})">
                    <jet-button
                        :class="{'bg-green-500':categoryId===category.id}"
                        class="mx-3 my-1 bg-green-300 hover:bg-green-400 active:bg-green-600">{{category.name}}
                    </jet-button>
                </inertia-link>
            </div>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-3">
                <div v-for="post in posts.data" :key="post.id"
                     class="m-3 p-3 border border-gray-300 rounded">
                    <inertia-link :href="route('dashboard.posts.view',{postId:post.id})">
                        <p class="text-lg">{{post.title}}</p>
                    </inertia-link>
                    <p class="text-md h-10" v-html="post.body"></p>
                    <jet-section-border></jet-section-border>
                    <p class="text-left">
                        <inertia-link :href="route('dashboard.posts.view',{postId:post.id})">
                            <jet-button>متن کامل</jet-button>
                        </inertia-link>
                    </p>
                </div>
                <div class="sm:col-span-2 md:col-span-3">
                    <Pagination
                        :urlsArray="paginatedLinks"
                        :total-rows="posts.total"
                        :previousPageUrl="posts.prev_page_url"
                        :nextPageUrl="posts.next_page_url"
                    />
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from '@/Jetstream/Button';
    import JetInput from '@/Jetstream/Input';
    import JetSectionBorder from '@/Jetstream/SectionBorder';
    import Pagination from "@/Pages/Dashboard/Components/Pagination";
    import {Inertia} from "@inertiajs/inertia";

    export default {
        name: "Archive",
        components: {Pagination, Dashboard, JetButton, JetSectionBorder, JetInput},
        props: {
            categories: Array,
            posts: Object,
            paginatedLinks: Array,
            categoryId: Number,
            searchQuery: String
        },
        data() {
            return {
                query: this.searchQuery
            }
        },
        methods: {
            submitSearchForm() {
                Inertia.visit(route('dashboard.posts.archive'), {
                    method: 'get',
                    data: {
                        query: this.query,
                        categoryId: this.categoryId,
                    },
                })
            },
        }
    }
</script>

<style scoped>

</style>
