<template>
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-3">
        <div class="col-span-3 lg:col-span-2">
            <div class="grid md:grid-cols-6 gap-6">
                <div class="col-6 sm:col-span-6">
                    <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                        مشخصات پرونده</p>
                    <p v-show="$page.message"
                       class="bg-green-300 rounded mx-2 my-3 p-3">
                        {{ $page.message }}</p>
                    <jet-button
                        :class="{'bg-blue-600':profileTypeForm.type==='REGISTER'}"
                        @click.native="profileTypeForm.type='REGISTER'"
                        class="bg-blue-300 hover:bg-blue-400 active:bg-blue-800"
                        :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                        ثبت پرونده
                        جدید
                    </jet-button>
                    <jet-button :class="{'bg-red-600':profileTypeForm.type==='EDIT'}"
                                @click.native="profileTypeForm.type='EDIT'"
                                class="bg-red-300 hover:bg-red-400 active:bg-red-800"
                                :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                        تغییر مشخصات پرونده
                    </jet-button>
                    <jet-button
                        :class="{'bg-green-600':profileTypeForm.type==='TRANSFER'}"
                        @click.native="profileTypeForm.type='TRANSFER'"
                        class="bg-green-300 hover:bg-green-400 active:bg-green-800"
                        :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                        ثبت
                        تغییر مالکیت
                    </jet-button>
                    <p v-if="profileTypeForm.type==='TRANSFER'" class="text-lg mt-3">
                        اطلاعات پذیرنده
                        قبلی:</p>
                    <div v-show="profileTypeForm.type==='TRANSFER'"
                         class="grid md:grid-cols-2 gap-3">
                        <div>
                            <div>
                                <jet-label>نام</jet-label>
                                <jet-input tye="text"
                                           class="block w-full mt-3"
                                           name="previous_name"
                                           ref="previous_name"
                                           id="previous_name"
                                           :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0"
                                           v-model="profileTypeForm.previous_name"/>
                                <jet-input-error
                                    :message="profileTypeForm.error('previous_name')"
                                    class="mt-2"/>
                            </div>
                            <div>
                                <jet-label>کد ملی</jet-label>
                                <jet-input tye="text"
                                           class="block w-full mt-3"
                                           name="previous_national_code"
                                           ref="previous_national_code"
                                           id="previous_national_code"
                                           :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0"
                                           v-model="profileTypeForm.previous_national_code"/>
                                <jet-input-error
                                    :message="profileTypeForm.error('previous_national_code')"
                                    class="mt-2"/>
                            </div>
                            <div>
                                <jet-label>تلفن همراه</jet-label>
                                <jet-input tye="text"
                                           class="block w-full mt-3"
                                           name="previous_mobile"
                                           ref="previous_mobile"
                                           id="previous_mobile"
                                           :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0"
                                           v-model="profileTypeForm.previous_mobile"/>
                                <jet-input-error
                                    :message="profileTypeForm.error('previous_mobile')"
                                    class="mt-2"/>
                            </div>
                            <div>
                                <p class="text-red-700 font-bold">حتما پس از ثبت
                                    اطلاعات، پرونده را
                                    ثبت
                                    نهایی نمایید.</p>
                            </div>
                        </div>
                        <div>
                            <div>
                                <div
                                    class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg v-if="imageFiles.transferFilePreview===''"
                                             class="mx-auto h-12 w-12 text-gray-400"
                                             stroke="currentColor"
                                             fill="none" viewBox="0 0 48 48"
                                             aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                        <img v-else
                                             :src="imageFiles.transferFilePreview">
                                        <div class="flex text-sm text-gray-600">
                                            <label for="transfer_file"
                                                   class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>انتخاب فایل</span>
                                                <input id="transfer_file"
                                                       name="transfer_file"
                                                       type="file"
                                                       :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0"
                                                       @change="onTransferFileChange"
                                                       class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            تصویر فرم انتقال مالکیت
                                        </p>
                                        <jet-input-error
                                            :message="profileTypeForm.error('transfer_file')"
                                            class="mt-2"/>
                                        <jet-input-error
                                            :message="fileUploadErrors.transfer_file"
                                            class="mt-2"/>
                                    </div>
                                </div>
                                <div
                                    class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                    <div class="space-y-1 text-center">
                                        <svg
                                            v-if="imageFiles.transferPaymentFilePreview===''"
                                            class="mx-auto h-12 w-12 text-gray-400"
                                            stroke="currentColor"
                                            fill="none" viewBox="0 0 48 48"
                                            aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"/>
                                        </svg>
                                        <img v-else
                                             :src="imageFiles.transferPaymentFilePreview">
                                        <div class="flex text-sm text-gray-600">
                                            <label for="transfer_payment_file"
                                                   class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>انتخاب فایل</span>
                                                <input id="transfer_payment_file"
                                                       name="transfer_payment_file"
                                                       type="file"
                                                       :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0"
                                                       @change="onTransferPaymentFileChange"
                                                       class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">
                                            تصویر رسید پرداخت وجه
                                        </p>
                                        <jet-input-error
                                            :message="profileTypeForm.error('transfer_payment_file')"
                                            class="mt-2"/>
                                        <jet-input-error
                                            :message="fileUploadErrors.transfer_payment_file"
                                            class="mt-2"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-left">
                        <jet-button @click.native="submitProfileTypeForm"
                                    :disabled="$page.user.level!=='OFFICE' && $page.user.level!=='ADMIN' && $page.user.level!=='SUPERUSER' && profile.status!=0">
                            ذخیره
                        </jet-button>
                    </div>
                </div>
            </div>
        </div>
        <div>
            <p class="text-white text-center text-lg m-2 py-2 mb-4 bg-indigo-600 rounded">
                گزارش پرونده</p>
            <div class="h-96 overflow-y-auto">
                <div v-for="error in profile.messages"
                     :class="'border-'+error.color+'-600 bg-'+error.color+'-100'"
                     class="my-1 p-3 border-r-4 col-2 sm:col-span-8">
                    <span class="text-xs text-gray-600">{{ error.jDate }}</span>
                    <p :class="'text-'+error.color+'-900'">{{ error.title }}</p>
                    <p>{{ error.message }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "General"
}
</script>

<style scoped>

</style>
