<template>
    <SettingsMain active="devices" title=" / مدیریت انواع دستگاه ها">
        <template #settings>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="col-1 md:col-span-2">
                                    <input type="text"
                                           v-model="query"
                                           placeholder="جستجو در مدل دستگاه"
                                           class="w-1/2 inline shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border">
                                    <jet-button @click.native="submitSearchForm"
                                                class="bg-blue-600 hover:bg-blue-500">
                                        جستجو
                                    </jet-button>
                                </div>
                                <div class="col-1 sm:col-span-2">
                                    <jet-button
                                        class="float-left"
                                        @click.native="newDevice">
                                        ثبت دستگاه جدید
                                    </jet-button>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        مدل دستگاه
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نوع ارتباط
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        موجودی
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="device in deviceTypes" :key="device.id">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{device.name}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{device.type.name}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <div class="text-sm text-green-900">سالم {{device.physicalStatus1Count |
                                            persianDigit}}
                                        </div>
                                        <div class="text-sm text-red-900">خراب {{device.physicalStatus2Count |
                                            persianDigit}}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(device.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{device.statusText}}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button class="text-indigo-600 hover:text-indigo-900"
                                                v-on:click="editDevice(device)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.settings.devices.destroy',{deviceId:device.id})"
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

            <jet-confirmation-modal :show="submitDeviceModal" @close="submitDeviceModal = false">
                <template #title>
                    {{editDeviceModal ? 'ویرایش اطلاعات دستگاه' : 'ثبت مدل جدید دستگاه'}}
                </template>
                <template #content>
                    <div class="mt-2 grid md:grid-cols-2 gap-3">
                        <div class="md:cols-1">
                            <input type="text"
                                   v-model="deviceForm.name"
                                   placeholder="نام مدل"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                            <jet-input-error
                                :message="deviceForm.error('name')"
                                class="mt-2"/>
                        </div>
                        <div class="md:cols-1">
                            <select v-model="deviceForm.device_connection_type_id"
                                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                                <option value="">نوع ارتباط</option>
                                <option v-for="type in deviceConnectionTypes" :key="type.id"
                                        :value="type.id">
                                    {{type.name}}
                                </option>
                            </select>
                            <jet-input-error
                                :message="deviceForm.error('device_connection_type_id')"
                                class="mt-2"/>
                        </div>
                        <div class="md:cols-1">
                            <button v-on:click="deviceForm.status=1"
                                    :class="deviceForm.status===1 ? 'bg-green-700' : 'bg-green-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                فعال
                            </button>
                            <button v-on:click="deviceForm.status=2"
                                    :class="deviceForm.status===2 ? 'bg-red-700' : 'bg-red-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                غیرفعال
                            </button>
                            <jet-input-error
                                :message="deviceForm.error('status')"
                                class="mt-2"/>
                        </div>
                        <div class="col-span-2">
                            <p>لطفا سرویس دهندگانی که از این مدل پشتیبانی میکنند را انتخاب نمایید:</p>
                            <label v-for="psp in psps" :key="psp.id" :for="'psp_'+psp.id"
                                   class="inline-block mx-3 my-1 py-1 px-2 border border-indigo-500 rounded">
                                {{psp.name}} <input type="checkbox"
                                                    v-model="pspList"
                                                    :id="'psp_'+psp.id"
                                                    :value="psp.id">
                            </label>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="submitDeviceModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button v-if="editDeviceModal" class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitEditDevice"
                                :class="{ 'opacity-25': deviceForm.processing }"
                                :disabled="deviceForm.processing">
                        ویرایش
                    </jet-button>
                    <jet-button v-else class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitNewDevice"
                                :class="{ 'opacity-25': deviceForm.processing }"
                                :disabled="deviceForm.processing">
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
        name: "Devices",
        components: {SettingsMain, JetButton, JetInputError, JetConfirmationModal, JetDangerButton, JetSecondaryButton},
        props: {
            deviceTypes: Array,
            deviceConnectionTypes: Array,
            searchQuery: String,
            psps: Array
        },
        data() {
            return {
                query: '',
                submitDeviceModal: false,
                editDeviceModal: false,
                editDeviceId: '',
                pspList: [],
                deviceForm: this.$inertia.form({
                    '_method': 'POST',
                    device_connection_type_id: '',
                    name: '',
                    status: 1,
                    description: '',
                    image: '',

                }, {
                    bag: 'deviceForm',
                    resetOnSuccess: true
                }),
            }
        },
        mounted() {
            this.query = this.searchQuery
        },
        methods: {
            newDevice() {
                this.editDeviceModal = false;
                this.pspList = [];
                this.deviceForm = this.$inertia.form({
                    '_method': 'POST',
                    device_connection_type_id: '',
                    name: '',
                    status: '',
                    description: '',
                    image: '',
                    psps: []
                }, {
                    bag: 'deviceForm',
                    resetOnSuccess: true
                });
                this.submitDeviceModal = true;
            },
            editDevice(device) {
                this.pspList = [];
                this.deviceForm = this.$inertia.form({
                    '_method': 'PUT',
                    device_connection_type_id: device.device_connection_type_id,
                    name: device.name,
                    status: device.status,
                    description: '',
                    image: '',
                    psps: []
                }, {
                    bag: 'deviceForm',
                    resetOnSuccess: true
                });
                this.editDeviceId = device.id;
                this.submitDeviceModal = true;
                this.editDeviceModal = true;
                let psps = device.psps;
                for (let psp of psps) {
                    this.pspList.push(psp.psp_id);
                }
            },
            submitNewDevice() {
                this.deviceForm.psps = this.pspList;
                this.deviceForm.post(route('dashboard.settings.devices.store')).then(response => {
                    if (!this.deviceForm.hasErrors()) {
                        this.submitDeviceModal = false;
                    }
                })
            },
            submitEditDevice() {
                this.deviceForm.psps = this.pspList;
                this.deviceForm.post(route('dashboard.settings.devices.update', {deviceId: this.editDeviceId})).then(response => {
                    if (!this.deviceForm.hasErrors()) {
                        this.submitDeviceModal = false;
                        this.editDeviceModal = false;
                    }
                })
            },
            submitSearchForm() {
                Inertia.visit(route('dashboard.settings.devices.list'), {
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
                    case 2:
                        return 'bg-red-100 text-red-800';
                }
            }
        }
    }
</script>

<style scoped>

</style>
