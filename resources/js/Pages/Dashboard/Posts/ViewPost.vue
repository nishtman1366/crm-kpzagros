<template>
    <Dashboard>
        <template #breadcrumb> / {{post.title}}</template>
        <template #dashboardContent>
            <div class="grid grid-cols-4 gap-3">
                <div class="sm:col-span-3" v-html="post.body"></div>
                <div>
                    <div v-if="post.files.length > 0" class="mx-2 my-3 border-b border-gray-300">
                        <p class="py-1 text-lg">دانلود فایل</p>
                        <p class="py-1 text-center" v-for="file in post.files" :key="file.id">
                            <a :href="file.url" target="_blank">
                                <jet-button>دریافت</jet-button>
                            </a>
                        </p>
                    </div>
                    <div v-if="post.videos.length > 0" class="mx-2 my-3 border-b border-gray-300">
                        <p class="text-lg">ویدئوها</p>
                        <p v-for="video in post.videos" :key="video.id">
                            <video controls class="w-full m-1 border border-gray-500">
                                <source type="video/mp4" :src="video.url">
                            </video>
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from "@/Jetstream/Button"

    export default {
        name: "ViewPost",
        components: {
            Dashboard,
            JetButton,
        },
        props: {
            post: Object
        }
    }
</script>

<style scoped>

</style>
