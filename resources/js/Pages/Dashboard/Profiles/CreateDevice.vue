<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست ها / انتخاب دستگاه {{ customer.fullName }}</template>
        <template #dashboardContent>
            <ProfileSteps :step="4"
                          :customer-info="!!profile.customer"
                          :business-info="!!profile.customer"
                          :account-info="!!profile.account"
                          :profile-id="profile.id"
                          :edit="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER' ? true : false"
            ></ProfileSteps>
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6 bg-gray-300  rounded-lg">
                    <div class="md:col-span-1 m-2">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">انتخاب دستگاه</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش دستگاه مورد نظر برای ارائه به مشتری را انتخاب نمایید.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6">
                                        <jet-label value="نوع دستگاه"/>
                                        <jet-button @click.native="deviceTypeForm.type='WPOS'"
                                                    :class="deviceTypeForm.type==='WPOS' ? 'bg-yellow-500 text-white' : 'bg-yellow-200 text-gray-900 hover:bg-yellow-400'">
                                            پایانه فروش بیسیم
                                        </jet-button>
                                        <jet-button :disabled="true" @click.native="deviceTypeForm.type='IPG'"
                                                    :class="deviceTypeForm.type==='IPG' ? 'bg-yellow-500 text-white' : 'bg-yellow-200 text-gray-900 hover:bg-yellow-400'">
                                            درگاه پرداخت اینترنتی
                                        </jet-button>
                                        <jet-button :disabled="true" @click.native="deviceTypeForm.type='POS'"
                                                    :class="deviceTypeForm.type==='POS' ? 'bg-yellow-500 text-white' : 'bg-yellow-200 text-gray-900 hover:bg-yellow-400'">
                                            پایانه فروش رومیزی
                                        </jet-button>
                                        <jet-input-error :message="deviceTypeForm.error('type')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 md:col-span-3">
                                        <jet-label value="نحوه فروش"/>
                                        <jet-button @click.native="deviceTypeForm.device_sell_type='cash'"
                                                    :class="{'bg-blue-500':deviceTypeForm.device_sell_type==='cash'}"
                                                    class="bg-blue-200 text-gray-900 hover:bg-blue-400">نقدی
                                        </jet-button>
                                        <jet-button @click.native="deviceTypeForm.device_sell_type='dept'"
                                                    :class="{'bg-blue-500':deviceTypeForm.device_sell_type==='dept'}"
                                                    class="bg-blue-200 text-gray-900 hover:bg-blue-400">امانی
                                        </jet-button>
                                        <jet-button @click.native="deviceTypeForm.device_sell_type='installment'"
                                                    :class="{'bg-blue-500':deviceTypeForm.device_sell_type==='installment'}"
                                                    class="bg-blue-200 text-gray-900 hover:bg-blue-400">اقساطی
                                        </jet-button>
                                        <jet-input-error :message="deviceTypeForm.error('device_sell_type')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 md:col-span-3">
                                        <template v-if="deviceTypeForm.device_sell_type==='cash'">
                                            <jet-label for="device_amount" value="مبلغ فروش"/>
                                            <jet-input type="text" id="device_amount" name="device_amount"
                                                       v-model="deviceTypeForm.device_amount"/>
                                            <jet-input-error :message="deviceTypeForm.error('device_amount')"
                                                             class="mt-2"/>
                                        </template>
                                        <template v-else-if="deviceTypeForm.device_sell_type==='dept'">
                                            <jet-label for="device_amount" value="مبلغ امانت"/>
                                            <jet-input type="text" id="device_amount" name="device_amount"
                                                       v-model="deviceTypeForm.device_amount"/>
                                            <jet-input-error :message="deviceTypeForm.error('device_amount')"
                                                             class="mt-2"/>
                                        </template>
                                        <template v-else-if="deviceTypeForm.device_sell_type==='installment'">
                                            <jet-label for="device_dept_profile_id" value="شماره پرونده"/>
                                            <jet-input type="text" id="device_dept_profile_id"
                                                       name="device_dept_profile_id"
                                                       v-model="deviceTypeForm.device_dept_profile_id"/>
                                            <jet-input-error :message="deviceTypeForm.error('device_dept_profile_id')"
                                                             class="mt-2"/>
                                            <jet-label for="device_amount" value="مبلغ قسط"/>
                                            <jet-input type="text" id="device_amount" name="device_amount"
                                                       v-model="deviceTypeForm.device_amount"/>
                                            <jet-input-error :message="deviceTypeForm.error('device_amount')"
                                                             class="mt-2"/>
                                        </template>
                                    </div>
                                    <div class="col-span-6">
                                        <jet-label value="وضعیت فیزیکی دستگاه"/>
                                        <jet-button @click.native="deviceTypeForm.device_physical_status='new'"
                                                    :class="{'bg-purple-500':deviceTypeForm.device_physical_status==='new'}"
                                                    class="bg-purple-200 text-gray-900 hover:bg-purple-400">آکبند
                                        </jet-button>
                                        <jet-button @click.native="deviceTypeForm.device_physical_status='stock'"
                                                    :class="{'bg-purple-500':deviceTypeForm.device_physical_status==='stock'}"
                                                    class="bg-purple-200 text-gray-900 hover:bg-purple-400">کارکرده
                                        </jet-button>
                                        <jet-input-error :message="deviceTypeForm.error('device_physical_status')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-span-6">
                                        <label for="psp_id"
                                               class="block text-sm font-medium text-gray-700">
                                            شرکت سرویس دهنده:
                                        </label>
                                        <select name="psp_id"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                ref="psp_id"
                                                id="psp_id"
                                                v-model="deviceTypeForm.psp_id">
                                            <option value="">انتخاب کنید:</option>
                                            <option v-for="psp in psps" :key="psp.id" :value="psp.id">
                                                {{ psp.name }}
                                            </option>
                                        </select>
                                        <jet-input-error :message="deviceTypeForm.error('psp_id')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-span-6">
                                        <p class="text-lg">انتخاب نوع ارتباط:</p>
                                    </div>
                                    <div
                                        class="col-span-6 flex items-center justify-center flex-wrap space-x-2 space-x-reverse">
                                        <jet-button v-for="connectionType in connectionTypes"
                                                    :key="connectionType.id"
                                                    @click.native="selectConnectionType(connectionType.id)"
                                                    :class="selectedConnectionType===connectionType.id ? 'bg-green-500 hover:bg-green-400' : 'bg-green-300 hover:bg-green-400 text-gray-700'"
                                                    class="my-1 justify-center">
                                            {{ connectionType.name }}
                                        </jet-button>
                                    </div>
                                    <div v-if="selectedConnectionType===null || deviceTypeForm.psp_id === ''"
                                         class="col-span-6">
                                        <p class="text-lg text-center">شرکت سرویس دهنده و نوع ارتباط دستگاه را انتخاب
                                            نمایید.</p>
                                    </div>
                                    <div v-else-if="devicesList.length===0" class="col-span-6">
                                        <p class="text-lg text-center">در حال حاضر هیچ مدل دستگاهی از این نوع موجود
                                            نیست</p>
                                    </div>
                                    <div v-for="deviceType in devicesList" :key="deviceType.id"
                                         class="col-span-3 sm:col-span-2 text-center border rounded border-grey-600 p-3">
                                        <svg
                                            class="mx-auto h-12 w-12 text-gray-400"
                                            stroke="currentColor"
                                            fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                        <h1 class="text-base md:text-lg">{{ deviceType.name }}</h1>
                                        <jet-button type="submit"
                                                    class="bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-indigo-500"
                                                    @click.native="submitDeviceType(deviceType.id)">
                                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M11,15V12H9V15H6V17H9V20H11V17H14V15H11Z"/>
                                            </svg>
                                            انتخاب
                                        </jet-button>
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
import ProfileSteps from "@/Pages/Dashboard/Components/ProfileSteps";
import JetInputError from '@/Jetstream/InputError';
import JetButton from '@/Jetstream/Button';
import JetLabel from "@/Jetstream/Label"
import JetInput from "@/Jetstream/Input"

export default {
    name: "CreateDevice",
    components: {Dashboard, ProfileSteps, JetInputError, JetButton, JetLabel, JetInput},
    props: {
        profileId: Number,
        profile: Object,
        customer: Object,
        connectionTypes: Array,
        deviceTypes: Array,
        psps: Array,
        devicePsps: Array,
    },
    data() {
        return {
            selectedDeviceTypes: [],
            deviceNotFound: false,
            selectPsp: false,
            selectedConnectionType: null,
            deviceTypeForm: this.$inertia.form({
                '_method': 'POST',
                type: 'WPOS',
                profile_id: this.profileId,
                device_sell_type: null,
                device_amount: null,
                device_dept_profile_id: null,
                device_physical_status: null,
                device_type_id: '',
                device_connection_type_id: '',
                psp_id: '',
            }, {
                bag: 'deviceTypeForm',
                resetOnSuccess: false
            })
        }
    },
    computed: {
        devicesList: function () {
            let list = [];
            if (this.deviceTypeForm.psp_id === '') return [];
            if (this.selectedConnectionType === null) return [];

            this.deviceTypes.map(device => {
                if (device.device_connection_type_id === this.selectedConnectionType) {
                    let psps = this.devicePsps.filter(psp => psp.psp_id === this.deviceTypeForm.psp_id);
                    psps.map(psp => {
                        if (psp.device_type_id === device.id) {
                            list.push({id: device.id, name: device.name});
                        }
                    })
                }
            });
            return list;
        }
    },
    methods: {
        selectConnectionType(id) {
            this.selectedConnectionType = id;
        },
        submitDeviceType(id) {
            this.deviceTypeForm.device_type_id = id;
            this.deviceTypeForm.device_connection_type_id = this.selectedConnectionType;
            this.deviceTypeForm.post(route('dashboard.profiles.devices.store', {profile: this.profileId})).then(response => {

            })
        }
    }
}
</script>

<style scoped>

</style>
