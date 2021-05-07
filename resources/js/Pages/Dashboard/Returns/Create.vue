<template>
    <Dashboard>
        <template #breadcrumb> / درخواست های عودت دستگاه / ثبت درخواست جدید</template>
        <template #dashboardContent>
            <div>
                <div class="p-3 bg-gray-300">
                    <jet-form-section @submitted="submitNewReturnForm">
                        <template #title>
                            ثبت درخواست عودت دستگاه
                        </template>

                        <template #description>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات درخواست عودت دستگاه را ثبت نمایید.
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
                                <span class="inline-block font-bold mt-2 ml-1">مبلغ و فااکتور خرید دستگاه:</span>
                                در این بخش مبلغی که بابت فروش دستگاه دریافت شده است و همچنین فایل تصویر فاکتور فروش را
                                وارد نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">اقلام همراه دستگاه:</span>
                                چنانچه اقلامی همراه دستگاه عودت داده شده وجود دارد در این بخش انتخاب نمایید.
                            </p>
                            <p class="text-justify">
                                <span class="inline-block font-bold mt-2 ml-1">توضیحات تکمیلی:</span>
                                چنانچه موردی جهت توضیح در خصوص علت عودت دستگاه وجود دارد میتوانید در این بخش وارد
                                نمایید.
                            </p>
                        </template>
                        <template #form>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="device_type_id" value="مدل دستگاه"/>
                                <select id="device_type_id" name="device_type_id" ref="device_type_id"
                                        v-model="newReturnForm.device_type_id"
                                        autocomplete="device_type_id"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                    <option v-for="type in deviceTypes" :key="type.id"
                                            :value="type.id">
                                        {{type.name}}
                                    </option>
                                </select>
                                <jet-input-error :message="newReturnForm.error('device_type_id')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="serial" value="سریال دستگاه"/>
                                <jet-input id="serial"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="newReturnForm.serial"
                                           ref="serial"
                                           autocomplete="serial"/>
                                <jet-input-error :message="newReturnForm.error('serial')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="name" value="نام پذیرنده"/>
                                <jet-input id="name"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="newReturnForm.name"
                                           ref="name"
                                           autocomplete="name"/>
                                <jet-input-error :message="newReturnForm.error('name')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="national_code" value="کد ملی پذیرنده"/>
                                <jet-input id="national_code"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="newReturnForm.national_code"
                                           ref="national_code"
                                           autocomplete="national_code"/>
                                <jet-input-error :message="newReturnForm.error('national_code')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="mobile" value="تلفن همراه پذیرنده"/>
                                <jet-input id="mobile"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="newReturnForm.mobile"
                                           ref="mobile"
                                           autocomplete="mobile"/>
                                <jet-input-error :message="newReturnForm.error('mobile')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <div class="col-span-3">
                                <jet-label for="amount" value="مبلغ خرید"/>
                                <jet-input id="amount"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="newReturnForm.amount"
                                           ref="mobile"
                                           autocomplete="mobile"/>
                                <jet-input-error :message="newReturnForm.error('amount')" class="mt-2"/>
                            </div>
                            <div class="col-span-3">
                                <div
                                    class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg v-if="imageFiles.faktorFilePreview===''"
                                             class="mx-auto h-12 w-12 text-gray-400"
                                             stroke="currentColor"
                                             fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                        <img v-else :src="imageFiles.faktorFilePreview">
                                        <div class="flex text-sm text-gray-600">
                                            <label for="faktor_file"
                                                   class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>انتخاب فایل</span>
                                                <input id="faktor_file"
                                                       name="faktor_file"
                                                       type="file"
                                                       @change="onFaktorFileChange"
                                                       class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            تصویر فاکتور خرید
                                        </p>
                                        <jet-input-error
                                            :message="newReturnForm.error('file')"
                                            class="mt-2"/>
                                        <jet-input-error
                                            :message="fileUploadErrors.faktor"
                                            class="mt-2"/>
                                    </div>
                                </div>
                                <jet-input-error :message="newReturnForm.error('accessories')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-label for="accessories" value="اقلام همراه"/>
                                <div class="flex">
                                    <jet-label v-for="item in accessories" :key="item.id" class="flex items-center"
                                               :for="'accessories_'+item.id">
                                        <input class="form-checkbox" type="checkbox" :name="'accessories_'+item.id"
                                               :id="'accessories_'+item.id"
                                               v-model="newReturnForm.accessories"
                                               :value="item.id"/>
                                        <span class="mr- ml-3">{{item.name}}</span>
                                    </jet-label>
                                </div>
                                <jet-input-error :message="newReturnForm.error('accessories')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <jet-label for="description" value="توضیحات تکمیلی"/>
                                <textarea id="description"
                                          type="description"
                                          class="form-input mt-1 block w-full"
                                          v-model="newReturnForm.description"
                                          ref="description"
                                          autocomplete="description" rows="3" cols="60"></textarea>
                                <jet-input-error :message="newReturnForm.error('description')" class="mt-2"/>
                            </div>
                        </template>
                        <template #actions>
                            <jet-action-message :on="newReturnForm.recentlySuccessful" class="mr-3">
                                ذخیره شد.
                            </jet-action-message>

                            <jet-button :class="{ 'opacity-25': newReturnForm.processing }"
                                        :disabled="newReturnForm.processing">
                                ذخیره
                            </jet-button>
                        </template>
                    </jet-form-section>
                </div>
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
    import VuePersianDatetimePicker from 'vue-persian-datetime-picker';

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
            datePicker: VuePersianDatetimePicker
        },
        props: {
            deviceTypes: Array,
            accessories: Array,
        },
        data() {
            return {
                imageFiles: {
                    faktorFilePreview: null,
                },
                fileUploadErrors: {
                    faktorFile: null,
                },
                newReturnForm: this.$inertia.form({
                    '_method': 'POST',
                    device_type_id: '',
                    serial: '',
                    name: '',
                    mobile: '',
                    national_code: '',
                    description: '',
                    amount: '',
                    file: '',
                    status: 1,
                    accessories: []
                }, {
                    bag: 'newReturnForm',
                    resetOnSuccess: false
                }),
            }
        },
        methods: {
            submitNewReturnForm() {
                this.newReturnForm.post(route('dashboard.returns.store')).then(response => {
                    if (!this.newReturnForm.hasErrors()) {

                    }
                })
            },
            onFaktorFileChange(e) {
                const file = e.target.files[0];
                if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                    this.fileUploadErrors.faktor = 'فایل انتخاب شده نباید بیشتر از '
                        + this.$page.configs.maximumUploadSize
                        + 'کیلوبایت باشد.';
                    return;
                }
                this.fileUploadErrors.faktor = '';
                this.newReturnForm.file = e.target.files[0];
                this.imageFiles.faktorFilePreview = URL.createObjectURL(file);
            },
        }
    }
</script>

<style scoped>

</style>
