<template>
    <SettingsMain active="licenses" title=" / مدیریت سرویس دهنده ها">
        <template #settings>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="col-1 md:col-span-2">
                                    <input type="text"
                                           v-model="query"
                                           placeholder="جستجو در نام مدارک"
                                           class="w-1/2 inline shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border">
                                    <jet-button @click.native="submitSearchForm"
                                                class="bg-blue-600 hover:bg-blue-500">
                                        جستجو
                                    </jet-button>
                                </div>
                                <div class="col-1 sm:col-span-2">
                                    <jet-button
                                        class="float-left"
                                        @click.native="newLicense">
                                        ثبت مدرک جدید
                                    </jet-button>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام مدرک
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        کلید
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ضرورت
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام فایل
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="license in licenses" :key="license.id">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{license.name}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{license.key}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="requiredColors(license.required)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                        {{license.requireText}}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{license.file_name}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(license.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{license.statusText}}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button class="text-indigo-600 hover:text-indigo-900"
                                                v-on:click="editLicense(license)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.settings.licenses.destroy',{licenseId:license.id})"
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

            <jet-confirmation-modal :show="submitLicenseModal" @close="submitLicenseModal = false">
                <template #title>
                    {{editLicenseModal ? 'ویرایش مدرک' : 'ثبت مدرک جدید'}}
                </template>
                <template #content>
                    <div class="mt-2">
                        <div class="my-2">
                            <input type="text"
                                   v-model="licenseForm.name"
                                   placeholder="نام مدرک"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                            <jet-input-error
                                :message="licenseForm.error('name')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <input type="text"
                                   v-model="licenseForm.key"
                                   placeholder="کلید مدرک"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                            <jet-input-error
                                :message="licenseForm.error('key')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <input type="text"
                                   v-model="licenseForm.file_name"
                                   placeholder="نام فایل"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                            <jet-input-error
                                :message="licenseForm.error('file_name')"
                                class="mt-2"/>
                        </div>
                        <div class="inline-block my-2">
                            <button v-on:click="licenseForm.required=1"
                                    :class="licenseForm.required===1 ? 'bg-green-700' : 'bg-green-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                ضروری
                            </button>
                            <button v-on:click="licenseForm.required=0"
                                    :class="licenseForm.required===0 ? 'bg-red-700' : 'bg-red-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                اختیاری
                            </button>
                            <jet-input-error
                                :message="licenseForm.error('status')"
                                class="mt-2"/>
                        </div>
                        <div class="inline-block float-left my-2">
                            <button v-on:click="licenseForm.status=1"
                                    :class="licenseForm.status===1 ? 'bg-green-700' : 'bg-green-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                فعال
                            </button>
                            <button v-on:click="licenseForm.status=0"
                                    :class="licenseForm.status===0 ? 'bg-red-700' : 'bg-red-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                غیرفعال
                            </button>
                            <jet-input-error
                                :message="licenseForm.error('status')"
                                class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="submitLicenseModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button v-if="editLicenseModal" class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitEditLicense"
                                :class="{ 'opacity-25': licenseForm.processing }"
                                :disabled="licenseForm.processing">
                        ویرایش
                    </jet-button>
                    <jet-button v-else class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitNewLicense"
                                :class="{ 'opacity-25': licenseForm.processing }"
                                :disabled="licenseForm.processing">
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
        name: "LicenseTypes",
        components: {SettingsMain, JetButton, JetInputError, JetConfirmationModal, JetDangerButton, JetSecondaryButton},
        props: {
            licenses: Array,
            searchQuery: String,
        },
        data() {
            return {
                query: '',
                submitLicenseModal: false,
                editLicenseModal: false,
                editLicenseId: '',
                licenseForm: this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    key: '',
                    file_name: '',
                    required: 1,
                    status: 1,
                }, {
                    bag: 'licenseForm',
                    resetOnSuccess: true
                }),
            }
        },
        mounted() {
            this.query = this.searchQuery
        },
        methods: {
            newLicense() {
                this.editLicenseModal = false;
                this.licenseForm = this.$inertia.form({
                    '_method': 'POST',
                    name: '',
                    key: '',
                    file_name: '',
                    required: 1,
                    status: 1,
                }, {
                    bag: 'licenseForm',
                    resetOnSuccess: true
                });
                this.submitLicenseModal = true;
            },
            editLicense(license) {
                this.licenseForm = this.$inertia.form({
                    '_method': 'PUT',
                    name: license.name,
                    key: license.key,
                    file_name: license.file_name,
                    required: license.required,
                    status: license.status,
                }, {
                    bag: 'licenseForm',
                    resetOnSuccess: true
                });
                this.editLicenseId = license.id;
                this.submitLicenseModal = true;
                this.editLicenseModal = true;
            },
            submitNewLicense() {
                this.licenseForm.post(route('dashboard.settings.licenses.store')).then(response => {
                    if (!this.licenseForm.hasErrors()) {
                        this.submitLicenseModal = false;
                    }
                })
            },
            submitEditLicense() {
                this.licenseForm.licenses = this.licenseList;
                this.licenseForm.post(route('dashboard.settings.licenses.update', {licenseId: this.editLicenseId})).then(response => {
                    if (!this.licenseForm.hasErrors()) {
                        this.submitLicenseModal = false;
                        this.editLicenseModal = false;
                    }
                })
            },
            submitSearchForm() {
                Inertia.visit(route('dashboard.settings.licenses.list'), {
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
            },
            requiredColors(required) {
                switch (required) {
                    case 0:
                        return 'bg-green-100 text-green-800';
                    case 1:
                        return 'bg-red-100 text-red-800';
                }
            }
        }
    }
</script>

<style scoped>

</style>
