<template>
    <Dashboard>
        <template #breadcrumb> / ثبت دستگاه جدید</template>
        <template #dashboardContent>
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6 bg-gray-300  rounded-lg">
                    <div class="md:col-span-1 m-2">
                        <div class="px-4 sm:px-0 text-justify">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">اطلاعات دستگاه جدید</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات مربوط به دستگاه جدید را وارد نمایید.
                            </p>
                            <p class="mt-5 text-sm text-gray-600">
                                در قسمت مدل، نوع ارتباط، شیوه ارتباط دستگاه موردنظر را انتخاب نمایید.
                            </p>
                            <p class="mt-5 text-sm text-gray-600">
                                در قسمت مدل، مدل دستگاه موردنظر را انتخاب نمایید.
                            </p>
                            <p v-if="$page.user.level==='AGENT'"
                               class="mt-5 p-3 text-sm text-white bg-yellow-500 rounded">
                                چنانچه مدل مورد نظر شما در لیست موجود نیست، باید از مدیر خود بخواهید که مدل مورد نظر شما
                                را ثبت نماید.
                            </p>
                            <p v-else-if="$page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER'"
                               class="mt-5 p-3 text-sm text-white bg-yellow-500 rounded">
                                چنانچه مدل مورد نظر شما در لیست موجود نیست، باید از طریق بخش «تنظیمات >> دستگاه ها» مدل
                                مورد نظر خود را ثبت نماید.
                            </p>
                            <p class="mt-5 text-sm text-gray-600">
                                در قسمت سریال، شماره سریال دستگاه موردنظر را وارد نمایید.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6">
                                    <div class="col-span-6">
                                        <label for="device_type_id" class="block text-sm font-medium text-gray-700">
                                            نوع ارتباط دستگاه:
                                        </label>
                                        <button v-for="connectionType in deviceConnectionTypes" :key="connectionType.id"
                                                v-on:click="selectDeviceConnection(connectionType.id)"
                                                class="mx-2 sm:col-span-2 inline-flex justify-center py-2 px-4 border border-green-700 shadow-sm text-sm font-medium rounded-md bg-white hover:bg-green-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                                :class="chosenConnectionType===connectionType.id ? 'bg-green-700 text-white' : ' text-green-600'">
                                            {{connectionType.name}}
                                        </button>
                                        <jet-input-error :message="deviceForm.error('device_connection_type_id')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-span-6">
                                        <label for="device_type_id" class="block text-sm font-medium text-gray-700">
                                            مدل دستگاه:
                                        </label>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                            <div v-for="deviceType in selectedDeviceTypes" :key="deviceType.id"
                                                 :class="chosenDeviceTypes===deviceType.id ? 'bg-indigo-200' : ''"
                                                 class="text-center border rounded border-grey-600 p-3">
                                                <svg
                                                    class="mx-auto h-12 w-12 text-gray-400"
                                                    stroke="currentColor"
                                                    fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"/>
                                                </svg>
                                                <h1 class="text-lg">{{deviceType.name}}</h1>
                                                <button type="submit"
                                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                        v-on:click="chooseDeviceType(deviceType.id)">
                                                    <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                                        <path fill="currentColor"
                                                              d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M11,15V12H9V15H6V17H9V20H11V17H14V15H11Z"/>
                                                    </svg>
                                                    انتخاب این مدل
                                                </button>
                                            </div>
                                        </div>
                                        <div v-if="deviceNotFound" class="text-center text-red-600">هیچ مدلی از نوع
                                            ارتباطی انتخاب شده موجود نیست.
                                        </div>
                                        <jet-input-error :message="deviceForm.error('device_type_id')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-2 sm:col-span-2">
                                        <label for="serial" class="block text-sm font-medium text-gray-700">
                                            شماره سریال دستگاه:
                                        </label>
                                        <jet-input type="text"
                                                   placeholder="شماره سریال دستگاه"
                                                   ref="serial"
                                                   id="serial"
                                                   v-model="deviceForm.serial"/>
                                        <jet-input-error :message="deviceForm.error('serial')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-2 sm:col-span-2">
                                        <label for="guarantee_start" class="block text-sm font-medium text-gray-700">
                                            تاریخ شروع گارانتی:
                                        </label>
                                        <date-picker
                                            v-model="deviceForm.guarantee_start" ref="guarantee_start_cal"
                                            element="guarantee_start"></date-picker>
                                        <jet-input type="text"
                                                   placeholder="تاریخ شروع گارانتی"
                                                   ref="guarantee_start"
                                                   id="guarantee_start"
                                                   v-model="deviceForm.guarantee_start"
                                                   readonly/>
                                        <jet-input-error :message="deviceForm.error('guarantee_start')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-2 sm:col-span-2">
                                        <label for="guarantee_end" class="block text-sm font-medium text-gray-700">
                                            تاریخ پایان گارانتی:
                                        </label>
                                        <date-picker
                                            v-model="deviceForm.guarantee_end" ref="guarantee_end_cal"
                                            element="guarantee_end"></date-picker>
                                        <jet-input type="text"
                                                   placeholder="تاریخ پایان گارانتی"
                                                   ref="guarantee_end"
                                                   id="guarantee_end"
                                                   v-model="deviceForm.guarantee_end"
                                                   readonly/>
                                        <jet-input-error :message="deviceForm.error('guarantee_end')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-2 sm:col-span-2">
                                        <label for="physical_status" class="block text-sm font-medium text-gray-700">
                                            وضعیت فیزیکی:
                                        </label>
                                        <select name="physical_status"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                id="physical_status"
                                                v-model="deviceForm.physical_status"
                                                ref="physical_status">
                                            <option value="1">سالم</option>
                                            <option value="2">خراب</option>
                                        </select>
                                        <jet-input-error :message="deviceForm.error('physical_status')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-2 sm:col-span-2">
                                        <label for="transport_status" class="block text-sm font-medium text-gray-700">
                                            وضعیت انبار:
                                        </label>
                                        <select name="transport_status"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                id="transport_status"
                                                v-model="deviceForm.transport_status"
                                                ref="transport_status">
                                            <option value="1">در انبار</option>
                                            <option value="2">در انتظار نصب</option>
                                            <option value="3">نصب شده</option>
                                        </select>
                                        <jet-input-error :message="deviceForm.error('transport_status')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-2 sm:col-span-2">
                                        <label for="psp_status" class="block text-sm font-medium text-gray-700">
                                            وضعیت psp:
                                        </label>
                                        <select name="psp_status"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                id="psp_status"
                                                v-model="deviceForm.psp_status"
                                                ref="psp_status">
                                            <option value="1"> انتظار تخصیص</option>
                                            <option value="2">تخصیص داده شده</option>
                                        </select>
                                        <jet-input-error :message="deviceForm.error('psp_status')"
                                                         class="mt-2"/>
                                    </div>
                                    <div v-if="$page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER'"
                                         class="col-2 sm:col-span-2">
                                        <label for="status" class="block text-sm font-medium text-gray-700">
                                            مالک:
                                        </label>
                                        <select id="user_id" name="user_id" ref="user_id"
                                                v-model="deviceForm.user_id"
                                                autocomplete="status"
                                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option :value="$page.user.id">خودم</option>
                                            <option v-for="marketer in marketers"
                                                    :key="marketer.id"
                                                    :value="marketer.id">{{marketer.name}}
                                            </option>
                                        </select>
                                        <jet-input-error :message="deviceForm.error('user_id')"
                                                         class="mt-2"/>
                                    </div>
                                    <div v-if="$page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER'" class="col-2 sm:col-span-2">
                                        <label for="status" class="block text-sm font-medium text-gray-700">
                                            وضعیت:
                                        </label>
                                        <select id="status" name="status" ref="status"
                                                v-model="deviceForm.status"
                                                autocomplete="status"
                                                class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="1">ثبت شده</option>
                                            <option value="2">تایید شده</option>
                                        </select>
                                        <jet-input-error :message="deviceForm.error('status')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-6 sm:col-span-6 text-left">
                                        <button type="submit"
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                v-on:click="submitDeviceForm">
                                            ذخیره اطلاعات
                                        </button>
                                    </div>
                                </div>
                            </div>
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
    import JetInput from '@/Jetstream/Input';
    import JetInputError from '@/Jetstream/InputError';
    import VuePersianDatetimePicker from 'vue-persian-datetime-picker';

    export default {
        name: "CreateDevice",
        components: {Dashboard, JetButton, JetInputError, JetInput, datePicker: VuePersianDatetimePicker},
        props: {
            deviceConnectionTypes: Array,
            deviceTypes: Array,
            marketers: Array,
        },
        data() {
            return {
                selectedDeviceTypes: [],
                deviceNotFound: false,
                chosenConnectionType: 0,
                chosenDeviceTypes: 0,
                imageFiles: {
                    imageFilePreview: '',
                },
                deviceForm: this.$inertia.form({
                    '_method': 'POST',
                    device_connection_type_id: '',
                    device_type_id: '',
                    serial: '',
                    physical_status: 1,
                    transport_status: 1,
                    psp_status: 1,
                    guarantee_start: '',
                    guarantee_end: '',
                    status: 1,
                    user_id: this.$page.user.id
                }, {
                    bag: 'deviceForm',
                    resetOnSuccess: false
                })
            }
        },
        methods: {
            selectDeviceConnection(id) {
                let typeList = [];
                for (let type of this.deviceTypes) {
                    if (type.device_connection_type_id == id) {
                        typeList.push({id: type.id, name: type.name});
                    }
                }
                this.selectedDeviceTypes = typeList;
                if (typeList.length === 0) this.deviceNotFound = true;
                else this.deviceNotFound = false;

                this.chosenConnectionType = id;
                this.deviceForm.device_connection_type_id = id;
            },
            chooseDeviceType(id) {
                this.deviceForm.device_type_id = id;
                this.chosenDeviceTypes = id;
            },
            submitDeviceForm() {
                this.deviceForm.post(route('dashboard.devices.store'), {
                    preserveScroll: true
                }).then(response => {

                })
            }
        }
    }
</script>

<style scoped>

</style>
