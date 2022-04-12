<template>
    <Dashboard>
        <template #breadcrumb> / اعلان های گروهی</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="">
                                <jet-button
                                    class="md:float-left"
                                    @click.native="newNotification">
                                    ثبت اعلان جدید
                                </jet-button>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        #
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        عنوان
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نوع اعلان
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تاریخ ثبت
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تاریخ ارسال
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="notification in list" :key="notification.id">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{ notification.no }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{ notification.title }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{ notification.type == 'pattern' ? 'الگو' : 'باشگاه مشتریان' }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(notification.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{ notification.statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{ notification.createDate }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{ notification.sentDate }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button v-if="notification.status==0"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                @click="editNotification(notification)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <inertia-link v-else
                                                      class="text-indigo-600 hover:text-indigo-900"
                                                      :href="route('dashboard.notifications.details',{id:notification.id})">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </inertia-link>
                                        <button class="text-green-600 hover:text-green-900"
                                                @click.nativ="viewReceptions(notification)">
                                            <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M12,5.5A3.5,3.5 0 0,1 15.5,9A3.5,3.5 0 0,1 12,12.5A3.5,3.5 0 0,1 8.5,9A3.5,3.5 0 0,1 12,5.5M5,8C5.56,8 6.08,8.15 6.53,8.42C6.38,9.85 6.8,11.27 7.66,12.38C7.16,13.34 6.16,14 5,14A3,3 0 0,1 2,11A3,3 0 0,1 5,8M19,8A3,3 0 0,1 22,11A3,3 0 0,1 19,14C17.84,14 16.84,13.34 16.34,12.38C17.2,11.27 17.62,9.85 17.47,8.42C17.92,8.15 18.44,8 19,8M5.5,18.25C5.5,16.18 8.41,14.5 12,14.5C15.59,14.5 18.5,16.18 18.5,18.25V20H5.5V18.25M0,20V18.5C0,17.11 1.89,15.94 4.45,15.6C3.86,16.28 3.5,17.22 3.5,18.25V20H0M24,20H20.5V18.25C20.5,17.22 20.14,16.28 19.55,15.6C22.11,15.94 24,17.11 24,18.5V20Z"/>
                                            </svg>
                                        </button>
                                        <button @click="sendNotification(notification)"
                                                class="text-blue-600 hover:text-blue-900">
                                            <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M13 17H17V14L22 18.5L17 23V20H13V17M20 4H4A2 2 0 0 0 2 6V18A2 2 0 0 0 4 20H11.35A5.8 5.8 0 0 1 11 18A6 6 0 0 1 22 14.69V6A2 2 0 0 0 20 4M20 8L12 13L4 8V6L12 11L20 6Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.notifications.destroy',{id:notification.id})"
                                                     class="text-red-600 hover:text-red-900">
                                            <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                            </svg>
                                        </InertiaLink>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <jet-dialog :show="viewNotificationModal" @close="viewNotificationModal=false">
                <template #title>ثبت اعلان جدید</template>
                <template #content>
                    <div class="">
                        <div class="mt-3">
                            <jet-label for="title" value="عنوان"/>
                            <jet-input type="text" class="w-full" name="title" id="title"
                                       v-model="notificationForm.title"/>
                            <jet-input-error :message="notificationForm.error('title')"/>
                        </div>
                        <div class="mt-3">
                            <jet-label for="type" value="نوع اعلان"/>
                            <jet-button class="hover:bg-blue-400"
                                        @click.native="notificationForm.type='pattern'"
                                        :class="notificationForm.type=='pattern' ? 'bg-blue-500' : 'bg-blue-100 text-blue-500'">
                                الگو
                            </jet-button>
                            <jet-button class="hover:bg-blue-400"
                                        @click.native="notificationForm.type='club'"
                                        :class="notificationForm.type=='club' ? 'bg-blue-500' : 'bg-blue-100 text-blue-500'">
                                باشگاه مشتریان
                            </jet-button>
                            <jet-input-error :message="notificationForm.error('type')"/>
                        </div>
                        <template v-if="notificationForm.type==='pattern'">
                            <div class="mt-3">
                                <jet-label for="pattern" value="کد الگو"/>
                                <jet-input type="text" name="pattern" id="pattern" v-model="notificationForm.pattern"/>
                                <jet-input-error :message="notificationForm.error('pattern')"/>
                            </div>
                            <div class="mt-3">
                                <jet-label for="parameters" value="متغیرها"/>
                                <textarea class="w-full form-input"
                                          name="parameters"
                                          id="parameters"
                                          v-model="notificationForm.parameters"
                                          placeholder="value1=مقدار ۱,value2=مقدار ۲..."
                                />
                                <span>در این بخش پارامترهای موجود در پترن را مااند نمونه وارد نمایید: نام متغیر=مقدار متغیر و هر جفت را با ویرگول انگلیسی (,) از هم جدا کنید.</span>
                                <jet-input-error :message="notificationForm.error('parameters')"/>
                            </div>
                        </template>
                        <template v-if="notificationForm.type==='club'">
                            <div class="mt-3">
                                <jet-label for="body" value="متن پیام"/>
                                <textarea class="w-full form-input"
                                          name="body"
                                          id="body"
                                          v-model="notificationForm.body"
                                          placeholder="متن پیام"
                                />
                                <div class="flex justify-between font-bold">
                                    <span>{{ notificationForm.body && notificationForm.body.length }} کاراکتر</span>
                                    <span>{{ notificationPageCount }} پیامک</span>
                                </div>
                                <jet-input-error :message="notificationForm.error('body')"/>
                            </div>
                        </template>
                    </div>
                </template>
                <template #footer>
                    <jet-button v-if="notificationId" class="bg-green-500 hover:bg-green-400"
                                @click.native="updateNotification">بروزرسانی
                    </jet-button>
                    <jet-button v-else class="bg-green-500 hover:bg-green-400" @click.native="submitNewNotification">ثبت
                    </jet-button>
                    <jet-secondary-button @click.native="viewNotificationModal=false">انصراف</jet-secondary-button>
                </template>
            </jet-dialog>
            <jet-dialog :show="viewReceptionsModal" @close="viewReceptionsModal=false">
                <template #title>لیست گیرندگان</template>
                <template #content>
                    <div class="grid grid-cols-4 gap-3">
                        <div class="col-span-3">
                            <div class="mt-3">
                                <jet-label for="file" value="فایل اکسل"/>
                                <jet-button @click.native="$refs.file.click()">انتخاب فایل</jet-button>
                                <p>فایل اکسل باید بدون سطر عنوان و حاوی یک ستون که در هر سطر آن یک شماره قرار دارد
                                    باشد.</p>
                                <input type="file" name="file" id="file" ref="file" class="hidden"
                                       @change="handleReceptionsFile"/>
                                <jet-input-error :message="receptionsForm.error('file')"/>
                            </div>
                            <div class="mt-3">
                                <jet-label for="body" value="لیست شماره ها"/>
                                <textarea placeholder="09123456789,09351234567,09112554788" class="w-full form-input"
                                          name="body" id="body" v-model="receptionsForm.body"/>
                                <span>در این بخش میتوانید شماره گیرندگان را به صورت پشت هم نوشته و با یک ویرگول (,) از هم جدا کنید.</span>
                                <jet-input-error :message="receptionsForm.error('body')"/>
                            </div>
                            <div class="mt-3 flex items-center justify-start">
                                <input type="checkbox" id="delete_old_numbers" name="delete_old_numbers"
                                       class="border border-gray-200 ml-1"
                                       v-model="receptionsForm.delete_old_numbers">
                                <jet-label for="delete_old_numbers" value="حذف شماره های قدیمی"/>
                            </div>
                        </div>
                        <div class="h-64 overflow-y-auto border border-gray-200 rounded p-1">
                            <div class="font-bold">گیرندگان این اعلان:</div>
                            <div>{{ notification.receptions_count }} شماره</div>
                            <!--                            <div class="flex justify-between" v-for="reception in notification.receptions"-->
                            <!--                                 :key="reception.id">-->
                            <!--                                <div>{{reception.reception}}</div>-->
                            <!--                                <div class="cursor-pointer text-red-500 hover:text-red-400"-->
                            <!--                                     @click="deleteReception(reception.id)">حذف-->
                            <!--                                </div>-->
                            <!--                            </div>-->
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-button class="bg-green-500 hover:bg-green-400"
                                :class="{'opacity-25':receptionsForm.processing}"
                                :disable="receptionsForm.processing"
                                @click.native="submitNewReceptions">ارسال
                    </jet-button>
                    <jet-secondary-button @click.native="viewReceptionsModal=false">انصراف</jet-secondary-button>
                </template>
            </jet-dialog>
            <jet-dialog :show="viewSendNotificationModal" @close="viewSendNotificationModal=false">
                <template #title>ارسال اعلان</template>
                <template #content>
                    <div class="">
                        <div class="mt-3">
                            <p>آیا از ارسال این اعلان مطمئن هستید؟</p>
                            <p>عنوان: <span class="font-bold">{{ notification.title }}</span></p>
                            <p v-if="notification.type==='pattern'">کد الگو: <span
                                class="font-bold">{{ notification.pattern }}</span>
                            </p>
                            <p v-else-if="notification.type==='club'" class="my-1">متن اعلان: <span
                                class="font-bold">{{ notification.body }}</span>
                            </p>
                            <p>تعداد گیرندگان: <span class="font-bold">{{ notification.receptions_count }}</span></p>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-button :disable="sendNotificationForm.processing"
                                :class="{'opacity-25':sendNotificationForm.processing}"
                                class="bg-green-500 hover:bg-green-400"
                                @click.native="submitSendNotification">
                        ارسال
                    </jet-button>
                    <jet-secondary-button @click.native="viewSendNotificationModal=false">انصراف</jet-secondary-button>
                </template>
            </jet-dialog>
        </template>
    </Dashboard>
</template>

<script>
import Dashboard from "@/Pages/Dashboard";
import JetButton from "@/Jetstream/Button";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDialog from "@/Jetstream/DialogModal"
import JetLabel from "@/Jetstream/Label";
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";

export default {
    name: "List",
    components: {Dashboard, JetButton, JetSecondaryButton, JetDialog, JetLabel, JetInput, JetInputError},
    props: {
        list: Array
    },
    data() {
        return {
            viewNotificationModal: false,
            notificationForm: this.$inertia.form({
                title: null,
                pattern: null,
                parameters: null,
                body: null,
                type: null,
            }, {
                bag: 'notificationForm'
            }),
            viewReceptionsModal: false,
            notification: {id: null, title: null, pattern: null},
            notificationId: null,
            receptionsForm: this.$inertia.form({
                batch_notification_id: null,
                file: null,
                delete_old_numbers: false,
                body: null
            }, {
                bag: 'receptionsForm'
            }),
            viewSendNotificationModal: false,
            sendNotificationForm: this.$inertia.form({})
        }
    },
    computed: {
        notificationPageCount: function () {
            let length = 0;
            if (this.notificationForm.body) {
                length = Math.round(this.notificationForm.body.length / 67);
            }
            if (length < 1) return 1;
            return length;

        }
    },
    methods: {
        newNotification() {
            this.notificationForm.reset();
            this.notificationId = null;
            this.viewNotificationModal = true;
        },
        submitNewNotification() {
            this.notificationForm.post(route('dashboard.notifications.store'))
                .then(response => {
                    if (!this.notificationForm.hasErrors()) {
                        this.viewNotificationModal = false;
                    }
                })
        },
        viewReceptions(notification) {
            this.receptionsForm.reset();
            this.viewReceptionsModal = true;
            this.notification = notification;
            this.receptionsForm.batch_notification_id = notification.id;
        },
        handleReceptionsFile(e) {
            this.receptionsForm.file = e.target.files[0];
        },
        submitNewReceptions() {
            this.receptionsForm.post(route('dashboard.notifications.receptions.store', {id: this.notification.id}))
                .then(response => {
                    if (!this.receptionsForm.hasErrors()) {
                        this.viewReceptionsModal = false;
                        this.notificationId = null;
                    }
                });
        },
        editNotification(notification) {
            this.notificationId = notification.id;
            this.notificationForm.title = notification.title;
            this.notificationForm.type = notification.type;
            this.notificationForm.body = notification.body;
            this.notificationForm.pattern = notification.pattern;
            this.notificationForm.status = notification.status;
            this.notificationForm.parameters = notification.parameters;
            this.viewNotificationModal = true;
        },
        updateNotification() {
            this.notificationForm.put(route('dashboard.notifications.update', {id: this.notificationId}))
                .then(response => {
                    if (!this.notificationForm.hasErrors()) {
                        this.viewNotificationModal = false;
                        this.notificationId = null;
                    }
                })
        },
        sendNotification(notification) {
            this.notification = notification;
            this.viewSendNotificationModal = true;
        },
        submitSendNotification() {
            this.sendNotificationForm.post(route('dashboard.notifications.send', {id: this.notification.id}), {
                onSuccess: () => {
                    this.viewSendNotificationModal = false;
                }
            })
        },
        statusColors(status) {
            switch (status) {
                case 0:
                    return 'bg-red-100 text-red-800';
                case 1:
                    return 'bg-green-100 text-green-800';
            }
        }
    }
}
</script>

<style scoped>

</style>
