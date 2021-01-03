<template>
    <div class="mb-3">
        <inertia-link v-for="(link,index) in links" :key="index" :href="link.url">
            <jet-button
                :class="active==link.item ? 'bg-green-600 text-white' : 'bg-green-400'"
                class="my-1 mx-1  border border-green-600 hover:bg-green-500 hover:text-white">
                {{link.title}}
            </jet-button>
        </inertia-link>
    </div>
</template>

<script>
    import JetButton from '@/Jetstream/Button'

    export default {
        name: "SettingsMenu",
        components: {JetButton},
        props: {
            links: Array,
            active: {
                type: String,
                default: 'main'
            }
        }
    }
</script>

<style scoped>

</style>
