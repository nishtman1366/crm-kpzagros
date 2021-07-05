<template>
    <Dashboard>
        <template #breadcrumb> / پشتیبانی / مشاهده درخواست / درخواست شماره {{ticket.code | persianDigit}}</template>
        <template #dashboardContent>
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                        <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                            <div>
                                <inertia-link :href="route('dashboard.tickets.list')">
                                    <jet-button>بازگشت</jet-button>
                                </inertia-link>
                            </div>
                            <div class="flex justify-between mx-3 my-2">
                                <div class="rounded bg-gray-200 px-2 py-1 font-bold">تاریخ ارسال:
                                    {{ticket.createDate | persianDigit}}
                                </div>
                                <div class="rounded bg-gray-200 px-2 py-1 font-bold">آخرین بروزرسانی:
                                    {{ticket.updateDate | persianDigit}}
                                </div>
                                <div class="rounded bg-gray-200 px-2 py-1 font-bold">وضعیت: {{ticket.statusText}}</div>
                                <div class="rounded bg-gray-200 px-2 py-1 font-bold">واحد: {{ticket.type &&
                                    ticket.type.name}}
                                </div>
                            </div>
                            <div class="m-3 text-left">
                                <jet-button @click.native="newReply"
                                            v-if="ticket.status!==99"
                                            class="bg-green-500 hover:bg-green-400">ارسال
                                    پاسخ
                                </jet-button>
                                <jet-button @click.native="updateTicket(1)"
                                            v-if="ticket.status!==99 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.agent_id!==null)"
                                            class="bg-yellow-500 hover:bg-yellow-400">در حال بررسی
                                </jet-button>
                                <jet-button @click.native="transferTicket"
                                            v-if="ticket.status!==99 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.agent_id!==null)"
                                            class="bg-blue-500 hover:bg-blue-400">انتقال
                                </jet-button>
                                <jet-button @click.native="updateTicket(99)"
                                            v-if="ticket.status!==99 && ($page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.agent_id!==null)"
                                            class="bg-purple-500 hover:bg-purple-400">بستن
                                </jet-button>
                                <jet-button @click.native="eventsModal=true"
                                            v-if="$page.user.level==='SUPERUSER' || $page.user.level==='ADMIN' || $page.user.agent_id!==null"
                                            class="bg-indigo-500 hover:bg-indigo-400">
                                    رخدادها
                                </jet-button>
                            </div>
                            <div class="flex border border-gray-200 rounded mx-2 my-3">
                                <div class="w-1/4 py-3 bg-gray-100">
                                    <div><img class="mx-auto" :src="ticket.user.profile_photo_url"/></div>
                                    <div class="text-center font-bold">{{ticket.user && ticket.user.name}}</div>
                                </div>
                                <div class="p-3 w-full">
                                    <div class="text-lg font-bold my-3">{{ticket.title}}</div>
                                    <div class="my-3">{{ticket.body}}</div>
                                </div>
                            </div>
                            <div v-for="reply in ticket.replies" class="flex border border-gray-200 rounded mx-2 my-3">
                                <div class="w-1/4 py-3 bg-gray-100">
                                    <div><img class="mx-auto" :src="reply.user.profile_photo_url"/></div>
                                    <div class="text-center font-bold">{{reply.user && reply.user.name}}</div>
                                    <div class="text-center bg-gray-200 rounded mx-2 my-1">{{reply.createDate |
                                        persianDigit}}
                                    </div>
                                </div>
                                <div class="p-3 w-full">
                                    <div class="my-3">{{reply.body}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <jet-dialog :show="newReplyModal" @close="newReplyModal=false">
                <template #title>ثبت پاسخ</template>
                <template #content>
                    <div class="">
                        <div class="mt-3">
                            <jet-label for="body" value="متن پاسخ"/>
                            <textarea v-model="replyForm.body" id="body"
                                      class="w-full block form-input rounded-md shadow-sm"/>
                            <jet-input-error :message="replyForm.error('body')"/>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-button class="bg-green-500 hover:bg-green-400" @click.native="submitNewReply">ثبت</jet-button>
                    <jet-secondary-button @click.native="newReplyModal=false">انصراف</jet-secondary-button>
                </template>
            </jet-dialog>

            <jet-dialog :show="eventsModal" @close="eventsModal=false">
                <template #title>رویدادها</template>
                <template #content>
                    <div class="">
                        <div v-for="event in ticket.events" :key="event.id"
                             class="border-b border-gray-200 pb-2 mx-3 mb-2">
                            <p class="font-bold">{{event.title}} <span
                                class="text-gray-600 text-sm px-2">{{event.date}}</span></p>
                            <p>{{event.body}}</p>
                        </div>
                    </div>
                </template>
                <template #footer>
                    <jet-secondary-button @click.native="eventsModal=false">بستن</jet-secondary-button>
                </template>
            </jet-dialog>
            <jet-dialog :show="transferModal" @close="transferModal=false">
                <template #title>انتقال درخواست</template>
                <template #content>
                    <div class="">
                        <jet-label value="واحد پشتیبانی" for="ticket_type_id"/>
                        <select class="form-input pr-8"
                                id="ticket_type_id"
                                name="ticket_type_id"
                                v-model="ticketForm.ticket_type_id">
                            <option v-text="'انتخاب کنید:'"/>
                            <option v-for="type in types" :key="type.id" :value="type.id" v-text="type.name"/>
                        </select>
                    </div>
                </template>
                <template #footer>
                    <jet-button class="bg-green-500 hover:bg-green-400" @click.native="updateTicket(4)">ثبت
                    </jet-button>
                    <jet-secondary-button @click.native="transferModal=false">بستن</jet-secondary-button>
                </template>
            </jet-dialog>
        </template>
    </Dashboard>
</template>

<script>
    import Dashboard from "@/Pages/Dashboard";
    import JetButton from "@/Jetstream/Button"
    import JetDialog from "@/Jetstream/DialogModal"
    import JetSecondaryButton from "@/Jetstream/SecondaryButton"
    import JetLabel from "@/Jetstream/Label"
    import JetInputError from "@/Jetstream/InputError"

    export default {
        name: "ViewTicket",
        components: {Dashboard, JetButton, JetDialog, JetSecondaryButton, JetLabel, JetInputError},
        props: {
            ticket: Object,
            types: Array
        },
        data() {
            return {
                newReplyModal: false,
                eventsModal: false,
                transferModal: false,
                replyForm: this.$inertia.form({
                    body: null,
                }),
                ticketForm: this.$inertia.form({
                    status: null,
                    ticket_type_id: this.ticket.ticket_type_id,
                })
            }
        },
        methods: {
            newReply() {
                this.replyForm.reset();
                this.newReplyModal = true;
            },
            submitNewReply() {
                this.replyForm.post(route('dashboard.tickets.reply.store', {id: this.ticket.id}))
                    .then(response => {
                        if (!this.replyForm.hasErrors()) {
                            this.newReplyModal = false;
                        }
                    })
            },
            updateTicket(status) {
                this.ticketForm.status = status;
                this.ticketForm.put(route('dashboard.tickets.update', {id: this.ticket.id}))
                    .then(response => {

                    })
            },
            transferTicket() {
                this.transferModal = true;
            },
        }
    }
</script>

<style scoped>

</style>
