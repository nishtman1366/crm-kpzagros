<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست ها / ثبت پذیرنده جدید</template>
        <template #dashboardContent>
            <ProfileSteps :step="1"
                          :profile-id="profile.id"
                          :edit="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER' ? true : false"
            >
            </ProfileSteps>
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6 bg-gray-300 rounded-lg">
                    <div class="md:col-span-1 m-2">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">اطلاعات مشتری</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات شناسنامه ای پذیرنده و در صورت حقوقی بودن اطلاعات نماینده پذیرنده را
                                وارد نمایید.
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid md:grid-cols-6 gap-6 ">
                                    <div class="col-6 sm:col-span-6">
                                        <div>
                                            <legend class="text-base font-medium text-gray-900">نوع مشتری:</legend>
                                            <p class="text-sm text-gray-500">در صورتی که مشتری یک شخص است "حقیقی" و در
                                                صورتی که یک شرکت است "حقوقی" را انتخاب کنید.</p>
                                        </div>
                                        <div class="grid grid-cols-2 md:grid-cols-6">
                                            <div class="flex items-center">
                                                <input id="type_person"
                                                       name="type"
                                                       type="radio"
                                                       v-model="customerType"
                                                       value="PERSON"
                                                       class="shadow-sm focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 border ml-2"
                                                >
                                                <label for="type_person"
                                                       class="ml-3 block text-sm font-medium text-gray-700">
                                                    حقیقی
                                                </label>
                                            </div>
                                            <div class="flex items-center">
                                                <input id="type_organization"
                                                       name="type"
                                                       type="radio"
                                                       v-model="customerType"
                                                       value="ORGANIZATION"
                                                       class="shadow-sm focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 border ml-2"
                                                >
                                                <label for="type_organization"
                                                       class="ml-3 block text-sm font-medium text-gray-700">
                                                    حقوقی
                                                </label>
                                            </div>
                                        </div>
                                        <jet-input-error :message="customerForm.error('type')" class="mt-2"/>
                                    </div>
                                    <div v-if="customerType==='ORGANIZATION'"
                                         class="grid md:grid-cols-6 sm:col-span-6 gap-6">
                                        <div class="col-3 sm:col-span-3">
                                            <label for="company_name"
                                                   class="block text-sm font-medium text-gray-700">
                                                نام شرکت:
                                            </label>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="نام شرکت"
                                                   ref="company_name"
                                                   id="company_name"
                                                   v-model="customerForm.company_name"/>
                                            <jet-input-error :message="customerForm.error('company_name')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-3 sm:col-span-3">
                                            <label for="company_name_english"
                                                   class="block text-sm font-medium text-gray-700">
                                                نام شرکت (انگلیسی):
                                            </label>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="نام شرکت (انگلیسی)"
                                                   ref="company_name_english"
                                                   id="company_name_english"
                                                   v-model="customerForm.company_name_english"/>
                                            <jet-input-error :message="customerForm.error('company_name_english')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-3 sm:col-span-3">
                                            <label for="business_name"
                                                   class="block text-sm font-medium text-gray-700">
                                                نام تجاری:
                                            </label>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="نام تجاری"
                                                   ref="business_name"
                                                   id="business_name"
                                                   v-model="customerForm.business_name"/>
                                            <jet-input-error :message="customerForm.error('business_name')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-3 sm:col-span-3">
                                            <label for="company_reg_date"
                                                   class="block text-sm font-medium text-gray-700">
                                                تاریخ ثبت:
                                            </label>
                                            <date-picker
                                                ref="reg_date_cal"
                                                input-format="YYYY-MM-DD"
                                                format="jYYYY/jMM/jDD"
                                                @change="selectRegDate"
                                                element="reg_date"
                                                v-model="regDate">
                                            </date-picker>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="تاریخ ثبت"
                                                   ref="reg_date"
                                                   id="reg_date"
                                                   v-model="regDate"
                                                   readonly/>
                                            <jet-input-error :message="customerForm.error('reg_date')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-3 sm:col-span-3">
                                            <label for="reg_code"
                                                   class="block text-sm font-medium text-gray-700">
                                                شماره ثبت:
                                            </label>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="شماره ثبت"
                                                   ref="reg_code"
                                                   id="reg_code"
                                                   v-model="customerForm.reg_code"/>
                                            <jet-input-error :message="customerForm.error('reg_code')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-3 sm:col-span-3">
                                            <label for="company_national_code"
                                                   class="block text-sm font-medium text-gray-700">
                                                شناسه ملی:
                                            </label>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="شناسه ملی"
                                                   ref="company_national_code"
                                                   id="company_national_code"
                                                   v-model="customerForm.company_national_code"/>
                                            <jet-input-error :message="customerForm.error('company_national_code')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-2 sm:col-span-2">
                                            <div
                                                class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                <div class="space-y-1 text-center">
                                                    <svg v-if="imageFiles.asasnameFilePreview===''"
                                                         class="mx-auto h-12 w-12 text-gray-400"
                                                         stroke="currentColor"
                                                         fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                        <path
                                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"/>
                                                    </svg>
                                                    <img v-else :src="imageFiles.asasnameFilePreview">
                                                    <div class="flex text-sm text-gray-600">
                                                        <label for="asasname_file"
                                                               class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                            <span>انتخاب فایل</span>
                                                            <input id="asasname_file"
                                                                   name="asasname_file"
                                                                   type="file"
                                                                   @change="onAsasnameFileChange"
                                                                   class="sr-only">
                                                        </label>
                                                    </div>
                                                    <p class="text-xs text-gray-500">
                                                        تصویر اساسنامه
                                                    </p>
                                                    <jet-input-error
                                                        :message="customerForm.error('asasname_file')"
                                                        class="mt-2"/>
                                                    <jet-input-error
                                                        :message="fileUploadErrors.asasname_file"
                                                        class="mt-2"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2 sm:col-span-2">
                                            <div
                                                class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                <div class="space-y-1 text-center">
                                                    <svg v-if="imageFiles.agahiFile1Preview===''"
                                                         class="mx-auto h-12 w-12 text-gray-400"
                                                         stroke="currentColor"
                                                         fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                        <path
                                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"/>
                                                    </svg>
                                                    <img v-else :src="imageFiles.agahiFile1Preview">
                                                    <div class="flex text-sm text-gray-600">
                                                        <label for="agahi_file_1"
                                                               class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                            <span>انتخاب فایل</span>
                                                            <input id="agahi_file_1"
                                                                   name="agahi_file_1"
                                                                   type="file"
                                                                   @change="onAgahiFile1Change"
                                                                   class="sr-only">
                                                        </label>
                                                    </div>
                                                    <p class="text-xs text-gray-500">
                                                        تصویر آگهی ثبتی
                                                    </p>
                                                    <jet-input-error
                                                        :message="customerForm.error('agahi_file_1')"
                                                        class="mt-2"/>
                                                    <jet-input-error
                                                        :message="fileUploadErrors.agahi_file_1"
                                                        class="mt-2"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-2 sm:col-span-2">
                                            <div
                                                class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                <div class="space-y-1 text-center">
                                                    <svg v-if="imageFiles.agahiFile2Preview===''"
                                                         class="mx-auto h-12 w-12 text-gray-400"
                                                         stroke="currentColor"
                                                         fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                        <path
                                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"/>
                                                    </svg>
                                                    <img v-else :src="imageFiles.agahiFile2Preview">
                                                    <div class="flex text-sm text-gray-600">
                                                        <label for="agahi_file_2"
                                                               class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                            <span>انتخاب فایل</span>
                                                            <input id="agahi_file_2"
                                                                   name="agahi_file_2"
                                                                   type="file"
                                                                   @change="onAgahiFile2Change"
                                                                   class="sr-only">
                                                        </label>
                                                    </div>
                                                    <p class="text-xs text-gray-500">
                                                        تصویر آگهی تغییرات
                                                    </p>
                                                    <jet-input-error
                                                        :message="customerForm.error('agahi_file_2')"
                                                        class="mt-2"/>
                                                    <jet-input-error
                                                        :message="fileUploadErrors.agahi_file_2"
                                                        class="mt-2"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="hidden col-6 sm:col-span-6 sm:block" aria-hidden="true">
                                            <div class="py-5">
                                                <div class="border-t border-gray-200"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="first_name" class="block text-sm font-medium text-gray-700">
                                            نام:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام"
                                               ref="first_name"
                                               id="first_name"
                                               v-model="customerForm.first_name"/>
                                        <jet-input-error :message="customerForm.error('first_name')" class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="first_name_english"
                                               class="block text-sm font-medium text-gray-700">
                                            نام (انگلیسی):
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام (انگلیسی)"
                                               ref="first_name_english"
                                               id="first_name_english"
                                               v-model="customerForm.first_name_english"/>
                                        <jet-input-error :message="customerForm.error('first_name_english')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="last_name" class="block text-sm font-medium text-gray-700">
                                            نام خانوادگی:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام خانوادگی"
                                               ref="last_name"
                                               id="last_name"
                                               v-model="customerForm.last_name"/>
                                        <jet-input-error :message="customerForm.error('last_name')" class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="last_name_english"
                                               class="block text-sm font-medium text-gray-700">
                                            نام خانوادگی (انگلیسی):
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام خانوادگی (انگلیسی)"
                                               ref="last_name_english"
                                               id="last_name_english"
                                               v-model="customerForm.last_name_english"/>
                                        <jet-input-error :message="customerForm.error('last_name_english')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="father" class="block text-sm font-medium text-gray-700">
                                            نام پدر:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام پدر"
                                               ref="father"
                                               id="father"
                                               v-model="customerForm.father"/>
                                        <jet-input-error :message="customerForm.error('father')" class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="father_english" class="block text-sm font-medium text-gray-700">
                                            نام پدر (انگلیسی):
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام پدر (انگلیسی)"
                                               ref="father_english"
                                               id="father_english"
                                               v-model="customerForm.father_english"/>
                                        <jet-input-error :message="customerForm.error('father_english')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="national_code" class="block text-sm font-medium text-gray-700">
                                            کد ملی:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="کد ملی"
                                               ref="national_code"
                                               id="national_code"
                                               v-model="customerForm.national_code"/>
                                        <jet-input-error :message="customerForm.error('national_code')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="id_code" class="block text-sm font-medium text-gray-700">
                                            شماره شناسنامه:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="شماره شناسنامه"
                                               ref="id_code"
                                               id="id_code"
                                               v-model="customerForm.id_code"/>
                                        <jet-input-error :message="customerForm.error('id_code')" class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="birthday" class="block text-sm font-medium text-gray-700">
                                            تاریخ تولد:
                                        </label>
                                        <date-picker
                                            ref="birthday_cal"
                                            input-format="YYYY-MM-DD"
                                            format="jYYYY/jMM/jDD"
                                            @change="selectBirthday"
                                            element="birthday"
                                            v-model="birthday">
                                        </date-picker>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تاریخ تولد"
                                               ref="birthday"
                                               id="birthday"
                                               v-model="birthday"
                                               readonly="true"/>
                                        <jet-input-error :message="customerForm.error('birthday')" class="mt-2"/>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="gender" class="block text-sm font-medium text-gray-700">
                                            جنسیت:
                                        </label>
                                        <div class="col-6 sm:col-span-6">
                                            <div class="grid grid-cols-2 sm:grid-cols-6">
                                                <div class="flex items-center">
                                                    <input id="gender_male"
                                                           name="gender"
                                                           type="radio"
                                                           v-model="customerForm.gender"
                                                           value="male"
                                                           class="shadow-sm focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 border ml-2"
                                                    >
                                                    <label for="gender_male"
                                                           class="ml-3 block text-sm font-medium text-gray-700">
                                                        مرد
                                                    </label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input id="gender_female"
                                                           name="gender"
                                                           type="radio"
                                                           v-model="customerForm.gender"
                                                           value="female"
                                                           class="shadow-sm focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 border ml-2"
                                                    >
                                                    <label for="gender_female"
                                                           class="ml-3 block text-sm font-medium text-gray-700">
                                                        زن
                                                    </label>
                                                </div>
                                            </div>
                                            <jet-input-error :message="customerForm.error('gender')" class="mt-2"/>
                                        </div>
                                    </div>
                                    <div class="col-3 sm:col-span-3">
                                        <label for="mobile" class="block text-sm font-medium text-gray-700">
                                            تلفن همراه:
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تلفن همراه"
                                               ref="mobile"
                                               id="mobile"
                                               v-model="customerForm.mobile"/>
                                        <jet-input-error :message="customerForm.error('mobile')" class="mt-2"/>
                                    </div>
                                    <div class="col-6 sm:col-span-6">
                                        <div
                                            class="grid md:grid-cols-6 sm:col-span-6 gap-6">
                                            <div class="sm:col-span-2">
                                                <div
                                                    class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                    <div class="space-y-1 text-center">
                                                        <svg v-if="imageFiles.nationalFile1Preview===''"
                                                             class="mx-auto h-12 w-12 text-gray-400"
                                                             stroke="currentColor"
                                                             fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                            <path
                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"/>
                                                        </svg>
                                                        <img v-else :src="imageFiles.nationalFile1Preview">
                                                        <div class="flex text-sm text-gray-600">
                                                            <label for="national_card_file_1"
                                                                   class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                                <span>انتخاب فایل</span>
                                                                <input id="national_card_file_1"
                                                                       name="national_card_file_1"
                                                                       type="file"
                                                                       @change="onNationalFile1Change"
                                                                       class="sr-only">
                                                            </label>
                                                        </div>
                                                        <p class="text-xs text-gray-500">
                                                            تصویر روی کارت ملی
                                                        </p>
                                                        <jet-input-error
                                                            :message="customerForm.error('national_card_file_1')"
                                                            class="mt-2"/>
                                                        <jet-input-error
                                                            :message="fileUploadErrors.national_card_file_1"
                                                            class="mt-2"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <div
                                                    class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                    <div class="space-y-1 text-center">
                                                        <svg v-if="imageFiles.nationalFile2Preview===''"
                                                             class="mx-auto h-12 w-12 text-gray-400"
                                                             stroke="currentColor"
                                                             fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                            <path
                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"/>
                                                        </svg>
                                                        <img v-else :src="imageFiles.nationalFile2Preview">
                                                        <div class="flex text-sm text-gray-600">
                                                            <label for="national_card_file_2"
                                                                   class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                                <span>انتخاب فایل</span>
                                                                <input id="national_card_file_2"
                                                                       name="national_card_file_2"
                                                                       type="file"
                                                                       @change="onNationalFile2Change"
                                                                       class="sr-only">
                                                            </label>
                                                        </div>
                                                        <p class="text-xs text-gray-500">
                                                            تصویر پشت کارت ملی
                                                        </p>
                                                        <jet-input-error
                                                            :message="customerForm.error('national_card_file_2')"
                                                            class="mt-2"/>
                                                        <jet-input-error
                                                            :message="fileUploadErrors.national_card_file_2"
                                                            class="mt-2"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <div
                                                    class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                                    <div class="space-y-1 text-center">
                                                        <svg v-if="imageFiles.idFilePreview===''"
                                                             class="mx-auto h-12 w-12 text-gray-400"
                                                             stroke="currentColor"
                                                             fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                            <path
                                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                                stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round"/>
                                                        </svg>
                                                        <img v-else :src="imageFiles.idFilePreview">
                                                        <div class="flex text-sm text-gray-600">
                                                            <label for="id_file"
                                                                   class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                                <span>انتخاب فایل</span>
                                                                <input id="id_file"
                                                                       name="id_file"
                                                                       type="file"
                                                                       @change="onIdFileChange"
                                                                       class="sr-only">
                                                            </label>
                                                        </div>
                                                        <p class="text-xs text-gray-500">
                                                            تصویر شناسنامه
                                                        </p>
                                                        <jet-input-error
                                                            :message="customerForm.error('id_file')"
                                                            class="mt-2"/>
                                                        <jet-input-error
                                                            :message="fileUploadErrors.id_file"
                                                            class="mt-2"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 text-left sm:px-6">
                                <button type="submit"
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        v-on:click="submitCustomerForm">
                                    ذخیره اطلاعات
                                </button>
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
    import JetInput from '@/Jetstream/Input'
    import JetInputError from '@/Jetstream/InputError'
    import VuePersianDatetimePicker from 'vue-persian-datetime-picker';
    import ProfileSteps from "@/Pages/Dashboard/Components/ProfileSteps";

    export default {
        name: "CreateCustomer",
        components: {Dashboard, JetInput, JetInputError, datePicker: VuePersianDatetimePicker, ProfileSteps},
        props: {
            profile: Object,
            profileId: Number
        },
        data() {
            return {
                customerType: 'PERSON',
                imageFiles: {
                    nationalFile1Preview: '',
                    nationalFile2Preview: '',
                    idFilePreview: '',
                    agahiFile1Preview: '',
                    agahiFile2Preview: '',
                    asasnameFilePreview: '',
                },
                fileUploadErrors: {
                    national_card_file_1: '',
                    national_card_file_2: '',
                    id_file: '',
                    asasname_file: '',
                    agahi_file_1: '',
                    agahi_file_2: '',
                },

                birthday: '',
                regDate: '',
                customerForm: this.$inertia.form({
                    '_method': 'POST',
                    profile_id: this.profile.id,
                    type: '',
                    first_name: '',
                    first_name_english: '',
                    last_name: '',
                    last_name_english: '',
                    national_code: '',
                    id_code: '',
                    father: '',
                    father_english: '',
                    gender: '',
                    mobile: '',
                    birthday: '',
                    national_card_file_1: '',
                    national_card_file_2: '',
                    id_file: '',
                    company_name: '',
                    company_name_english: '',
                    business_name: '',
                    reg_date: '',
                    reg_code: '',
                    company_national_code: '',
                    asasname_file: '',
                    agahi_file_1: '',
                    agahi_file_2: '',
                }, {
                    bag: 'customerForm'
                })
            }
        },
        methods: {
            onNationalFile1Change(e) {
                const file = e.target.files[0];
                if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                    this.fileUploadErrors.national_card_file_1 = 'فایل انتخاب شده نباید بیشتر از '
                        + this.$page.configs.maximumUploadSize
                        + 'کیلوبایت باشد.';
                    return;
                }
                this.fileUploadErrors.national_card_file_1 = '';
                this.customerForm.national_card_file_1 = e.target.files[0];
                this.imageFiles.nationalFile1Preview = URL.createObjectURL(file);
            },
            onNationalFile2Change(e) {
                const file = e.target.files[0];
                if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                    this.fileUploadErrors.national_card_file_2 = 'فایل انتخاب شده نباید بیشتر از '
                        + this.$page.configs.maximumUploadSize
                        + 'کیلوبایت باشد.';
                    return;
                }
                this.fileUploadErrors.national_card_file_2 = '';
                this.customerForm.national_card_file_2 = e.target.files[0];
                this.imageFiles.nationalFile2Preview = URL.createObjectURL(file);
            },
            onIdFileChange(e) {
                const file = e.target.files[0];
                if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                    this.fileUploadErrors.id_file = 'فایل انتخاب شده نباید بیشتر از '
                        + this.$page.configs.maximumUploadSize
                        + 'کیلوبایت باشد.';
                    return;
                }
                this.fileUploadErrors.id_file = '';
                this.customerForm.id_file = e.target.files[0];
                this.imageFiles.idFilePreview = URL.createObjectURL(file);
            },
            onAsasnameFileChange(e) {
                const file = e.target.files[0];
                if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                    this.fileUploadErrors.asasname_file = 'فایل انتخاب شده نباید بیشتر از '
                        + this.$page.configs.maximumUploadSize
                        + 'کیلوبایت باشد.';
                    return;
                }
                this.fileUploadErrors.asasname_file = '';
                this.customerForm.asasname_file = e.target.files[0];
                this.imageFiles.asasnameFilePreview = URL.createObjectURL(file);
            },
            onAgahiFile1Change(e) {
                const file = e.target.files[0];
                if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                    this.fileUploadErrors.agahi_file_1 = 'فایل انتخاب شده نباید بیشتر از '
                        + this.$page.configs.maximumUploadSize
                        + 'کیلوبایت باشد.';
                    return;
                }
                this.fileUploadErrors.agahi_file_1 = '';
                this.customerForm.agahi_file_1 = e.target.files[0];
                this.imageFiles.agahiFile1Preview = URL.createObjectURL(file);
            },
            onAgahiFile2Change(e) {
                const file = e.target.files[0];
                if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                    this.fileUploadErrors.agahi_file_2 = 'فایل انتخاب شده نباید بیشتر از '
                        + this.$page.configs.maximumUploadSize
                        + 'کیلوبایت باشد.';
                    return;
                }
                this.fileUploadErrors.agahi_file_2 = '';
                this.customerForm.agahi_file_2 = e.target.files[0];
                this.imageFiles.agahiFile2Preview = URL.createObjectURL(file);
            },
            resetFileUploadErrors() {
                this.fileUploadErrors = {
                    national_card_file_1: '',
                    national_card_file_2: '',
                    id_file: '',
                    asasname_file: '',
                    agahi_file_1: '',
                    agahi_file_2: '',
                };
            },
            selectBirthday(e) {
                this.customerForm.birthday = e.format('YYYY/MM/DD');
            },
            selectRegDate(e) {
                this.customerForm.reg_date = e.format('YYYY/MM/DD');
            },
            submitCustomerForm() {
                this.customerForm.type = this.customerType;
                this.resetFileUploadErrors();
                this.customerForm.post(route('dashboard.profiles.customers.store', {profileId: this.profileId}
                )).then(response => {

                })
            },
        }
    }
</script>

<style scoped>

</style>
