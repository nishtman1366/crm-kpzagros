<template>
    <Dashboard>
        <template #breadcrumb> / پشتیبانی / لیست درخواست ها</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div class="flex justify-between item-center">
                                <div class="flex justify-start"
                                     v-if="$page.user.agent_id || $page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'">
                                    <div class="mx-8 w-1/4">
                                        <jet-label for="query" value="جستجو در کد پیگیری"/>
                                        <jet-input type="text" id="query" name="query"
                                                   @keypress.enter.native="submitSearchForm" ref="query"
                                                   v-model="query"/>
                                    </div>
                                    <div class="mx-8 w-1/4">
                                        <jet-label for="type_id" value="واحد پشتیبانی"/>
                                        <select name="type_id"
                                                size="1"
                                                ref="type_id"
                                                id="type_id"
                                                v-model="type_id"
                                                v-on:change="submitSearchForm"
                                                class="w-full mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">>
                                            <option :value="null" v-text="'واحد پشتیبانی'"/>
                                            <option v-for="type in types" :key="type.id" :value="type.id"
                                                    v-text="type.name"/>
                                        </select>
                                    </div>
                                    <div class="mx-8 w-1/4">
                                        <jet-label for="status_id"
                                                   value="وضعیت "/>
                                        <select ref="status_id"
                                                size="1"
                                                v-model="status_id"
                                                id="status_id"
                                                v-on:change="submitSearchForm"
                                                class="w-full mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">>
                                            <option :value="255" v-text="'وضعیت'"/>
                                            <option v-for="(value,index) in statuses" :key="index" :value="index"
                                                    v-text="value"/>
                                        </select>
                                    </div>
                                    <div class="mx-8 w-1/4">
                                        <jet-label for="agent_id" value="کاربر"/>
                                        <select name="agent_id" size="1" id="agent_id"
                                                ref="agent_id"
                                                v-model="agent_id"
                                                v-on:change="submitSearchForm"
                                                class="w-full mt-1 inline py-2 px-6 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">>
                                            <option :value="null" v-text="'کاربر'"/>
                                            <option v-for="user in agentsList" :key="user.id" :value="user.id"
                                                    v-text="user.name"/>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <jet-button
                                        @click.native="newTicket">
                                        ثبت درخواست جدید
                                    </jet-button>
                                </div>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        نام کاربر
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        واحد پشتیبانی
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        موضوع
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        وضعیت
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        تاریخ درخواست
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        آخرین تغییر وضعیت
                                    </th>
                                    <th scope="col" class="px-6 py-3 bg-gray-50">عملیات</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{ ticket.user && ticket.user.name }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{ ticket.type && ticket.type.name }}
                                    </td>
                                    <td class="px-6 py-4 text-center">{{ ticket.code | persianDigit }} -
                                        {{ ticket.title }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <span
                                            :class="statusColors(ticket.status)"
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          {{ ticket.statusText }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{ ticket.createDate }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        {{ ticket.updateDate }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-900">
                                        <inertia-link :href="route('dashboard.tickets.view',{id:ticket.id})"
                                                      class="text-indigo-600 hover:text-indigo-900">
                                            <svg style="display:inline;width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M23.5,17L18.5,22L15,18.5L16.5,17L18.5,19L22,15.5L23.5,17M12,9A3,3 0 0,1 15,12A3,3 0 0,1 12,15A3,3 0 0,1 9,12A3,3 0 0,1 12,9M12,17C12.5,17 12.97,16.93 13.42,16.79C13.15,17.5 13,18.22 13,19V19.45L12,19.5C7,19.5 2.73,16.39 1,12C2.73,7.61 7,4.5 12,4.5C17,4.5 21.27,7.61 23,12C22.75,12.64 22.44,13.26 22.08,13.85C21.18,13.31 20.12,13 19,13C18.22,13 17.5,13.15 16.79,13.42C16.93,12.97 17,12.5 17,12A5,5 0 0,0 12,7A5,5 0 0,0 7,12A5,5 0 0,0 12,17Z"/>
                                            </svg>
                                        </inertia-link>
                                        <InertiaLink method="DELETE"
                                                     v-if="$page.user.level==='ADMIN' || $page.user.level==='SUPERUSER'"
                                                     :href="route('dashboard.tickets.destroy',{id:ticket.id})"
                                                     class="text-red-600 hover:text-red-900">
                                            <svg class="inline" style="width:24px;height:24px" viewBox="0 0 24 24">
                                                <path fill="currentColor"
                                                      d="M19,4H15.5L14.5,3H9.5L8.5,4H5V6H19M6,19A2,2 0 0,0 8,21H16A2,2 0 0,0 18,19V7H6V19Z"/>
                                            </svg>
                                        </InertiaLink>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <pagination
                                            :urlsArray="paginatedLinks"
                                            :totalRows="tickets.total"
                                            :previousPageUrl="tickets.prev_page_url"
                                            :nextPageUrl="tickets.next_page_url"/>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <jet-dialog :show="newTicketModal" @close="newTicketModal=false">
                <template #title>ثبت درخواست جدید</template>
                <template #content>
                    <div class="">
                        <div v-if="$page.user.agent_id" class="mt-3 flex justify-start">
                            <div class="w-1/2 ml-1">
                                <jet-label for="user_type" value="نوع کاربر"/>
                                <select name="user_type" id="user_type"
                                        v-model="userTypeId"
                                        class="w-full pr-8 block form-input rounded-md shadow-sm">
                                    <option value=""/>
                                    <option v-for="type in userTypes" :key="type.id" :value="type.id"
                                            v-text="type.name"/>
                                </select>
                            </div>
                            <div class="w-1/2 ml-1">
                                <jet-label for="user_id" value="انتخاب کاربر"/>
                                <select name="user_id" id="user_id"
                                        v-model="ticketForm.user_id"
                                        class="w-full pr-8 block form-input rounded-md shadow-sm">
                                    <option value=""/>
                                    <option v-for="user in usersList" :key="user.id" :value="user.id"
                                            v-text="user.name"/>
                                </select>
                                <jet-input-error :message="ticketForm.error('user_id')"/>
                            </div>
                        </div>
                        <div class="mt-3 flex justify-start">
                            <div class="w-1/2 ml-1">
                                <jet-label for="ticket_type_id" value="واحد پشتیبانی"/>
                                <select name="ticket_type_id" id="ticket_type_id"
                                        v-model="ticketType"
                                        :disabled="$page.user.ticket_type_id"
                                        class="w-full pr-8 block form-input rounded-md shadow-sm">
                                    <option value=""/>
                                    <option v-for="type in types" :key="type.id" :value="type.id" v-text="type.name"/>
                                </select>
                                <jet-input-error :message="ticketForm.error('ticket_type_id')"/>
                            </div>
                            <div class="w-1/2 mr-1">
                                <jet-label for="agent_id" value="کاربر پشتیبانی"/>
                                <select name="agent_id" id="agent_id"
                                        :disabled="$page.user.agent_id"
                                        v-model="ticketForm.agent_id"
                                        class="w-full pr-8 block form-input rounded-md shadow-sm">
                                    <option value=""/>
                                    <option v-for="agent in agentsList" :key="agent.id" :value="agent.id"
                                            v-text="agent.name"/>
                                </select>
                                <jet-input-error :message="ticketForm.error('agent_id')"/>
                            </div>
                        </div>
                        <div class="mt-3">
                            <jet-label for="title" value="موضوع"/>
                            <jet-input type="text" v-model="ticketForm.title" id="title" class="w-full block"/>
                            <jet-input-error :message="ticketForm.error('title')"/>
                        </div>
                        <div class="mt-3">
                            <jet-label for="body" value="متن درخواست"/>
                            <textarea v-model="ticketForm.body" id="body"
                                      class="w-full block form-input rounded-md shadow-sm"/>
                            <jet-input-error :message="ticketForm.error('body')"/>
                        </div>
                        <div class="mt-3" v-if="$page.user.agent_id">
                            <jet-label for="password" value="رمز پیام"/>
                            <jet-input type="text" v-model="ticketForm.password" id="password"
                                      class="w-full block form-input rounded-md shadow-sm"/>
                            <jet-input-error message="در صورتی که میخواهید پیام شما بوسیله رمز حفاظت شود رمز مورد نظر خود را وارد نمایید. این رمز فقط هنگام مشاهده پیام توسط نمایندگان یا بازاریابان موثر خواهد بود."/>
                        </div>
                        <div class="mt-3 text-left">
                            <jet-button @click.native="$refs.files.click()">انتخاب فایل پیوست</jet-button>
                            <input class="hidden" ref="files" type="file" multiple name="files" id="files"
                                   @change="handleTicketFiles"/>
                            <jet-input-error :message="ticketForm.error('files')"/>
                        </div>
                        <div class="mt-3" v-if="ticketForm.files.length > 0">
                            <div class="rounded border border-gray-200 m-1 px-2 py-1 h-16 overflow-y-auto">
                                <ul>
                                    <li v-for="(file,index) in ticketForm.files" :key="file"
                                        class="w-full flex justify-between">
                                        <span class="w-3/4 truncate">{{ file.name }}</span>
                                        <span class="text-left" style="direction:ltr">{{ file.size }} bytes</span>
                                        <span @click="deleteFile(index)"
                                              class="text-red-500 hover:text-red-400 cursor-pointer">حذف</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-button class="bg-green-500 hover:bg-green-400" @click.native="submitNewTicket">ثبت</jet-button>
                    <jet-secondary-button @click.native="newTicketModal=false">انصراف</jet-secondary-button>
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
import Pagination from "@/Pages/Dashboard/Components/Pagination";
import {Inertia} from "@inertiajs/inertia";

export default {
    name: "List",
    components: {
        Dashboard, JetButton, JetSecondaryButton, JetDialog, JetLabel, JetInput, JetInputError, Pagination,
    },
    props: {
        tickets: Object,
        types: Array,
        agents: Array,
        statuses: Object,
        agentUsers: Array,
        userTypes: Array,
        users: Array,
        paginatedLinks: Array,
        agentId: Number,
        statusId: Number,
        typeId: Number,
        searchQuery: String,
    },
    data() {
        return {
            newTicketModal: false,
            userTypeId: null,
            ticketType: this.$page.user.ticket_type_id,
            agentsList: [],
            usersList: [],
            ticketForm: this.$inertia.form({
                user_id: null,
                title: null,
                ticket_type_id: this.$page.user.ticket_type_id,
                agent_id: this.$page.user.agent_id,
                body: null,
                password: null,
                files: []
            }, {
                bag: 'ticketForm',
                resetOnSuccess: true
            }),

            agent_id: this.agentId,
            type_id: this.typeId,
            status_id: this.statusId,
            query: this.searchQuery,

        }
    },
    mounted() {
        if (this.typeId) {
            this.agentsList = this.agents.filter(agent => {
                return agent.ticket_type_id === this.typeId;
            });
        }
    },
    watch: {
        ticketType: function (val) {
            this.ticketForm.ticket_type_id = val;
            this.agentsList = this.agents.filter(agent => {
                return agent.ticket_type_id === val;
            });
        },
        userTypeId: function (val) {
            this.usersList = this.users.filter(user => {
                return user.level === val;
            });
        }
    },
    methods: {
        newTicket() {
            this.ticketForm.reset();
            this.newTicketModal = true;
        },
        handleTicketFiles(e) {
            let files = e.target.files;
            for (let i = 0; i < files.length; i++) {
                this.ticketForm.files.push(files[i]);
            }
        },
        deleteFile(index) {
            this.ticketForm.files.splice(index, 1);
        },
        submitNewTicket() {
            this.ticketForm.post(route('dashboard.tickets.store'))
                .then(response => {
                    if (!this.ticketForm.hasErrors()) {
                        this.newTicketModal = false;
                    }
                });
        },
        statusColors(status) {
            switch (status) {
                case 0://ثبت شده
                    return 'bg-blue-100 text-blue-800';
                case 1://در حال بررسی
                    return 'bg-yellow-100 text-yellow-800';
                case 2://پاسخ داده شده
                    return 'bg-green-100 text-green-800';
                case 3://پاسخ کاربر
                    return 'bg-blue-100 text-blue-800';
                case 99://بسته شده
                    return 'bg-gray-100 text-gray-800';
            }
        },
        submitSearchForm() {
            Inertia.visit(route('dashboard.tickets.list'), {
                method: 'get',
                data: {
                    query: this.query,
                    statusId: this.status_id,
                    agentId: this.agent_id,
                    typeId: this.type_id,
                },
            })
        }
    }
}
</script>

<style scoped>

</style>
