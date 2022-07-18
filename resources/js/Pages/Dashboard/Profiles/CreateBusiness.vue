<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست ها / ثبت اطلاعات کسب و کار {{ customer.fullName }}</template>
        <template #dashboardContent>
            <ProfileSteps :step="2"
                          :customer-info="!!profile.customer"
                          :profile-id="profile.id"
                          :edit="(profile.status==0 || profile.status==10 || profile.status==11) || $page.user.level==='ADMIN' || $page.user.level==='OFFICE' || $page.user.level==='SUPERUSER' ? true : false"
            ></ProfileSteps>
            <div>
                <div class="md:grid md:grid-cols-3 md:gap-6 bg-gray-300  rounded-lg">
                    <div class="md:col-span-1 m-2">
                        <div class="px-4 sm:px-0">
                            <h3 class="text-lg font-medium leading-6 text-gray-900">اطلاعات کسب و کار</h3>
                            <p class="mt-1 text-sm text-gray-600">
                                در این بخش اطلاعات مربوط به کسب و کار مشتری را وارد نمایید.
                            </p>
                            <p class="mt-1 text-sm text-gray-600 bg-red-200 text-red-700 px-2 py-1 rounded-md">
                                پر کردن موارد ستاره‌دار (<span class="text-red-500">*</span>) الزامیست
                            </p>
                        </div>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="shadow sm:rounded-md sm:overflow-hidden m-2">
                            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6">
                                        <div class="grid grid-cols-4 gap-2">
                                            <div class="col-span-2 md:col-span-1">
                                                <label for="ostan_id" class="block text-sm font-medium text-gray-700">
                                                    استان<span class="text-red-500">*</span>
                                                </label>
                                                <select id="ostan_id" name="ostan_id" ref="ostan_id"
                                                        v-model="businessForm.ostan_id"
                                                        autocomplete="ostan_id" v-on:change="listShahrestansItems"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب استان</option>
                                                    <option v-for="ostan in ostans" :key="ostan.id"
                                                            :value="ostan.id">
                                                        {{ ostan.name }}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('ostan_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div class="col-span-2 md:col-span-1">
                                                <label for="shahrestan_id"
                                                       class="block text-sm font-medium text-gray-700">
                                                    شهرستان<span class="text-red-500">*</span>
                                                </label>
                                                <select id="shahrestan_id" name="shahrestan_id" ref="shahrestan_id"
                                                        v-model="businessForm.shahrestan_id"
                                                        autocomplete="shahrestan_id"
                                                        v-on:change="listBakhshItems"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب شهرستان</option>
                                                    <option v-for="shahrestan in shahrestanItems"
                                                            :key="shahrestan.id" :value="shahrestan.id">
                                                        {{ shahrestan.name }}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('shahrestan_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div class="col-span-2 md:col-span-1">
                                                <label for="bakhsh_id" class="block text-sm font-medium text-gray-700">
                                                    بخش<span class="text-red-500">*</span>
                                                </label>
                                                <select id="bakhsh_id" name="bakhsh_id" autocomplete="bakhsh_id"
                                                        v-model="businessForm.bakhsh_id"
                                                        ref="bakhsh_id"
                                                        v-on:change="listShahrItems"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب بخش</option>
                                                    <option v-for="bakhsh in bakhshItems"
                                                            :key="bakhsh.id" :value="bakhsh.id">
                                                        {{ bakhsh.name }}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('bakhsh_id')"
                                                                 class="mt-2"/>
                                            </div>
                                            <div class="col-span-2 md:col-span-1">
                                                <label for="shahr_id" class="block text-sm font-medium text-gray-700">
                                                    شهر
                                                </label>
                                                <select id="shahr_id" name="shahr_id" autocomplete="shahr_id"
                                                        v-model="businessForm.shahr_id"
                                                        class="mt-1 block w-full py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                    <option value="0">انتخاب شهر</option>
                                                    <option v-for="shahr in shahrItems"
                                                            :key="shahr.id" :value="shahr.id">
                                                        {{ shahr.name }}
                                                    </option>
                                                </select>
                                                <jet-input-error :message="businessForm.error('shahr_id')"
                                                                 class="mt-2"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="name" class="block text-sm font-medium text-gray-700">
                                            نام کسب و کار<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام کسب و کار"
                                               ref="name"
                                               id="name"
                                               v-model="businessForm.name"/>
                                        <jet-input-error :message="businessForm.error('name')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="name_english" class="block text-sm font-medium text-gray-700">
                                            نام کسب و کار به انگلیسی:<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="نام کسب و کار به انگلیسی"
                                               ref="name_english"
                                               id="name_english"
                                               v-model="businessForm.name_english"/>
                                        <jet-input-error :message="businessForm.error('name_english')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="business_category_code"
                                               class="block text-sm font-medium text-gray-700">
                                            صنف مرتبط<span class="text-red-500">*</span>
                                        </label>
                                        <div>{{ senfText }}</div>
                                        <jet-button @click.native="viewCategories">انتخاب</jet-button>
                                        <jet-input-error
                                            :message="businessForm.error('business_category_code') || businessForm.error('business_subCategory_code')"
                                            class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                            کد پستی<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="کد پستی"
                                               ref="postal_code"
                                               id="postal_code"
                                               v-model="businessForm.postal_code"/>
                                        <jet-input-error :message="businessForm.error('postal_code')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6">
                                        <label for="address" class="block text-sm font-medium text-gray-700">
                                            آدرس<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="آدرس"
                                               ref="address"
                                               id="address"
                                               v-model="businessForm.address"/>
                                        <jet-input-error :message="businessForm.error('address')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                            آدرس ایمیل
                                        </label>
                                        <input type="email"
                                               style="direction:ltr"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="email@site.com"
                                               ref="email"
                                               id="email"
                                               v-model="businessForm.email"/>
                                        <jet-input-error :message="businessForm.error('email')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                            آدرس وبسایت
                                        </label>
                                        <input type="url"
                                               style="direction:ltr"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="https://www.website.com"
                                               ref="website"
                                               id="website"
                                               v-model="businessForm.website"/>
                                        <jet-input-error :message="businessForm.error('website')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-3 md:col-span-2">
                                        <label for="phone_code" class="block text-sm font-medium text-gray-700">
                                            پیش شماره شهرستان<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="پیش شماره شهرستان"
                                               ref="phone_code"
                                               id="phone_code"
                                               v-model="businessForm.phone_code"/>
                                        <jet-input-error :message="businessForm.error('phone_code')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 md:col-span-4">
                                        <label for="phone" class="block text-sm font-medium text-gray-700">
                                            تلفن تماس<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تلفن تماس"
                                               ref="phone"
                                               id="phone"
                                               v-model="businessForm.phone"/>
                                        <jet-input-error :message="businessForm.error('phone')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">
                                            جواز کسب<span class="text-red-500">*</span>
                                        </label>
                                        <jet-button @click.native="hasLicense='YES'"
                                                    :class="hasLicense==='YES' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            دارد
                                        </jet-button>
                                        <jet-button @click.native="hasLicense='NO'"
                                                    :class="hasLicense==='NO' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            ندارد
                                        </jet-button>
                                        <jet-input-error :message="businessForm.error('has_license')" class="mt-2"/>
                                    </div>
                                    <template v-if="hasLicense==='YES'">
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="license_code" class="block text-sm font-medium text-gray-700">
                                                شماره جواز کسب<span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="شماره جواز کسب"
                                                   ref="license_code"
                                                   id="license_code"
                                                   v-model="businessForm.license_code"/>
                                            <jet-input-error :message="businessForm.error('license_code')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="license_date" class="block text-sm font-medium text-gray-700">
                                                تاریخ صدور جواز کسب<span class="text-red-500">*</span>
                                            </label>
                                            <date-picker
                                                ref="regDate_cal"
                                                input-format="YYYY-MM-DD"
                                                format="jYYYY/jMM/jDD"
                                                @change="selectLicenseDate"
                                                element="license_date"
                                                v-model="licenseDate"></date-picker>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="تاریخ صدور جواز کسب"
                                                   ref="license_date"
                                                   id="license_date"
                                                   v-model="licenseDate"
                                                   readonly="true"/>
                                            <jet-input-error :message="businessForm.error('license_date')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="license_start_date"
                                                   class="block text-sm font-medium text-gray-700">
                                                تاریخ انقضای جواز کسب
                                            </label>
                                            <date-picker
                                                ref="regDate_cal"
                                                input-format="YYYY-MM-DD"
                                                format="jYYYY/jMM/jDD"
                                                @change="selectLicenseStartDate"
                                                element="license_start_date"
                                                v-model="licenseStartDate"></date-picker>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="تاریخ انقضای جواز کسب"
                                                   ref="license_start_date"
                                                   id="license_start_date"
                                                   v-model="licenseStartDate"
                                                   readonly="true"/>
                                            <jet-input-error :message="businessForm.error('license_start_date')"
                                                             class="mt-2"/>
                                        </div>
                                    </template>
                                    <div class="col-span-6 md:col-span-3">
                                        <label for="tax_code" class="block text-sm font-medium text-gray-700">
                                            کد مالیاتی<span class="text-red-500">*</span>
                                        </label>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="کد مالیاتی"
                                               ref="tax_code"
                                               id="tax_code"
                                               v-model="businessForm.tax_code"/>
                                        <jet-input-error :message="businessForm.error('tax_code')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700">
                                            وضعیت مالکیت:
                                        </label>
                                        <jet-button @click.native="businessForm.ownership_type='owner'"
                                                    :class="businessForm.ownership_type==='owner' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            مالک
                                        </jet-button>
                                        <jet-button @click.native="businessForm.ownership_type='tenant'"
                                                    :class="businessForm.ownership_type==='tenant' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            مستاجر
                                        </jet-button>
                                        <jet-input-error :message="businessForm.error('has_license')" class="mt-2"/>
                                    </div>
                                    <template v-if="businessForm.ownership_type==='tenant'">
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="license_code" class="block text-sm font-medium text-gray-700">
                                                شماره قرارداد اجاره:
                                            </label>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="شماره جواز کسب"
                                                   ref="rental_contract_number"
                                                   id="rental_contract_number"
                                                   v-model="businessForm.rental_contract_number"/>
                                            <jet-input-error :message="businessForm.error('license_code')"
                                                             class="mt-2"/>
                                        </div>
                                        <div class="col-span-6 sm:col-span-3">
                                            <label for="license_date" class="block text-sm font-medium text-gray-700">
                                                تاریخ پایان اجاره:
                                            </label>
                                            <date-picker
                                                ref="rental_contract_number"
                                                input-format="YYYY-MM-DD"
                                                format="jYYYY/jMM/jDD"
                                                @change="selectRentalExpiryDate"
                                                element="rental_expiry_date"
                                                v-model="rentalExpiryDate"></date-picker>
                                            <input type="text"
                                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                                   placeholder="تاریخ پایان اجاره"
                                                   ref="rental_expiry_date"
                                                   id="rental_expiry_date"
                                                   v-model="rentalExpiryDate"
                                                   readonly="true"/>
                                            <jet-input-error :message="businessForm.error('rental_expiry_date')"
                                                             class="mt-2"/>
                                        </div>
                                    </template>
                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            نوع فروشگاه:
                                        </label>
                                        <jet-button @click.native="businessForm.business_type='physical'"
                                                    :class="businessForm.business_type==='physical' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            فیزیکی
                                        </jet-button>
                                        <jet-button @click.native="businessForm.business_type='physicalVirtual'"
                                                    :class="businessForm.business_type==='physicalVirtual' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            فیزیکی و مجازی
                                        </jet-button>
                                        <jet-button @click.native="businessForm.business_type='virtual'"
                                                    :class="businessForm.business_type==='virtual' ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            مجازی
                                        </jet-button>
                                        <jet-input-error :message="businessForm.error('business_type')" class="mt-2"/>
                                    </div>
                                    <div class="col-span-6">
                                        <label class="block text-sm font-medium text-gray-700">
                                            نوع نماد اعتماد الکترونیک:
                                        </label>
                                        <jet-button @click.native="businessForm.etrust_certificate_type=0"
                                                    :class="businessForm.etrust_certificate_type===0 ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            یک ستاره
                                        </jet-button>
                                        <jet-button @click.native="businessForm.etrust_certificate_type=1"
                                                    :class="businessForm.etrust_certificate_type===1 ? 'bg-blue-500 hover:bg-blue-400' : 'bg-blue-100 text-blue-500 hover:bg-blue-400'">
                                            دو ستاره
                                        </jet-button>
                                        <jet-input-error :message="businessForm.error('etrust_certificate_type')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="license_date" class="block text-sm font-medium text-gray-700">
                                            تاریخ دریافت نماد:
                                        </label>
                                        <date-picker
                                            ref="etrust_certificate_issue_date"
                                            input-format="YYYY-MM-DD"
                                            format="jYYYY/jMM/jDD"
                                            @change="selectEtrustCertificateIssueDateDate"
                                            element="etrust_certificate_issue_date"
                                            v-model="etrustCertificateissueDate"></date-picker>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تاریخ دریافت نماد"
                                               ref="etrust_certificate_issue_date"
                                               id="etrust_certificate_issue_date"
                                               v-model="etrustCertificateissueDate"
                                               readonly="true"/>
                                        <jet-input-error :message="businessForm.error('etrust_certificate_issue_date')"
                                                         class="mt-2"/>
                                    </div>
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="license_date" class="block text-sm font-medium text-gray-700">
                                            تاریخ انقضای نماد:
                                        </label>
                                        <date-picker
                                            ref="etrust_certificate_expiry_date"
                                            input-format="YYYY-MM-DD"
                                            format="jYYYY/jMM/jDD"
                                            @change="selectEtrustCertificateExpiryDate"
                                            element="etrust_certificate_expiry_date"
                                            v-model="etrustCertificateExpiryDate"></date-picker>
                                        <input type="text"
                                               class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md border"
                                               placeholder="تاریخ انقضای نماد"
                                               ref="etrust_certificate_expiry_date"
                                               id="etrust_certificate_expiry_date"
                                               v-model="etrustCertificateExpiryDate"
                                               readonly="true"/>
                                        <jet-input-error :message="businessForm.error('etrust_certificate_expiry_date')"
                                                         class="mt-2"/>
                                    </div>
                                    <div v-if="hasLicense==='YES'" class="col-span-6 md:col-span-2">
                                        <div
                                            class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg v-if="imageFiles.licenseFilePreview===''"
                                                     class="mx-auto h-12 w-12 text-gray-400"
                                                     stroke="currentColor"
                                                     fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"/>
                                                </svg>
                                                <img v-else :src="imageFiles.licenseFilePreview">
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="license_file"
                                                           class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span>انتخاب فایل</span>
                                                        <input id="license_file"
                                                               name="license_file"
                                                               type="file"
                                                               @change="onLicenseFileChange"
                                                               class="sr-only">
                                                    </label>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    تصویر جواز کسب<span class="text-red-500">*</span>
                                                </p>
                                                <jet-input-error
                                                    :message="businessForm.error('license_file')"
                                                    class="mt-2"/>
                                                <jet-input-error
                                                    :message="fileUploadErrors.license_file"
                                                    class="mt-2"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="hasLicense==='NO'" class="col-span-6 md:col-span-2">
                                        <div
                                            class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                            <div class="space-y-1 text-center">
                                                <svg v-if="imageFiles.esteshhadFilePreview===''"
                                                     class="mx-auto h-12 w-12 text-gray-400"
                                                     stroke="currentColor"
                                                     fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                    <path
                                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                        stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"/>
                                                </svg>
                                                <img v-else :src="imageFiles.esteshhadFilePreview">
                                                <div class="flex text-sm text-gray-600">
                                                    <label for="esteshhad_file"
                                                           class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                        <span>انتخاب فایل</span>
                                                        <input id="esteshhad_file"
                                                               name="esteshhad_file"
                                                               type="file"
                                                               @change="onEsteshhadFileChange"
                                                               class="sr-only">
                                                    </label>
                                                </div>
                                                <p class="text-xs text-gray-500">
                                                    تصویر استشهادنامه
                                                </p>
                                                <jet-input-error
                                                    :message="businessForm.error('esteshhad_file')"
                                                    class="mt-2"/>
                                                <jet-input-error
                                                    :message="fileUploadErrors.esteshhad_file"
                                                    class="mt-2"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 py-3 bg-gray-50 text-left sm:px-6">
                                <button type="button"
                                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        v-on:click="submitBusinessForm"
                                        :disabled="submitBusinessFormLoading">
                                    <div v-if="submitBusinessFormLoading"
                                         class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-5 w-5 mx-1"></div>
                                    ذخیره اطلاعات
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- دسته بندی اصناف -->
            <jet-confirmation-modal :show="viewCategoriesModal" @close="closeCategoriesModal">
                <template #title>
                    انتخاب صنف مرتبط
                </template>
                <template #content>
                    <div class="flex items-center">
                        <div>
                            <div>
                                <jet-input type="text" placeholder="جستجو در نام اصناف"
                                           v-model="searchCategoriesValue"/>
                            </div>
                            <div class="mt-3 h-96 overflow-y-auto">
                                <div class="mt-1 hover:underline hover:text-gray-500 cursor-pointer"
                                     @click="selectedCategory=category.id"
                                     :class="{'font-bold':selectedCategory===category.id}"
                                     v-for="category in categoryList" :key="category.id">
                                    <span>{{ category.code }} - </span>
                                    <span>{{ category.name }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 h-96 overflow-y-auto">
                            <div v-if="selectedCategory"
                                 class="mt-1 hover:underline hover:text-gray-500 cursor-pointer"
                                 :class="{'font-bold':businessForm.business_subCategory_code===subCategory.id}"
                                 v-for="subCategory in subCategoryList" :key="subCategory.id"
                                 @click="selectCategory(subCategory.id)">
                                <span>{{ subCategory.code }} - </span>
                                <span>{{ subCategory.name }}</span>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="closeCategoriesModal">
                        بستن
                    </jet-secondary-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </Dashboard>
</template>

<script>
import Dashboard from "@/Pages/Dashboard";
import JetInput from '@/Jetstream/Input'
import JetButton from '@/Jetstream/Button'
import JetInputError from '@/Jetstream/InputError'
import VuePersianDatetimePicker from 'vue-persian-datetime-picker';
import ProfileSteps from "@/Pages/Dashboard/Components/ProfileSteps";
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetDangerButton from "@/Jetstream/DangerButton";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";

export default {
    name: "CreateBusiness",
    components: {
        Dashboard,
        JetInput,
        JetButton,
        JetInputError,
        datePicker: VuePersianDatetimePicker,
        ProfileSteps,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },
    props: {
        customer: Object,
        profile: Object,
        ostans: Array,
        shahrestans: Array,
        bakhshs: Array,
        shahrs: Array,
        categories: Array,
        subCategories: Array,
        profileId: Number,
    },
    data() {
        return {
            shahrestanItems: [],
            bakhshItems: [],
            shahrItems: [],
            hasLicense: 'YES',
            imageFiles: {
                licenseFilePreview: '',
                esteshhadFilePreview: '',
            },
            fileUploadErrors: {
                license_file: '',
                esteshhad_file: '',
            },

            viewCategoriesModal: false,
            searchCategoriesValue: null,
            categoryList: this.categories,
            subCategoryList: this.subCategories,
            selectedCategory: null,

            licenseDate: '',
            licenseStartDate: '',
            rentalExpiryDate: '',
            etrustCertificateExpiryDate: '',
            etrustCertificateissueDate: '',

            businessForm: this.$inertia.form({
                '_method': 'POST',
                profile_id: '',
                ostan_id: '',
                shahrestan_id: '',
                bakhsh_id: '',
                shahr_id: '',
                phone_code: '',
                name: '',
                name_english: '',
                address: '',
                postal_code: '',
                phone: '',
                tax_code: '',
                has_license: '',
                license_code: '',
                license_date: '',
                license_file: '',
                esteshhad_file: '',


                description: '',
                license_start_date: '',

                ownership_type: 'owner',
                rental_contract_number: '',
                rental_expiry_date: '',

                business_category_code: null,
                business_subCategory_code: null,


                email: '',
                website: '',

                business_type: 0,

                etrust_certificate_type: null,
                etrust_certificate_issue_date: null,
                etrust_certificate_expiry_date: null,
            }, {
                bag: 'businessForm',
                resetOnSuccess: false
            }),

            submitBusinessFormLoading: false
        }
    },
    watch: {
        searchCategoriesValue: function ($val) {
            if ($val) {
                let condition = new RegExp($val);
                this.categoryList = this.categories.filter(function (category) {
                    return condition.test(category.name);
                });
            } else {
                this.categoryList = this.categories
            }
        },
        selectedCategory: function ($val) {
            if ($val) {
                this.businessForm.ca
                this.subCategoryList = this.subCategories.filter(c => c.category_id === $val);
            } else {
                this.subCategoryList = this.subCategories
            }
        }
    },
    computed: {
        senfText: function () {
            let category = this.categories.find(c => c.id === this.businessForm.business_category_code);
            let subCategory = this.subCategories.find(c => c.id === this.businessForm.business_subCategory_code);
            if (category && subCategory) return `${category.name} - ${subCategory.name}`;
        }
    },
    methods: {
        listShahrestansItems() {
            let ostanId = this.$refs.ostan_id.value;
            let shahrestanList = [];
            for (let shahrestan of this.shahrestans) {
                if (shahrestan.ostan == ostanId) {
                    shahrestanList.push({id: shahrestan.id, name: shahrestan.name});
                }
            }
            this.shahrestanItems = shahrestanList;
        },
        listBakhshItems() {
            let shahrestanId = this.$refs.shahrestan_id.value;
            let bakhshList = [];
            for (let bakhsh of this.bakhshs) {
                if (bakhsh.shahrestan == shahrestanId) {
                    bakhshList.push({id: bakhsh.id, name: bakhsh.name});
                }
            }
            this.bakhshItems = bakhshList;
        },
        listShahrItems() {
            let bakhshId = this.$refs.bakhsh_id.value;
            let shahrList = [];
            for (let shahr of this.shahrs) {
                if (shahr.bakhsh == bakhshId) {
                    shahrList.push({id: shahr.id, name: shahr.name});
                }
            }
            this.shahrItems = shahrList;
        },
        onLicenseFileChange(e) {
            const file = e.target.files[0];
            if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                this.fileUploadErrors.license_file = 'فایل انتخاب شده نباید بیشتر از '
                    + this.$page.configs.maximumUploadSize
                    + 'کیلوبایت باشد.';
                return;
            }
            this.fileUploadErrors.license_file = '';
            this.businessForm.license_file = e.target.files[0];
            this.imageFiles.licenseFilePreview = URL.createObjectURL(file);
        },
        onEsteshhadFileChange(e) {
            const file = e.target.files[0];
            if (file.size > (this.$page.configs.maximumUploadSize * 1024)) {
                this.fileUploadErrors.esteshhad_file = 'فایل انتخاب شده نباید بیشتر از '
                    + this.$page.configs.maximumUploadSize
                    + 'کیلوبایت باشد.';
                return;
            }
            this.fileUploadErrors.esteshhad_file = '';
            this.businessForm.esteshhad_file = e.target.files[0];
            this.imageFiles.esteshhadFilePreview = URL.createObjectURL(file);
        },
        resetFileUploadErrors() {
            this.fileUploadErrors = {
                license_file: '',
                esteshhad_file: '',
            };
        },
        selectLicenseDate(e) {
            this.businessForm.license_date = e.format('YYYY/MM/DD');
        },
        selectLicenseStartDate(e) {
            this.businessForm.license_start_date = e.format('YYYY/MM/DD');
        },
        selectRentalExpiryDate(e) {
            this.businessForm.rental_expiry_date = e.format('YYYY/MM/DD');
        },
        selectEtrustCertificateIssueDateDate(e) {
            this.businessForm.etrust_certificate_issue_date = e.format('YYYY/MM/DD');
        },
        selectEtrustCertificateExpiryDate(e) {
            this.businessForm.etrust_certificate_expiry_date = e.format('YYYY/MM/DD');
        },
        submitBusinessForm() {
            this.submitBusinessFormLoading = true;
            this.businessForm.profile_id = this.profileId;
            this.businessForm.has_license = this.hasLicense;
            this.resetFileUploadErrors();
            this.businessForm.post(route('dashboard.profiles.businesses.store', {profile: this.profile.id})).then(response => {
                this.submitBusinessFormLoading = false;
            })
        },

        viewCategories() {
            this.viewCategoriesModal = true;
        },
        closeCategoriesModal() {
            this.viewCategoriesModal = false;
        },
        selectCategory(id) {
            this.businessForm.business_category_code = this.selectedCategory;
            this.businessForm.business_subCategory_code = id;
            this.closeCategoriesModal();
        }
    }
}
</script>

<style scoped>

</style>
