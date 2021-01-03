<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست ها / ثبت درخواست جدید</template>
        <template #dashboardContent>
            <div>
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
                            چنانچه موردی جهت توضیح در خصوص ایرادات پیش آمده وجود دارد میتوانید در این بخش وارد نمایید.
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
                                    {{type.name}}
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
                            <jet-label for="model_id" value="سرویس دهنده"/>
                            <select id="psp_id" name="psp_id" ref="psp_id"
                                    v-model="newRepairForm.psp_id"
                                    autocomplete="psp_id"
                                    class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                <option v-for="psp in psps" :key="psp.id"
                                        :value="psp.id">
                                    {{psp.name}}
                                </option>
                            </select>
                            <jet-input-error :message="newRepairForm.error('psp_id')" class="mt-2"/>
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
                                       :value="type.id">{{type.name}}
                            </jet-label>
                        </div>
                        <div class="col-span-6"><jet-input-error :message="newRepairForm.error('repairTypeList')" class="mt-2"/></div>
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
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetActionMessage from '@/Jetstream/ActionMessage'
    import JetButton from '@/Jetstream/Button'
    import JetFormSection from '@/Jetstream/FormSection'
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import JetLabel from '@/Jetstream/Label'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'
    import JetSectionBorder from '@/Jetstream/SectionBorder'

    export default {
        name: "Create",
        components: {
            Dashboard,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            JetSectionBorder,
        },
        props: {
            deviceTypes: Array,
            psps: Array,
            repairTypes: Array,
        },
        data() {
            return {
                newRepairForm: this.$inertia.form({
                    '_method': 'POST',
                    user_id: this.$page.user.id,
                    device_type_id: '',
                    psp_id: '',
                    serial: '',
                    name: '',
                    mobile: '',
                    national_code: '',
                    repairTypeList: [],
                    description: '',
                    status: 1,
                }, {
                    bag: 'newRepairForm',
                })
            }
        },
        methods: {
            submitNewRepairForm() {
                this.newRepairForm.post(route('dashboard.repairs.store')).then(response => {
                    if (!this.newRepairForm.hasErrors()) {

                    }
                })
            }
        }
    }
</script>

<style scoped>

</style>
