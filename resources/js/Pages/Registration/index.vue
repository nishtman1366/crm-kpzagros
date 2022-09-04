<template>
    <guest>
        <template #header>
            <!--            <div class="text-2xl">سامانه جامع نمایندگان</div>-->
        </template>
        <template #contents>
            <div
                class="w-full lg:w-1/2 border border-gray-200 rounded p-4 md:p-16 flex flex-col md:flex-row md:space-x-reverse space-y-4 md:space-y-0 md:space-x-4 items-center justify-center">
                <jet-button @click.native="openNationalCodeSearchDialog"
                            class="w-full md:w-1/2 justify-center bg-green-200 text-green-500 hover:bg-green-400 hover:text-white">
                    جستجو بر اساس کد
                    ملی پذیرنده
                </jet-button>
                <jet-button @click.native="openSerialSearchDialog"
                            class="w-full md:w-1/2 justify-center bg-blue-200 text-blue-500 hover:bg-blue-400 hover:text-white">
                    جستجو بر اساس شماره
                    سریال دستگاه
                </jet-button>
            </div>

            <div v-if="resultsType"
                 class="w-full lg:w-1/2 border border-gray-200 rounded p-4 md:p-16 flex flex-col md:flex-row md:space-x-reverse space-y-4 md:space-y-0 md:space-x-4 items-center justify-center">
                <div v-if="results.length===0">هیچ موردی یافت نشد.</div>
                <div class="w-full" v-else>
                    <template v-if="resultsType==='customers'">
                        <Customer v-for="(customer,index) in results"
                                  :key="index"
                                  :customer="customer"
                                  @selectDevice="openSubmitFormDialog"
                        />
                    </template>
                    <template v-if="resultsType==='devices'">
                        <Device v-for="(device,index) in results"
                                :key="index"
                                :device="device"
                                @selectDevice="openSubmitFormDialog"
                        />
                    </template>
                </div>
            </div>

            <jet-dialog-modal :show="viewNationalCodeSearchDialog" @close="closeNationalCodeSearchDialog">
                <template #title>
                    جستجو بر اساس کد ملی
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <jet-input class="w-full" type="text" placeholder="کد ملی پذیرنده"
                                   v-model="nationalCode" id="nationalCode"/>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeNationalCodeSearchDialog">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-green-600" @click.native="searchDevices('nationalCode')">
                        جستجو
                    </jet-button>
                </template>
            </jet-dialog-modal>
            <jet-dialog-modal :show="viewSerialSearchDialog" @close="closeSerialSearchDialog">
                <template #title>
                    جستجو بر اساس سریال دستگاه
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <jet-input class="w-full" type="text" placeholder="شماره سریال دستگاه"
                                   v-model="serial" id="serial"/>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeSerialSearchDialog">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-green-600" @click.native="searchDevices('serial')">
                        جستجو
                    </jet-button>
                </template>
            </jet-dialog-modal>
            <jet-dialog-modal :show="viewSubmitFormDialog" @close="closeSubmitFormDialog">
                <template #title>
                    ثبت اطلاعات
                </template>
                <template #content>
                    <div class="mt-3">
                        <div class="text-xl block md:flex md:space-x-reverse md:space-x-2 justify-around"
                             v-if="selectedDevice">
                            <div>
                                <span>مدل دستگاه:</span>
                                <span class="font-bold">
                                    {{ selectedDevice.device_type && selectedDevice.device_type.name }}
                                </span>
                            </div>
                            <div>
                                <span>سریال دستگاه:</span>
                                <span class="font-bold">{{ selectedDevice.serial }}</span>
                            </div>
                        </div>
                        <div class="mt-4 block md:flex md:space-x-reverse md:space-x-2">
                            <div>
                                <jet-label for="imei" value="کد IMEI"/>
                                <jet-input type="text" v-model="form.imei" id="imei" class="w-full"/>
                                <jet-input-error :message="form.error('imei')"/>
                            </div>
                            <div>
                                <jet-label for="sim_number" value="شماره سیم‌کارت"/>
                                <jet-input type="text" v-model="form.sim_number" id="sim_number" class="w-full"/>
                                <jet-input-error :message="form.error('sim_number')"/>
                            </div>
                        </div>
                        <div class="mt-4 block flex space-x-reverse space-x-2">
                            <div class="w-1/2">
                                <jet-input type="text" placeholder="حاصل عبارت روبرو" v-model="form.captcha"
                                           id="captcha" class="w-full"/>
                                <jet-input-error :message="form.error('captcha')"/>
                            </div>
                            <div class="w-1/2 flex items-start md:items-center">
                                <img @click="refreshCaptcha" class="w-full md:w-1/3  cursor-pointer" :src="captcha"/>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeSubmitFormDialog">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="ml-2 bg-green-600" @click.native="storeSubmitForm()"
                                :class="{'opacity-25':form.processing}"
                                :disable="form.processing">
                        ثبت
                    </jet-button>
                </template>
            </jet-dialog-modal>
        </template>
    </guest>
</template>

<script>
import Guest from "@/Layouts/Guest";

import JetInput from '@/Jetstream/Input';
import JetInputError from '@/Jetstream/InputError';
import JetButton from '@/Jetstream/Button';
import JetLabel from '@/Jetstream/Label';
import JetDialogModal from '@/Jetstream/DialogModal';
import JetDangerButton from '@/Jetstream/DangerButton';
import JetSecondaryButton from '@/Jetstream/SecondaryButton';
import Customer from "@/Pages/Registration/Components/Customer";
import Device from "@/Pages/Registration/Components/Device";

export default {
    name: "index",
    components: {
        Device,
        Customer,
        Guest,
        JetInput,
        JetInputError,
        JetButton,
        JetLabel,
        JetDialogModal,
        JetDangerButton,
        JetSecondaryButton
    },
    props: {
        resultsType: String,
        results: Array,
    },
    data() {
        return {
            viewNationalCodeSearchDialog: false,
            viewSerialSearchDialog: false,
            nationalCode: null,
            serial: null,

            viewSubmitFormDialog: false,
            selectedDevice: null,
            form: this.$inertia.form({
                imei: null,
                sim_number: null,
                captcha: null,
            }),

            captcha: null,
        }
    },
    methods: {
        openNationalCodeSearchDialog() {
            this.viewNationalCodeSearchDialog = true;
        },
        closeNationalCodeSearchDialog() {
            this.viewNationalCodeSearchDialog = false;
        },
        openSerialSearchDialog() {
            this.viewSerialSearchDialog = true;
        },
        closeSerialSearchDialog() {
            this.viewSerialSearchDialog = false;
        },
        searchDevices(type) {
            this.closeNationalCodeSearchDialog();
            let data = {search: true, type: null};
            if ((type === 'nationalCode')) {
                data.type = 'nationalCode';
                data.query = this.nationalCode;
            } else if (type === 'serial') {
                data.type = 'serial';
                data.query = this.serial;
            }
            this.$inertia.visit(route('registration.search', {type: data.type, query: data.query}))
        },

        openSubmitFormDialog(device) {
            this.selectedDevice = device;
            this.form.imei = device.imei;
            this.form.sim_number = device.sim_number;
            this.refreshCaptcha();
            this.viewSubmitFormDialog = true;
        },
        storeSubmitForm() {
            this.form.post(route('registration.store', {device: this.selectedDevice}), {
                onSuccess: () => {
                    if (!this.form.hasErrors()) {
                        this.closeSubmitFormDialog();
                    } else {
                        this.refreshCaptcha();
                    }
                }
            });
        },
        closeSubmitFormDialog() {
            this.viewSubmitFormDialog = false;
            this.captcha = null;
            this.form.reset();
        },

        refreshCaptcha() {
            axios.get(route('captcha.refresh'))
                .then(response => {
                    this.captcha = response.data.src;
                    this.form.captcha = null;
                })
                .catch(error => {
                    console.log(error);
                })
        }
    }
}
</script>

<style scoped>

</style>
