<template>
    <guest>
        <template #header>
            <!--            <div class="text-2xl">سامانه جامع نمایندگان</div>-->
        </template>
        <template #contents>
            <div class="w-full lg:w-4/5 xl:w-2/3 mx-auto">
                <div class="p-3 bg-gray-300">
                    <jet-form-section @submitted="submitNewRepairForm">
                        <template #title>
                            ثبت درخواست تعمیرات
                        </template>
                        <template #description>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات درخواست تعمیرات را ثبت نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">مدل و شماره سریال دستگاه:</span>
                                مدل دستگاه مورد نظر را از لیست مدل ها انتخاب نمایید و شماره سریال دستگاه را وارد نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">اطلاعات پذیرنده:</span>
                                در این بخش اطلاعات مربوط به مالک دستگاه را وارد نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">ایراد دستگاه:</span>
                                در این بخش از بین موارد ذکر شده ایراد مربوط به دستگاه را انتخاب نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">توضیحات تکمیلی:</span>
                                چنانچه موردی جهت توضیح در خصوص ایرادات پیش آمده وجود دارد میتوانید در این بخش وارد
                                نمایید.
                            </p>
                        </template>
                        <template #form>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="device_type_id" value="مدل دستگاه"/>
                                <select id="device_type_id" name="device_type_id" ref="device_type_id"
                                        v-model="newRepairForm.device_type_id"
                                        autocomplete="device_type_id"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                    <option v-for="type in deviceTypes" :key="type.id"
                                            :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                                <jet-input-error :message="newRepairForm.error('device_type_id')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="serial" value="سریال دستگاه"/>
                                <jet-input id="serial"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="newRepairForm.serial"
                                           ref="serial"
                                           autocomplete="serial"/>
                                <jet-input-error :message="newRepairForm.error('serial')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="psp_id" value="سرویس دهنده"/>
                                <select id="psp_id" name="psp_id" ref="psp_id"
                                        v-model="newRepairForm.psp_id"
                                        autocomplete="psp_id"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                    <option v-for="psp in psps" :key="psp.id"
                                            :value="psp.id">
                                        {{ psp.name }}
                                    </option>
                                </select>
                                <jet-input-error :message="newRepairForm.error('psp_id')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="bank_id" value="بانک"/>
                                <select id="bank_id" name="bank_id" ref="bank_id"
                                        v-model="newRepairForm.bank_id"
                                        autocomplete="bank_id"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                    <option v-for="bank in banks" :key="bank.id"
                                            :value="bank.id">
                                        {{ bank.name }}
                                    </option>
                                </select>
                                <jet-input-error :message="newRepairForm.error('bank_id')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="guarantee_end" value="تاریخ پایان گارانتی"/>
                                <date-picker
                                    ref="guarantee_cal"
                                    input-format="YYYY-MM-DD"
                                    format="jYYYY/jMM/jDD"
                                    @change="selectGuaranteeEnd"
                                    element="guarantee_end"
                                    v-model="guarantee_end">
                                </date-picker>
                                <jet-input id="guarantee_end"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="guarantee_end"
                                           ref="guarantee_end"
                                           autocomplete="guarantee_end"
                                           readonly="true"/>
                                <jet-input-error :message="newRepairForm.error('guarantee_end')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="name" value="نام پذیرنده"/>
                                <jet-input id="name"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="newRepairForm.name"
                                           ref="name"
                                           autocomplete="name"/>
                                <jet-input-error :message="newRepairForm.error('name')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="national_code" value="کد ملی پذیرنده"/>
                                <jet-input id="national_code"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="newRepairForm.national_code"
                                           ref="national_code"
                                           autocomplete="national_code"/>
                                <jet-input-error :message="newRepairForm.error('national_code')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="mobile" value="تلفن همراه پذیرنده"/>
                                <jet-input id="mobile"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="newRepairForm.mobile"
                                           ref="mobile"
                                           autocomplete="mobile"/>
                                <jet-input-error :message="newRepairForm.error('mobile')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="business_name" value="نام فروشگاه"/>
                                <jet-input id="business_name"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="newRepairForm.business_name"
                                           ref="business_name"
                                           autocomplete="business_name"/>
                                <jet-input-error :message="newRepairForm.error('business_name')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-label for="accessories" value="اقلام همراه"/>

                                <div class="flex">
                                    <jet-label v-for="item in accessories" :key="item.id" class="flex items-center"
                                               :for="'accessories_'+item.id">
                                        <input class="form-checkbox" type="checkbox" :name="'accessories_'+item.id"
                                               :id="'accessories_'+item.id"
                                               v-model="newRepairForm.accessories"
                                               :value="item.id"/>
                                        <span class="mr- ml-3">{{ item.name }}</span>
                                    </jet-label>
                                </div>
                                <jet-input-error :message="newRepairForm.error('accessories')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <div class="col-span-6 text-lg">
                                ایراد دستگاه
                            </div>
                            <div v-for="type in repairTypes" :key="type.id" class="col-span-6 sm:col-span-2">
                                <jet-label>
                                    <input type="checkbox"
                                           class="form-input p-0 rounded-none m-1"
                                           v-model="newRepairForm.repairTypeList"
                                           :id="'type_'+type.id"
                                           :value="type.id">{{ type.name }}
                                </jet-label>
                            </div>
                            <div class="col-span-6">
                                <jet-input-error :message="newRepairForm.error('repairTypeList')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <jet-label for="description" value="توضیحات تکمیلی"/>
                                <textarea id="description"
                                          type="description"
                                          class="form-input mt-1 block w-full"
                                          v-model="newRepairForm.description"
                                          ref="description"
                                          autocomplete="description" rows="3" cols="60"></textarea>
                                <jet-input-error :message="newRepairForm.error('description')" class="mt-2"/>
                            </div>
                        </template>
                        <template #actions>
                            <jet-action-message :on="newRepairForm.recentlySuccessful" class="mr-3">
                                ذخیره شد.
                            </jet-action-message>

                            <jet-button :class="{ 'opacity-25': newRepairForm.processing }"
                                        :disabled="newRepairForm.processing">
                                ذخیره
                            </jet-button>
                        </template>
                    </jet-form-section>
                </div>
            </div>
        </template>
    </guest>
</template>

<script>
import Guest from "@/Layouts/Guest";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetButton from "@/Jetstream/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";

export default {
    name: "Create",
    components: {
        Guest,
        JetActionMessage,
        JetButton,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        JetSectionBorder,
        datePicker: VuePersianDatetimePicker
    },
    props: {
        deviceTypes: Array,
        psps: Array,
        banks: Array,
        repairTypes: Array,
        accessories: Array,
    },
    data() {
        return {
            guarantee_end: '',
            newRepairForm: this.$inertia.form({
                '_method': 'POST',
                device_type_id: '',
                psp_id: '',
                bank_id: '',
                serial: '',
                guarantee_end: '',
                name: '',
                business_name: '',
                mobile: '',
                national_code: '',
                repairTypeList: [],
                description: '',
                status: 1,
                accessories: []
            }, {
                bag: 'newRepairForm',
                resetOnSuccess: false
            })
        }
    },
    methods: {
        submitNewRepairForm() {
            this.newRepairForm.post(route('public.repairs.store')).then(response => {
                if (!this.newRepairForm.hasErrors()) {

                }
            })
        },
        selectGuaranteeEnd(e) {
            this.newRepairForm.guarantee_end = e.format('YYYY/MM/DD');
        }
    }
}
</script>

<style scoped>

</style>
