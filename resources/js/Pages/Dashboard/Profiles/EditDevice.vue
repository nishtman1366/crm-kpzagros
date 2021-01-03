<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست ها / انتخاب دستگاه {{customer.fullName}}</template>
        <template #dashboardContent>
            <ProfileSteps :step="4"
                          :customer-info="!!profile.customer"
                          :business-info="!!profile.customer"
                          :account-info="!!profile.account"
                          :profile-id="profile.id"
                          :edit="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER' ? true : false"
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
                                        <label for="psp_id"
                                               class="block text-sm font-medium text-gray-700">
                                            شرکت سرویس دهنده:
                                        </label>
                                        <select name="psp_id"
                                                v-on:change="changePsp"
                                                class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                ref="psp_id"
                                                id="psp_id"
                                                v-model="deviceTypeForm.psp_id">
                                            <option value="">انتخاب کنید:</option>
                                            <option v-for="psp in psps" :key="psp.id" :value="psp.id">
                                                {{psp.name}}
                                            </option>
                                        </select>
                                        <jet-input-error :message="deviceTypeForm.error('psp_id')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-span-6">
                                        <p class="text-lg">انتخاب نوع ارتباط:</p>
                                    </div>
                                    <button v-for="connectionType in connectionTypes" :key="connectionType.id"
                                            v-on:click="selectDeviceConnection(connectionType.id)"
                                            class="col-span-3 sm:col-span-2 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        {{connectionType.name}}
                                    </button>
                                    <div v-if="selectPsp" class="col-span-6">
                                        <p class="text-lg text-center">شرکت سرویس دهنده را انتخاب نمایید.</p>
                                    </div>
                                    <div v-else-if="deviceNotFound" class="col-span-6">
                                        <p class="text-lg text-center">در حال حاضر هیچ مدل دستگاهی از این نوع موجود
                                            نیست</p>
                                    </div>
                                    <div v-for="deviceType in selectedDeviceTypes" :key="deviceType.id"
                                         :class="deviceTypeForm.device_type_id==deviceType.id ? 'bg-indigo-200' : ''"
                                         class="col-2 sm:col-span-2 text-center border rounded border-grey-600 p-3">
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
                                                :disabled="deviceTypeForm.device_type_id==deviceType.id"
                                                :class="deviceTypeForm.device_type_id==deviceType.id ? 'text-gray-400 bg-gray-200' : 'text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'"
                                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md"
                                                v-on:click="submitDeviceType(deviceType.id)">
                                            <svg style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M13,9H18.5L13,3.5V9M6,2H14L20,8V20A2,2 0 0,1 18,22H6C4.89,22 4,21.1 4,20V4C4,2.89 4.89,2 6,2M11,15V12H9V15H6V17H9V20H11V17H14V15H11Z"/>
                                            </svg>
                                            انتخاب این مدل
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
    import ProfileSteps from "@/Pages/Dashboard/Components/ProfileSteps";
    import JetInputError from '@/Jetstream/InputError';

    export default {
        name: "CreateDevice",
        components: {Dashboard, ProfileSteps, JetInputError},
        props: {
            profileId: Number,
            profile: Object,
            customer: Object,
            connectionTypes: Array,
            deviceTypes: Array,
            deviceType: Object,
            psps: Array,
            devicePsps: Array,
        },
        data() {
            return {
                selectedDeviceTypes: [],
                deviceNotFound: false,
                selectPsp: false,
                deviceTypeForm: this.$inertia.form({
                    '_method': 'POST',
                }, {
                    bag: 'deviceTypeForm',
                    resetOnSuccess: false
                })
            }
        },
        mounted() {
            this.deviceTypeForm = this.$inertia.form({
                '_method': 'PUT',
                profile_id: this.profileId,
                device_type_id: this.deviceType.id,
                psp_id: this.profile.psp_id
            }, {
                bag: 'deviceTypeForm',
                resetOnSuccess: false
            });
            this.selectDeviceConnection(this.deviceType.device_connection_type_id);
        },
        methods: {
            changePsp() {
                this.selectedDeviceTypes = [];
            },
            selectDeviceConnection(id) {
                this.selectPsp = false;
                if (this.deviceTypeForm.psp_id === '') {
                    this.selectPsp = true;
                    return '';
                }
                let devicePspList = [];
                for (let psp of this.devicePsps) {
                    if (psp.psp_id == this.deviceTypeForm.psp_id) {
                        devicePspList.push(psp.device_type_id);
                    }
                }

                let typeList = [];
                for (let type of this.deviceTypes) {
                    let index = devicePspList.indexOf(type.id);
                    if (index !== -1 && type.device_connection_type_id == id) {
                        typeList.push({id: type.id, name: type.name});
                    }
                }

                this.selectedDeviceTypes = typeList;

                if (typeList.length === 0) this.deviceNotFound = true;
                else this.deviceNotFound = false;
            },
            submitDeviceType(id) {
                this.deviceTypeForm.device_type_id = id;
                this.deviceTypeForm.post(route('dashboard.profiles.devices.update', {profileId: this.profileId})).then(response => {

                })
            }
        }
    }
</script>

<style scoped>

</style>
