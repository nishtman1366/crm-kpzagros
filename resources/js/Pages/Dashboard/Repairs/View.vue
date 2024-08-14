<template>
    <Dashboard>
        <template #breadcrumb> / لیست درخواست ها / بررسی درخواست تعمیرات</template>
        <template #dashboardContent>
            <div>
                <div>
                    <jet-form-section @submitted="submitUpdateRepairForm">
                        <template #title>
                            ثبت درخواست تعمیرات
                        </template>

                        <template #description>
                            <p class="mx-3 my-2 rounded border border-blue-700 bg-blue-200 text-blue-700 px-2 py-3 text-center text-xl">
                                کد رهگیری: {{ repair.tracking_code }}
                            </p>
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

                            <div class="my-1 p-3 border-r-4 border-green-600 bg-green-100"
                                 v-for="event in repair.events" :key="event.id">
                                <p class="text-sm text-gray-400 mt-2">{{ event.jDate | persianDigit }}</p>
                                <p class="font-bold ml-1">{{ event.title }}
                                    <span class="text-xs text-gray-400">{{ event.user && event.user.name }}</span>
                                </p>
                                <p v-if="event.description" class="text-justify">{{ event.description }}</p>
                            </div>
                        </template>
                        <template #form>
                            <div class="col-span-6 flex justify-start items-end"
                                 v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'">
                                <div class="ml-3">
                                    <jet-label for="admin_status" value="وضعیت"/>
                                    <select id="admin_status" name="admin_status" ref="admin_status"
                                            v-model="updateStatusByAdminForm.status"
                                            autocomplete="admin_status"
                                            class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                        <option v-for="status in statuses" :key="status.id"
                                                :value="status.id">
                                            {{ status.name }}
                                        </option>
                                    </select>
                                </div>
                                <jet-button type="button" @click.native="submitUpdateStatusByAdminForm">ذخیره
                                </jet-button>
                            </div>
                            <div class="col-span-6" v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'">
                                <jet-section-border/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="device_type_id" value="مدل دستگاه"/>
                                <select id="device_type_id" name="device_type_id" ref="device_type_id"
                                        v-model="updateRepairForm.device_type_id"
                                        autocomplete="device_type_id"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                    <option v-for="type in deviceTypes" :key="type.id"
                                            :value="type.id">
                                        {{ type.name }}
                                    </option>
                                </select>
                                <jet-input-error :message="updateRepairForm.error('device_type_id')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="serial" value="سریال دستگاه"/>
                                <jet-input id="serial"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="updateRepairForm.serial"
                                           ref="serial"
                                           autocomplete="serial"/>
                                <jet-input-error :message="updateRepairForm.error('serial')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="model_id" value="سرویس دهنده"/>
                                <select id="psp_id" name="psp_id" ref="psp_id"
                                        v-model="updateRepairForm.psp_id"
                                        autocomplete="psp_id"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                    <option v-for="psp in psps" :key="psp.id"
                                            :value="psp.id">
                                        {{ psp.name }}
                                    </option>
                                </select>
                                <jet-input-error :message="updateRepairForm.error('psp_id')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="bank_id" value="بانک"/>
                                <select id="bank_id" name="bank_id" ref="bank_id"
                                        v-model="updateRepairForm.bank_id"
                                        autocomplete="bank_id"
                                        class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                    <option v-for="bank in banks" :key="bank.id"
                                            :value="bank.id">
                                        {{ bank.name }}
                                    </option>
                                </select>
                                <jet-input-error :message="updateRepairForm.error('bank_id')" class="mt-2"/>
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
                                <jet-input-error :message="updateRepairForm.error('guarantee_end')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="name" value="نام پذیرنده"/>
                                <jet-input id="name"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="updateRepairForm.name"
                                           ref="name"
                                           autocomplete="name"/>
                                <jet-input-error :message="updateRepairForm.error('name')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="national_code" value="کد ملی پذیرنده"/>
                                <jet-input id="national_code"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="updateRepairForm.national_code"
                                           ref="national_code"
                                           autocomplete="national_code"/>
                                <jet-input-error :message="updateRepairForm.error('national_code')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="mobile" value="تلفن همراه پذیرنده"/>
                                <jet-input id="mobile"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="updateRepairForm.mobile"
                                           ref="mobile"
                                           autocomplete="mobile"/>
                                <jet-input-error :message="updateRepairForm.error('mobile')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="business_name" value="نام فروشگاه"/>
                                <jet-input id="business_name"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="updateRepairForm.business_name"
                                           ref="business_name"
                                           autocomplete="business_name"/>
                                <jet-input-error :message="updateRepairForm.error('business_name')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-label for="accessories" value="اقلام همراه"/>

                                <div class="flex">
                                    <jet-label v-for="item in accessories" :key="item.id" class="flex items-center"
                                               :for="'accessories_'+item.id">
                                        <input class="form-checkbox" type="checkbox" :name="'accessories_'+item.id"
                                               :id="'accessories_'+item.id"
                                               v-model="updateRepairForm.accessories"
                                               :value="item.id"/>
                                        <span class="mr- ml-3">{{ item.name }}</span>
                                    </jet-label>
                                </div>
                                <jet-input-error :message="updateRepairForm.error('accessories')" class="mt-2"/>
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
                                           v-model="updateRepairForm.repairTypeList"
                                           :id="'type_'+type.id"
                                           :value="type.id">{{ type.name }}
                                </jet-label>
                            </div>
                            <div class="col-span-6">
                                <jet-input-error :message="updateRepairForm.error('repairTypeList')" class="mt-2"/>
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <jet-label for="description" value="توضیحات تکمیلی"/>
                                <textarea id="description"
                                          type="description"
                                          class="form-input mt-1 block w-full"
                                          v-model="updateRepairForm.description"
                                          ref="description"
                                          autocomplete="description" rows="3" cols="60"></textarea>
                                <jet-input-error :message="updateRepairForm.error('description')" class="mt-2"/>
                            </div>
                            <div class="col-span-6">
                                <jet-section-border></jet-section-border>
                            </div>
                            <template
                                v-if="$page.user.level==='SUPERUSER' || $page.user.level==='ADMIN'  || $page.user.level==='TECHNICAL'">
                                <div class="col-span-6 text-lg">
                                    تعمیرات
                                </div>
                                <div
                                    v-if="$page.user.level==='SUPERUSER' || $page.user.level==='ADMIN'  || $page.user.level==='TECHNICAL'"
                                    class="col-span-6 sm:col-span-2">
                                    <jet-label for="location_id" value="محل تعمیرات"/>
                                    <select id="location_id" name="location_id" ref="location_id"
                                            v-model="updateRepairForm.location_id"
                                            autocomplete="location_id"
                                            class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                        <option v-for="location in locations" :key="location.id"
                                                :value="location.id">
                                            {{ location.name }}
                                        </option>
                                    </select>
                                    <jet-input-error :message="updateRepairForm.error('location_id')" class="mt-2"/>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="price" value="هزینه تعمیرات"/>
                                    <jet-input id="price"
                                               type="text"
                                               class="mt-1 block w-full"
                                               v-model="updateRepairForm.price"
                                               ref="price"
                                               :disabled="$page.user.level==='AGENT' || $page.user.level==='MARKETER'"
                                               autocomplete="price"/>
                                    <jet-input-error :message="updateRepairForm.error('price')" class="mt-2"/>
                                </div>
                                <div class="col-span-6 text-lg">
                                    دستگاه جایگزین
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="location_id" value="مدل دستگاه جایگزین"/>
                                    <select id="new_device_type_id" name="new_device_type_id" ref="new_device_type_id"
                                            v-model="updateRepairForm.new_device_type_id"
                                            autocomplete="new_device_type_id"
                                            class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                        <option v-for="device in deviceTypes" :key="device.id"
                                                :value="device.id">
                                            {{ device.name }}
                                        </option>
                                    </select>
                                    <jet-input-error :message="updateRepairForm.error('new_device_type_id')"
                                                     class="mt-2"/>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="new_serial" value="سریال دستگاه جایگزین"/>
                                    <jet-input id="new_serial"
                                               type="text"
                                               class="mt-1 block w-full"
                                               v-model="updateRepairForm.new_serial"
                                               ref="new_serial"
                                               autocomplete="new_serial"/>
                                    <jet-input-error :message="updateRepairForm.error('new_serial')" class="mt-2"/>
                                </div>
                                <div class="col-span-6 text-lg">
                                    دستگاه امانی
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="loan_device_type_id" value="مدل دستگاه امانی"/>
                                    <select id="loan_device_type_id" name="loan_device_type_id"
                                            ref="loan_device_type_id"
                                            v-model="updateRepairForm.loan_device_type_id"
                                            autocomplete="loan_device_type_id"
                                            class="form-input rounded-md shadow-sm mt-1 block w-full pr-6">
                                        <option v-for="device in deviceTypes" :key="device.id"
                                                :value="device.id">
                                            {{ device.name }}
                                        </option>
                                    </select>
                                    <jet-input-error :message="updateRepairForm.error('loan_device_type_id')"
                                                     class="mt-2"/>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="loan_serial" value="سریال دستگاه امانی"/>
                                    <jet-input id="loan_serial"
                                               type="text"
                                               class="mt-1 block w-full"
                                               v-model="updateRepairForm.loan_serial"
                                               ref="loan_serial"
                                               autocomplete="loan_serial"/>
                                    <jet-input-error :message="updateRepairForm.error('loan_serial')" class="mt-2"/>
                                </div>
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="deposit" value="ودیعه دستگاه امانی"/>
                                    <jet-input id="deposit"
                                               type="text"
                                               class="mt-1 block w-full"
                                               v-model="updateRepairForm.deposit"
                                               ref="deposit"
                                               :disabled="$page.user.level==='AGENT' || $page.user.level==='MARKETER'"
                                               autocomplete="deposit"/>
                                    <jet-input-error :message="updateRepairForm.error('deposit')" class="mt-2"/>
                                </div>
                                <div class="col-span-6">
                                    <jet-section-border></jet-section-border>
                                </div>
                                <div class="col-span-6 text-lg">
                                    پرداخت ها
                                </div>
                                <div class="col-span-6">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead>
                                        <tr>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                نام پرداخت کننده
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                شیوه پرداخت
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                تاریخ
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                کد پیگیری بانک
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                کد پیگیری سیستم
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                وضعیت
                                            </th>
                                            <th scope="col"
                                                class="py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                تایید پرداخت
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="payment in repair.payments" :key="payment.id">
                                            <td class="py-4 text-center text-gray-900">
                                                {{ payment.user && payment.user.name }}
                                            </td>
                                            <td class="py-4 text-center text-gray-900">
                                                {{ payment.type && payment.type.name }}
                                            </td>
                                            <td class="py-4 text-center text-gray-900">{{ payment.jDate }}</td>
                                            <td class="py-4 text-center text-gray-900">{{ payment.ref_code }}</td>
                                            <td class="py-4 text-center text-gray-900">{{ payment.tracking_code }}</td>
                                            <td :colspan="payment.status==2 ? 2 : ''"
                                                class="py-4 text-center text-gray-900">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                  :class="paymentStatusColors(payment.status)">
                                                    {{ payment.statusText }}
                                            </span>
                                            </td>
                                            <td class="py-4 text-center text-gray-900">
                                                <InertiaLink
                                                    v-if="payment.status==1 && $page.user.level==='ADMIN'  || $page.user.level==='SUPERUSER'"
                                                    :href="route('dashboard.payments.confirm',{paymentId: payment.id})"
                                                    class="tooltip-box text-indigo-600 hover:text-indigo-900">
                                                    <button title="تایید پرداخت"
                                                            v-b-tooltip.hover>
                                                        <i id="test" class="material-icons">check</i>
                                                    </button>
                                                </InertiaLink>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-span-6">
                                    <jet-section-border></jet-section-border>
                                </div>
                                <div class="col-span-6 sm:col-span-4">
                                    <jet-label for="technical_description" value="گزارش عملیات"/>
                                    <textarea id="technical_description"
                                              type="technical_description"
                                              class="form-input mt-1 block w-full"
                                              v-model="updateRepairForm.technical_description"
                                              ref="technical_description"
                                              autocomplete="technical_description" rows="3" cols="60"></textarea>
                                    <jet-input-error :message="updateRepairStatusForm.error('technical_description')"
                                                     class="mt-2"/>
                                </div>
                            </template>
                        </template>
                        <template #actions v-if="$page.user.level!=='ACCOUNTING'">
                            <jet-action-message :on="updateRepairForm.recentlySuccessful" class="mr-3">
                                ذخیره شد.
                            </jet-action-message>

                            <jet-button
                                v-if="$page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='TECHNICAL'"
                                :class="{ 'opacity-25': updateRepairForm.processing || updateRepairStatusForm.processing }"
                                :disabled="updateRepairForm.processing || updateRepairStatusForm.processing">
                                ویرایش اطلاعات پرونده / ثبت گزارش
                            </jet-button>
                            <jet-button
                                v-if="repair.status==1 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN'  || $page.user.level==='TECHNICAL')"
                                @click.native="changeRepairStatus(2)"
                                type="button"
                                class="mx-2 bg-green-600 hover:bg-green-500 active:bg-green-700"
                                :class="{ 'opacity-25': updateRepairStatusForm.processing || updateRepairForm.processing }"
                                :disabled="updateRepairStatusForm.processing || updateRepairForm.processing">
                                دریافت توسط واحد فنی
                            </jet-button>
                            <jet-button
                                v-if="repair.status==2 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN'  || $page.user.level==='TECHNICAL')"
                                @click.native="changeRepairStatus(3)"
                                type="button"
                                class="mx-2 bg-green-600 hover:bg-green-500 active:bg-green-700"
                                :class="{ 'opacity-25': updateRepairStatusForm.processing || updateRepairForm.processing }"
                                :disabled="updateRepairStatusForm.processing || updateRepairForm.processing">
                                در صف تعمیر
                            </jet-button>
                            <jet-button
                                v-if="repair.status==3 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN'  || $page.user.level==='TECHNICAL')"
                                @click.native="changeRepairStatus(8)"
                                type="button"
                                class="mx-2 bg-red-600 hover:bg-red-500 active:bg-red-700"
                                :class="{ 'opacity-25': updateRepairStatusForm.processing || updateRepairForm.processing }"
                                :disabled="updateRepairStatusForm.processing || updateRepairForm.processing">
                                غیرقابل تعمیر
                            </jet-button>
                            <jet-button
                                v-if="repair.status==3 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN'  || $page.user.level==='TECHNICAL')"
                                @click.native="changeRepairStatus(4)"
                                type="button"
                                class="mx-2 bg-green-600 hover:bg-green-500 active:bg-green-700"
                                :class="{ 'opacity-25': updateRepairStatusForm.processing || updateRepairForm.processing }"
                                :disabled="updateRepairStatusForm.processing || updateRepairForm.processing">
                                تعمیر شده
                            </jet-button>
                            <jet-button
                                v-if="repair.status==4 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN'  || $page.user.level==='TECHNICAL')"
                                @click.native="changeRepairStatus(5)"
                                type="button"
                                class="mx-2 bg-green-600 hover:bg-green-500 active:bg-green-700"
                                :class="{ 'opacity-25': updateRepairStatusForm.processing || updateRepairForm.processing }"
                                :disabled="updateRepairStatusForm.processing || updateRepairForm.processing">
                                در انتظار پرداخت
                            </jet-button>
                            <jet-button
                                v-if="repair.status==5"
                                @click.native="showPaymentModal"
                                type="button"
                                class="mx-2 bg-green-600 hover:bg-green-500 active:bg-green-700"
                                :class="{ 'opacity-25': updateRepairStatusForm.processing || updateRepairForm.processing }"
                                :disabled="updateRepairStatusForm.processing || updateRepairForm.processing">
                                پرداخت هزینه
                            </jet-button>
                            <jet-button
                                v-if="(repair.status==6 || repair.status==8) && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.level==='TECHNICAL')"
                                @click.native="changeRepairStatus(7)"
                                type="button"
                                class="mx-2 bg-green-600 hover:bg-green-500 active:bg-green-700"
                                :class="{ 'opacity-25': updateRepairStatusForm.processing || updateRepairForm.processing }"
                                :disabled="updateRepairStatusForm.processing || updateRepairForm.processing">
                                عودت دستگاه
                            </jet-button>
                        </template>
                    </jet-form-section>
                </div>
            </div>

            <jet-confirmation-modal maxHeight="h-96" class="h-16" :show="viewPaymentModal"
                                    @close="viewPaymentModal = false">
                <template #title>
                    پرداخت هزینه تعمیرات
                </template>
                <template #content>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                        <div class="flex items-center justify-between mt-8">
                            <jet-button
                                :class="{ 'bg-green-500 hover:bg-green-400': submitPaymentForm.type==1 }"
                                class="border-green-500 bg-green-200 hover:bg-green-400 active:bg-green-600 text-gray-900"
                                @click.native="requestOnlinePayment">پرداخت آنلاین
                            </jet-button>
                            <jet-button
                                v-if="$page.user.level==='SUPERUSER' || $page.user.level==='ADMIN'  || $page.user.level==='TECHNICAL'"
                                @click.native="submitPaymentForm.type=2;requestOnlinePaymentLoading=false"
                                :class="{ 'bg-blue-500 hover:bg-blue-400': submitPaymentForm.type==2 }"
                                class="border-blue-500 bg-blue-200 hover:bg-blue-400 active:bg-blue-600 text-gray-900">
                                واریز به حساب
                            </jet-button>
                        </div>
                        <div class="mt-3" v-if="requestOnlinePaymentLoading">
                            <div v-if="onlinePaymentError" class="text-center text-lg">{{ onlinePaymentError }}</div>
                            <div v-else class="text-center text-lg">
                                در حال ارسال به درگاه پرداخت
                            </div>
                        </div>
                        <div class="grid grid-cols-2 mt-3" v-else-if="submitPaymentForm.type==2">
                            <div class="col-span-2 sm:col-span-1">
                                <jet-label for="ref_code" value="کد پیگیری پرداخت"></jet-label>
                                <jet-input name="ref_code"
                                           id="ref_code"
                                           v-model="submitPaymentForm.ref_code">
                                </jet-input>
                                <jet-input-error :message="submitPaymentForm.error('ref_code')" class="mt-2"/>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <jet-label for="payment_date" value="تاریخ پرداخت"></jet-label>
                                <date-picker
                                    :clearable="true"
                                    type="datetime"
                                    ref="payment_date_cal"
                                    input-format="YYYY-MM-DD H:m"
                                    format="jYYYY/jMM/jDD  H:m"
                                    @change="selectPaymentDate"
                                    element="payment_date"
                                    v-model="paymentDate"></date-picker>
                                <jet-input name="payment_date"
                                           id="payment_date"
                                           readonly="true"
                                           v-model="paymentDate">
                                </jet-input>
                                <jet-input-error :message="submitPaymentForm.error('payment_date')" class="mt-2"/>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer v-if="!requestOnlinePaymentLoading">
                    <jet-secondary-button class="ml-2" @click.native="viewPaymentModal = false">
                        انصراف
                    </jet-secondary-button>
                    <jet-button class="bg-green-500 ml-2 hover:bg-green-400" @click.native="submitPayment"
                                :class="{ 'opacity-25': submitPaymentForm.processing }"
                                :disabled="submitPaymentForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
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
import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
import VuePersianDatetimePicker from 'vue-persian-datetime-picker';

export default {
    name: "View",
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
        JetConfirmationModal,
        datePicker: VuePersianDatetimePicker,
    },
    props: {
        repair: Object,
        repairTypesList: Array,
        locations: Array,
        deviceTypes: Array,
        psps: Array,
        banks: Array,
        repairTypes: Array,
        accessories: Array,
        statuses: Array,
    },
    data() {
        return {
            viewPaymentModal: false,
            paymentDate: '',
            message: '',
            guarantee_end: this.repair.jGuaranteeEnd,
            updateRepairForm: this.$inertia.form({
                '_method': 'PUT',
                device_type_id: this.repair.device_type_id,
                psp_id: this.repair.psp_id,
                bank_id: this.repair.bank_id,
                serial: this.repair.serial,
                guarantee_end: this.repair.guarantee_end,
                name: this.repair.name,
                business_name: this.repair.business_name,
                mobile: this.repair.mobile,
                national_code: this.repair.national_code,
                repairTypeList: this.repairTypesList,
                description: this.repair.description,
                location_id: this.repair.location_id,
                price: this.repair.price,
                new_serial: this.repair.new_serial,
                new_device_type_id: this.repair.new_device_type_id,
                loan_serial: this.repair.loan_serial,
                loan_device_type_id: this.repair.loan_device_type_id,
                deposit: this.repair.deposit,
                technical_description: this.repair.technical_description,
                accessories: this.repair.accessories,
            }, {
                bag: 'updateRepairForm',
            }),
            updateRepairStatusForm: this.$inertia.form({
                '_method': 'PUT',
                status: '',
                message: ''
            }, {
                bag: 'updateRepairStatusForm',
            }),

            requestOnlinePaymentLoading: false,
            submitPaymentForm: this.$inertia.form({
                '_method': 'PUT',
                type: null,
                ref_code: '',
                payment_date: '',
                status: 6,
            }, {
                bag: 'submitPaymentForm',
            }),

            onlinePaymentError: '',
            updateStatusByAdminForm: this.$inertia.form({
                '_method': 'PUT',
                status: this.repair.status,
            }, {
                bag: 'updateStatusByAdminForm',
            }),
        }
    },
    methods: {
        requestOnlinePayment() {
            this.submitPaymentForm.type = 1;
            this.requestOnlinePaymentLoading = true;
            this.onlinePaymentError = '';
            axios.get(route('dashboard.payments.ipg.request', {type: 'repairs', id: this.repair.id}))
                .then(response => {
                    this.viewPaymentModal = true;
                    if (response.data.paymentRequest && response.data.paymentRequest.status === 'success') {
                        // console.log(response.data.paymentRequest.redirectUrl);
                        window.location.href = response.data.paymentRequest.redirectUrl;
                    }
                    // this.requestOnlinePaymentLoading = false;
                })
                .catch(error => {
                    this.onlinePaymentError = error.response.data.message;

                })
                .finally(() => {

                })
        },
        showPaymentModal() {
            this.viewPaymentModal = true;
        },
        selectPaymentDate(e) {
            this.submitPaymentForm.payment_date = e.format('YYYY/MM/DD H:m');
        },
        submitPayment() {
            this.submitPaymentForm.post(route('dashboard.repairs.update', {repairId: this.repair.id})).then(response => {
                if (!this.submitPaymentForm.hasErrors()) {
                    this.viewPaymentModal = false;
                }
            })
        },
        submitUpdateRepairForm() {
            this.updateRepairForm.post(route('dashboard.repairs.update', {repairId: this.repair.id})).then(response => {
                if (!this.updateRepairForm.hasErrors()) {

                }
            })
        },
        changeRepairStatus(status) {
            this.updateRepairStatusForm.status = status;
            this.updateRepairStatusForm.message = this.message;
            this.updateRepairStatusForm.post(route('dashboard.repairs.update', {repairId: this.repair.id})).then(response => {
                if (!this.updateRepairStatusForm.hasErrors()) {

                }
            })
        },
        paymentStatusColors(status) {
            switch (status) {
                case 0:
                    return 'bg-blue-100 text-blue-800';
                case 1:
                    return 'bg-yellow-100 text-yellow-800';
                case 2:
                    return 'bg-green-100 text-green-800';
            }
        },
        selectGuaranteeEnd(e) {
            this.updateRepairForm.guarantee_end = e.format('YYYY/MM/DD');
        },
        submitUpdateStatusByAdminForm() {
            this.updateStatusByAdminForm.post(route('dashboard.repairs.updateStatusByAdmin', {repairId: this.repair.id}));
        }
    }
}
</script>

<style scoped>

</style>
