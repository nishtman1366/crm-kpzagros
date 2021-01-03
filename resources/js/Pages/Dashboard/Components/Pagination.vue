<template>
    <div>
        <ul class="flex justify-self-center justify-center pl-0 list-none rounded my-2">
            <InertiaLink v-if="nextPageUrl"
                :href="nextPageUrl"
                class="page-link">
                <li
                    class="relative block py-2 px-3 leading-tight bg-white border border-gray-300 text-blue-700 rounded-r hover:bg-gray-200"
                >
                    بعدی
                </li>
            </InertiaLink>
            <InertiaLink v-for="eachUrlArray in urlsArray"
                         v-if="eachUrlArray.type === 'URLS'"
                         :href="eachUrlArray.url"
                         :key="eachUrlArray.indexKey">
                <li class="relative block py-2 px-3 leading-tight bg-white border border-gray-300 text-blue-700 hover:bg-gray-200"
                    :class="{ 'bg-gray-200 text-red-600': eachUrlArray.isCurrentPage === true }">
                    <span>{{eachUrlArray.pageNumber}}</span>
                </li>
            </InertiaLink>
            <InertiaLink v-if="previousPageUrl"
                :href="previousPageUrl">
                <li
                    class="relative block py-2 px-3 leading-tight bg-white border border-gray-300 text-blue-700 ml-0 rounded-l hover:bg-gray-200"
                >
                    قبلی
                </li>
            </InertiaLink>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "Pagination",
        props: ['urlsArray', 'previousPageUrl', 'nextPageUrl'],
    }
</script>

<style scoped>

</style>
