<template>
    <SettingsMain active="psps" title=" / مدیریت سرویس دهنده ها">
        <template #settings>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="col-1 md:col-span-2">
                                    <input type="text"
                                           v-model="query"
                                           placeholder="جستجو در نام سرویس دهنده"
                                           class="w-1/2 inline shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border">
                                    <jet-button @click.native="submitSearchForm"
                                                class="bg-blue-600 hover:bg-blue-500">
                                        جستجو
                                    </jet-button>
                                </div>
                                <div class="col-1 sm:col-span-2">
                                    <jet-button
                                        class="float-left"
                                        @click.native="newPsp">
                                        ثبت سرویس دهنده جدید
                                    </jet-button>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام سرویس دهنده
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="psp in psps" :key="psp.id">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{psp.name}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(psp.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{psp.statusText}}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button class="text-indigo-600 hover:text-indigo-900"
                                                v-on:click="editPsp(psp)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.settings.psps.destroy',{pspId:psp.id})"
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
                        </div>
                    </div>
                </div>
            </div>

            <jet-confirmation-modal :show="submitPspModal" @close="submitPspModal = false">
                <template #title>
                    {{editPspModal ? 'ویرایش اطلاعات سرویس دهنده' : 'ثبت سرویس دهنده جدید'}}
                </template>
                <template #content>
                    <div class="mt-2">
                        <div class="my-2">
                            <input type="text"
                                   v-model="pspForm.name"
                                   placeholder="نام سرویس دهنده"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                            <jet-input-error
                                :message="pspForm.error('name')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <button v-on:click="pspForm.status=1"
                                    :class="pspForm.status===1 ? 'bg-green-700' : 'bg-green-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                فعال
                            </button>
                            <button v-on:click="pspForm.status=0"
                                    :class="pspForm.status===0 ? 'bg-red-700' : 'bg-red-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                غیرفعال
                            </button>
                            <jet-input-error
                                :message="pspForm.error('status')"
                                class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="submitPspModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button v-if="editPspModal" class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitEditPsp"
                                :class="{ 'opacity-25': pspForm.processing }"
                                :disabled="pspForm.processing">
                        ویرایش
                    </jet-button>
                    <jet-button v-else class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitNewPsp"
                                :class="{ 'opacity-25': pspForm.processing }"
                                :disabled="pspForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>

        </template>
    </SettingsMain>
</template>

<script>
    import SettingsMain from "@/Pages/Dashboard/Settings/SettingsMain";
    import JetButton from '@/Jetstream/Button'
    import JetInputError from '@/Jetstream/InputError';
    import {Inertia} from "@inertiajs/inertia";
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        name: "Psps",
        components: {SettingsMain, JetButton, JetInputError, JetConfirmationModal, JetDangerButton, JetSecondaryButton},
        props: {
            psps: Array,
            searchQuery: String,
        },
        data() {
            return {
                query: '',
                submitPspModal: false,
                editPspModal: false,
                editPspId: '',
                pspForm: this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    status: 1,
                    image: '',
                }, {
                    bag: 'pspForm',
                    resetOnSuccess: true
                }),
            }
        },
        mounted() {
            this.query = this.searchQuery
        },
        methods: {
            newPsp() {
                this.editPspModal = false;
                this.pspForm = this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    status: 1,
                    image: '',
                }, {
                    bag: 'pspForm',
                    resetOnSuccess: true
                });
                this.submitPspModal = true;
            },
            editPsp(psp) {
                this.pspForm = this.$inertia.form({
                    '_method': 'PUT',
                    name: psp.name,
                    status: psp.status,
                    image: '',
                }, {
                    bag: 'pspForm',
                    resetOnSuccess: true
                });
                this.editPspId = psp.id;
                this.submitPspModal = true;
                this.editPspModal = true;
            },
            submitNewPsp() {
                this.pspForm.post(route('dashboard.settings.psps.store')).then(response => {
                    if (!this.pspForm.hasErrors()) {
                        this.submitPspModal = false;
                    }
                })
            },
            submitEditPsp() {
                this.pspForm.psps = this.pspList;
                this.pspForm.post(route('dashboard.settings.psps.update', {pspId: this.editPspId})).then(response => {
                    if (!this.pspForm.hasErrors()) {
                        this.submitPspModal = false;
                        this.editPspModal = false;
                    }
                })
            },
            submitSearchForm() {
                Inertia.visit(route('dashboard.settings.psps.list'), {
                    method: 'GET',
                    data: {
                        query: this.query
                    },
                })
            },
            statusColors(status) {
                switch (status) {
                    case 1:
                        return 'bg-green-100 text-green-800';
                    case 0:
                        return 'bg-red-100 text-red-800';
                }
            }
        }
    }
</script>

<style scoped>

</style>
