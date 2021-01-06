<template>
    <Dashboard>
        <template #breadcrumb> / لیست دستگاه ها</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="col-1 md:col-span-2">
                                    <input type="text"
                                           v-model="query"
                                           placeholder="جستجو در شماره سریال دستگاه"
                                           class="w-1/2 inline shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border">
                                    <jet-button @click.native="submitSearchForm"
                                                class="bg-blue-600 hover:bg-blue-500">
                                        جستجو
                                    </jet-button>
                                </div>
                                <div class="col-1 sm:col-span-2">
                                    <InertiaLink :href="route('dashboard.devices.create')">
                                        <jet-button class="my-5 mx-1 sm:float-left">
                                            <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M10,4L12,6H20A2,2 0 0,1 22,8V18A2,2 0 0,1 20,20H4C2.89,20 2,19.1 2,18V6C2,4.89 2.89,4 4,4H10M15,9V12H12V14H15V17H17V14H20V12H17V9H15Z"/>
                                            </svg>
                                            ثبت دستگاه جدید
                                        </jet-button>
                                    </InertiaLink>
                                    <jet-button v-show="$page.user.level=='ADMIN' || $page.user.level=='SUPERUSER'" class="my-5 mx-1 bg-green-600 hover:bg-green-500 sm:float-left"
                                                @click.native="viewUploadModal=true">
                                        <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                            <path fill="currentColor"
                                                  d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M15.8,20H14L12,16.6L10,20H8.2L11.1,15.5L8.2,11H10L12,14.4L14,11H15.8L12.9,15.5L15.8,20M13,9V3.5L18.5,9H13Z"/>
                                        </svg>
                                        ورود اطلاعات
                                    </jet-button>
                                    <a v-show="$page.user.level=='ADMIN' || $page.user.level=='SUPERUSER'" target="_blank"
                                       :href="route('dashboard.devices.downloadExcel',{
                                                typeId:type_id,
                                                modelId:model_id,
                                                query:query,
                                                physicalStatus:physical_status,
                                                transportStatus:transport_status,
                                                pspStatus:psp_status
                                            })">
                                        <jet-button class="my-5 mx-1 bg-yellow-600 hover:bg-yellow-500 sm:float-left">
                                            <svg style="width:24px;height:24px;display: inline" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M15.8,20H14L12,16.6L10,20H8.2L11.1,15.5L8.2,11H10L12,14.4L14,11H15.8L12.9,15.5L15.8,20M13,9V3.5L18.5,9H13Z"/>
                                            </svg>
                                            دریافت لیست
                                        </jet-button>
                                    </a>
                                </div>
                                <div class="col-1 md:col-span-4">
                                    <select id="type_id"
                                            name="type_id"
                                            ref="type_id"
                                            v-model="type_id"
                                            autocomplete="type_id"
                                            v-on:change="submitSearchForm"
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">نوع ارتباط</option>
                                        <option v-for="type in types" :key="type.id" :value="type.id">{{type.name}}
                                        </option>
                                    </select>
                                    <select id="model_id"
                                            name="model_id"
                                            ref="model_id"
                                            v-model="model_id"
                                            autocomplete="model_id"
                                            v-on:change="submitSearchForm"
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">مدل دستگاه</option>
                                        <option v-for="model in models" :key="model.id" :value="model.id">{{model.name}}
                                        </option>
                                    </select>
                                    <select id="physical_status"
                                            name="physical_status"
                                            ref="physical_status"
                                            v-model="physical_status"
                                            autocomplete="physical_status"
                                            v-on:change="submitSearchForm"
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">وضعیت فیزیکی</option>
                                        <option value="1">سالم</option>
                                        <option value="2">خراب</option>
                                    </select>
                                    <select id="transport_status"
                                            name="transport_status"
                                            v-model="transport_status"
                                            ref="transport_status"
                                            autocomplete="transport_status"
                                            v-on:change="submitSearchForm"
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">وضعیت انبار</option>
                                        <option value="1">در انبار</option>
                                        <option value="2">در انتظار نصب</option>
                                        <option value="3">نصب شده</option>
                                    </select>
                                    <select id="psp_status"
                                            name="psp_status"
                                            v-model="psp_status"
                                            ref="psp_status"
                                            autocomplete="psp_status"
                                            v-on:change="submitSearchForm"
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">وضعیت psp</option>
                                        <option value="1">در انتظار تخصیص</option>
                                        <option value="2">تخصیص داده شده</option>
                                    </select>
                                    <select id="device_status"
                                            name="device_status"
                                            v-model="device_status"
                                            ref="device_status"
                                            autocomplete="device_status"
                                            v-on:change="submitSearchForm"
                                            class="mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">وضعیت</option>
                                        <option value="1">ثبت شده</option>
                                        <option value="2">تایید شده</option>
                                    </select>
                                </div>
                            </div>

                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        مدل دستگاه
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        شماره سریال
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        مالک
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت فیزیکی
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت انبار
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت PSP
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col"
                                        class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        گارانتی
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="device in devices.data" :key="device.id">
                                    <td class="py-4 text-center text-gray-900">
                                        <div class="text-sm text-gray-900">
                                            {{device.device_type.name}}
                                            <span class="text-indigo-600">({{device.device_type.type.name}})</span>
                                        </div>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        {{device.serial}}
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        {{device.user.name}}
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(device.physical_status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{device.physicalStatusText}}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        <span
                                            :class="transportStatusColors(device.transport_status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{device.transportStatusText}}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(device.psp_status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{device.pspStatusText}}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        <span
                                            :class="status2Colors(device.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{device.statusText}}
                                        </span>
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        {{device.guarantee_start}} - {{device.guarantee_end}}
                                    </td>
                                    <td class="py-4 text-center text-gray-900">
                                        <InertiaLink :href="route('dashboard.devices.view',{id: device.id})"
                                                     class="text-indigo-600 hover:text-indigo-900"
                                                     v-b-tooltip.hover
                                                     title="ویرایش">
                                            <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </InertiaLink>
                                        <InertiaLink v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'" method="DELETE"
                                                     :href="route('dashboard.devices.destroy',{id: device.id})"
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
                                :previousPageUrl="devices.prev_page_url"
                                :nextPageUrl="devices.next_page_url"
                            >
                            </pagination>
                        </div>
                    </div>
                </div>
            </div>

            <jet-confirmation-modal :show="viewUploadModal" @close="viewUploadModal = false">
                <template #title>
                    ارسال فایل اکسل
                </template>
                <template #content>
                    <div class="w-full mt-3 text-center sm:mt-0 sm:text-right">
                        <div class="mt-2">
                            <input type="file"
                                   placeholder="انتخاب فایل"
                                   v-on:change="onExcelFileChange"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border">
                            <jet-input-error
                                :message="excelForm.error('file')"
                                class="mt-2"/>
                        </div>
                        <p v-if="excelForm.processing" class="my-3 mx-2 text-indigo-600">در حال ارسال
                            فایل...</p>
                        <jet-action-message :on="excelForm.recentlySuccessful" class="my-3 mx-2 text-green-600">
                            محتویات
                            فایل دریافتی با موفقیت به پایگاه داده اضافه شد.
                        </jet-action-message>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="viewUploadModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-blue-600 hover:bg-blue-500" @click.native="submitExcelForm"
                                :class="{ 'opacity-25': excelForm.processing }"
                                :disabled="excelForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>

        </template>
    </Dashboard>
</template>

<script>
    import {Inertia} from '@inertiajs/inertia';
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from '@/Jetstream/Button';
    import Pagination from "@/Pages/Dashboard/Components/Pagination";
    import JetInputError from '@/Jetstream/InputError'
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetActionMessage from '@/Jetstream/ActionMessage'

    export default {
        name: "DevicesList",
        components: {
            Dashboard,
            JetButton,
            Pagination,
            JetInputError,
            JetConfirmationModal,
            JetDangerButton,
            JetActionMessage,
            JetSecondaryButton
        },
        props: {
            devices: Object,
            paginatedLinks: Array,
            models: Array,
            types: Array,
            searchQuery: String,
            typeId: String,
            modelId: String,
            physicalStatus: String,
            transportStatus: String,
            pspStatus: String,
            deviceStatus: String,
        },
        data() {
            return {
                query: null,
                type_id: null,
                model_id: null,
                physical_status: null,
                transport_status: null,
                psp_status: null,
                viewUploadModal: false,
                excelFile: '',
                excelForm: this.$inertia.form({
                    '_method': 'POST',
                    file: ''
                }, {
                    bag: 'excelForm',
                    resetOnSuccess: false
                }),
                submitExcelFormButton: false,
                submitExcelFormLoading: false,
                submitExcelFormSuccessMessage: false,
            }
        },
        mounted() {
            this.query = this.searchQuery;
            this.type_id = this.typeId;
            this.model_id = this.modelId;
            this.physical_status = this.physicalStatus;
            this.transport_status = this.transportStatus;
            this.psp_status = this.pspStatus;
            this.device_status = this.deviceStatus;
        },
        methods: {
            statusColors(status) {
                switch (status) {
                    case 1:
                        return 'bg-green-100 text-green-800';
                    case 2:
                        return 'bg-blue-100 text-red-800';
                }
            },
            transportStatusColors(status) {
                switch (status) {
                    case 1:
                        return 'bg-green-100 text-green-800';
                    case 2:
                        return 'bg-yellow-100 text-yellow-800';
                    case 3:
                        return 'bg-blue-100 text-blue-800';
                }
            },
            status2Colors(status) {
                switch (status) {
                    case 2:
                        return 'bg-green-100 text-green-800';
                    case 1:
                        return 'bg-yellow-100 text-yellow-800';
                }
            },
            submitSearchForm() {
                Inertia.visit(route('dashboard.devices.list'), {
                    method: 'get',
                    data: {
                        searchQuery: this.query,
                        typeId: this.type_id,
                        modelId: this.model_id,
                        physicalStatus: this.physical_status,
                        transportStatus: this.transport_status,
                        pspStatus: this.psp_status,
                        deviceStatus: this.device_status,
                    },
                })

            },
            onExcelFileChange(e) {
                const file = e.target.files[0];
                this.excelForm.file = e.target.files[0];
            },
            submitExcelForm() {
                this.excelForm.post(route('dashboard.devices.uploadExcel'))
                    .then(response => {

                    });
            }
        }
    }
</script>

<style scoped>

</style>
