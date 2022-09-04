<template>
    <div class="w-full h-screen flex flex-col">
        <jet-dialog-modal maxWidth="md" :show="loading" @close="loading = false">
            <template #title>
                در حال بارگزاری اطلاعات
            </template>
            <template #content>
                <loading/>
            </template>
            <template #footer>

            </template>
        </jet-dialog-modal>
        <div v-if="messageBox"
             class="absolute top-2 left-2 h-16 w-64 bg-green-400 text-white flex items-center justify-center">
            <div class="text-white font-bold">ثبت اطلاعات با موفقیت انجام شد.</div>
        </div>
        <div class="shadow-sm">
            <div class="text-center">
                <div>
                    <img class="w-48 mx-auto"
                         :src="$page.configs.companyLogo ? $page.configs.companyLogo : 'https://tailwindui.com/img/logos/workflow-mark-indigo-500.svg'">
                </div>
                <div><slot name="header"></slot></div>
            </div>

        </div>
        <div class="flex-1 p-4">
            <div class="w-full flex flex-col items-center justify-center space-y-2">
                <slot name="contents"></slot>
            </div>
        </div>
        <portal-target name="modal" multiple></portal-target>
    </div>
</template>

<script>
import {Inertia} from "@inertiajs/inertia";
import JetDialogModal from '@/Jetstream/DialogModal';
import Loading from "@/Pages/Dashboard/Components/Loading";
import JetApplicationMark from "@/Jetstream/ApplicationMark";

export default {
    name: "Guest",
    components: {
        JetDialogModal,
        Loading,
        JetApplicationMark
    },
    data() {
        return {
            loading: false,
        }
    },
    mounted() {
        Inertia.on('start', event => {
            this.loading = true;
        });

        Inertia.on('finish', event => {
            this.loading = false;
        });
    },
    computed: {
        messageBox() {
            if (this.$page.message) {
                setTimeout(() => {
                    this.$page.message = null;
                    // this.viewMessageBox = false;
                }, 2500);
                // this.viewMessageBox = true;
                return true;
            }

            return false;
        }
    }
}
</script>

<style scoped>

</style>
