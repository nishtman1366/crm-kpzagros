<template>
    <SettingsMain active="notificationEvents" title=" / تنظیمات اعلان ها">
        <template #settings>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="grid md:grid-cols-4 gap-3">
                                <div class="col-1 md:col-span-2">
                                    <input type="text"
                                           v-model="query"
                                           placeholder="جستجو در نام، کد الگو و متن اعلان"
                                           class="w-1/2 inline shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 sm:text-sm border-gray-300 rounded-md border">
                                    <jet-button @click.native="submitSearchForm"
                                                class="bg-blue-600 hover:bg-blue-500">
                                        جستجو
                                    </jet-button>
                                </div>
                                <div class="col-1 sm:col-span-2">
                                    <jet-button
                                        class="float-left"
                                        @click.native="newNotificationEvent">
                                        ثبت اعلان جدید
                                    </jet-button>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        عنوان
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        دریافت کننده
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        دسته بندی
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="notificationEvent in notificationEvents" :key="notificationEvent.id">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{notificationEvent.type.title}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{notificationEvent.levelText}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{notificationEvent.eventTypeText}}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(notificationEvent.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{notificationEvent.statusText}}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <button class="text-indigo-600 hover:text-indigo-900"
                                                v-on:click="editNotificationEvent(notificationEvent)">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </button>
                                        <InertiaLink method="DELETE"
                                                     :href="route('dashboard.settings.notifications.events.destroy',{eventId:notificationEvent.id})"
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

            <jet-confirmation-modal :show="submitNotificationEventModal" @close="submitNotificationEventModal = false">
                <template #title>
                    {{editNotificationEventModal ? 'ویرایش اطلاعات اعلان' : 'ثبت اعلان جدید'}}
                </template>
                <template #content>
                    <div class="mt-2">
                        <div class="my-2">
                            <select name="notification_type_id"
                                    v-model="notificationEventForm.notification_type_id"
                                    class="form-input block w-full pr-8">
                                <option value="">نوع اعلان</option>
                                <option v-for="notificationType in notificationTypes" :key="notificationType.id"
                                        :value="notificationType.id">{{notificationType.title}}
                                </option>
                            </select>
                            <jet-input-error
                                :message="notificationEventForm.error('notification_type_id')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <select name="event_type"
                                    v-model="notificationEventForm.event_type"
                                    ref="event_type"
                                    v-on:change="listEventItems"
                                    class="form-input block w-full pr-8">
                                <option value="">دسته بندی رخداد</option>
                                <option v-for="type in eventTypes" :key="type.id"
                                        :value="type.id">{{type.name}}
                                </option>
                            </select>
                            <jet-input-error
                                :message="notificationEventForm.error('event')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <select name="event"
                                    v-model="notificationEventForm.event"
                                    class="form-input block w-full pr-8">
                                <option value="">نوع رخداد</option>
                                <option v-for="event in eventItems" :key="event.id"
                                        :value="event.id">{{event.name}}
                                </option>
                            </select>
                            <jet-input-error
                                :message="notificationEventForm.error('event')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <select name="level"
                                    v-model="notificationEventForm.level"
                                    class="form-input block w-full pr-8">
                                <option value="">دریافت کننده اعلان</option>
                                <option v-for="level in levels" :key="level.id"
                                        :value="level.id">{{level.name}}
                                </option>
                            </select>
                            <jet-input-error
                                :message="notificationEventForm.error('level')"
                                class="mt-2"/>
                        </div>
                        <div class="my-2">
                            <button v-on:click="notificationEventForm.status=1"
                                    :class="notificationEventForm.status===1 ? 'bg-green-700' : 'bg-green-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                فعال
                            </button>
                            <button v-on:click="notificationEventForm.status=0"
                                    :class="notificationEventForm.status===0 ? 'bg-red-700' : 'bg-red-400'"
                                    class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                غیرفعال
                            </button>
                            <jet-input-error
                                :message="notificationEventForm.error('status')"
                                class="mt-2"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button class="ml-2" @click.native="submitNotificationEventModal = false">
                        انصراف
                    </jet-secondary-button>

                    <jet-button v-if="editNotificationEventModal" class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitEditNotificationEvent"
                                :class="{ 'opacity-25': notificationEventForm.processing }"
                                :disabled="notificationEventForm.processing">
                        ویرایش
                    </jet-button>
                    <jet-button v-else class="ml-2 bg-blue-600 hover:bg-blue-500"
                                @click.native="submitNewNotificationEvent"
                                :class="{ 'opacity-25': notificationEventForm.processing }"
                                :disabled="notificationEventForm.processing">
                        ارسال
                    </jet-button>
                </template>
            </jet-confirmation-modal>
        </template>
    </SettingsMain>
</template>

<script>
    import SettingsMain from "@/Pages/Dashboard/Settings/SettingsMain";
    import JetButton from '@/Jetstream/Button'
    import JetInputError from '@/Jetstream/InputError';
    import {Inertia} from "@inertiajs/inertia";
    import JetConfirmationModal from '@/Jetstream/ConfirmationModal';
    import JetDangerButton from '@/Jetstream/DangerButton';
    import JetSecondaryButton from '@/Jetstream/SecondaryButton'

    export default {
        name: "NotificationEvents",
        components: {SettingsMain, JetButton, JetInputError, JetConfirmationModal, JetDangerButton, JetSecondaryButton},
        props: {
            notificationEvents: Array,
            levels: Array,
            events: Array,
            eventTypes: Array,
            notificationTypes: Array,
            searchQuery: String,
        },
        data() {
            return {
                query: '',
                eventItems: '',
                submitNotificationEventModal: false,
                editNotificationEventModal: false,
                editNotificationEventId: '',
                notificationEventForm: this.$inertia.form({
                    '_method': 'POST',
                    notification_type_id: '',
                    event_type: '',
                    level: '',
                    event: '',
                    status: 1,
                }, {
                    bag: 'notificationEventForm',
                    resetOnSuccess: true
                }),
            }
        },
        mounted() {
            this.query = this.searchQuery
        },
        methods: {
            listEventItems() {
                let eventType = this.$refs.event_type.value;
                console.log(eventType);
                let eventList = [];
                for (let event of this.events) {
                    if (event.type == eventType) {
                        eventList.push({id: event.id, name: event.name});
                    }
                }
                this.eventItems = eventList;
            },
            newNotificationEvent() {
                this.editNotificationEventModal = false;
                this.notificationEventForm = this.$inertia.form({
                    '_method': 'POST',
                    notification_type_id: '',
                    event_type: '',
                    level: '',
                    event: '',
                    status: 1,
                }, {
                    bag: 'notificationEventForm',
                    resetOnSuccess: true
                });
                this.submitNotificationEventModal = true;
            },
            editNotificationEvent(notificationEvent) {
                this.notificationEventForm = this.$inertia.form({
                    '_method': 'PUT',
                    notification_type_id: notificationEvent.notification_type_id,
                    event_type: notificationEvent.event_type,
                    event: notificationEvent.event,
                    level: notificationEvent.level,
                    status: notificationEvent.status,
                }, {
                    bag: 'notificationEventForm',
                    resetOnSuccess: true
                });
                this.editNotificationEventId = notificationEvent.id;
                this.submitNotificationEventModal = true;
                this.editNotificationEventModal = true;
                setTimeout(() => {
                    this.listEventItems();
                }, 250)
            },
            submitNewNotificationEvent() {
                this.notificationEventForm.post(route('dashboard.settings.notifications.events.store')).then(response => {
                    if (!this.notificationEventForm.hasErrors()) {
                        this.submitNotificationEventModal = false;
                    }
                })
            },
            submitEditNotificationEvent() {
                this.notificationEventForm.notificationEvents = this.notificationEventList;
                this.notificationEventForm.post(route('dashboard.settings.notifications.events.update', {eventId: this.editNotificationEventId})).then(response => {
                    if (!this.notificationEventForm.hasErrors()) {
                        this.submitNotificationEventModal = false;
                        this.editNotificationEventModal = false;
                    }
                })
            },
            submitSearchForm() {
                Inertia.visit(route('dashboard.settings.notifications.events.list'), {
                    method: 'GET',
                    data: {
                        query: this.query
                    },
                })
            },
            statusColors(status) {
                switch (status) {
                    case 1:
                        return 'bg-green-100 text-green-800';
                    case 0:
                        return 'bg-red-100 text-red-800';
                }
            }
        }
    }
</script>

<style scoped>

</style>
