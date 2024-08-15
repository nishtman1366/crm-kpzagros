<template>
    <guest>
        <template #header>
            <div class="text-2xl">ثبت درخواست تعمیرات</div>
        </template>
        <template #contents>
            <div class="w-full lg:w-4/5 xl:w-2/3 mx-auto">
                <div class="grid grid-cols-2 gap-3">
                    <div v-if="repair_message" class="col-span-2">
                        <div class="bg-green-200 p-1 rounded-md">
                            <div class="text-green-900 text-center text-lg my-4">{{repair_message[0]}}</div>
                            <div class="text-center text-base">{{repair_message[1]}}</div>
                            <div class="text-green-900 text-center text-2xl">{{repair_message[2]}}</div>
                            <div class="mty-4 text-center">{{repair_message[3]}}</div>
                        </div>
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <InertiaLink class="h-full" :href="route('public.repairs.create')">
                            <div class="h-full p-3 bg-gray-300 transform transition-all hover:scale-90 duration-300">
                                <div class="h-full text-center text-lg flex items-center justify-center">
                                    ثبت درخواست جدید
                                </div>
                            </div>
                        </InertiaLink>
                    </div>
                    <div class="col-span-2 md:col-span-1 p-3 bg-gray-300">
                        <div class="text-center text-lg">
                            پیگیری درخواست
                        </div>
                        <div class="w-full">
                            <div v-show="repairTrackingForm.error('report')">
                                <p class="my-4 text-lg text-center font-bold text-red-600">
                                    {{ repairTrackingForm.error('report') }}
                                </p>
                            </div>
                            <div>
                                <jet-label for="national_code" value="شماره ملی"/>
                                <jet-input id="national_code"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="repairTrackingForm.national_code"
                                           ref="national_code"
                                           autocomplete="national_code"/>
                                <jet-input-error :message="repairTrackingForm.error('national_code')" class="mt-2"/>
                            </div>
                            <div>
                                <jet-label for="tracking_code" value="کد رهگیری"/>
                                <jet-input id="tracking_code"
                                           type="text"
                                           class="mt-1 block w-full"
                                           v-model="repairTrackingForm.tracking_code"
                                           ref="tracking_code"
                                           autocomplete="tracking_code"/>
                                <jet-input-error :message="repairTrackingForm.error('tracking_code')" class="mt-2"/>
                            </div>
                            <div class="mt-4 text-center">
                                <jet-button @click.native="submitRepairTrackingForm">ارسال</jet-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </guest>
</template>

<script>
import Guest from "@/Layouts/Guest";
import JetActionMessage from "@/Jetstream/ActionMessage";
import JetButton from "@/Jetstream/Button";
import JetFormSection from "@/Jetstream/FormSection";
import JetInput from "@/Jetstream/Input";
import JetInputError from "@/Jetstream/InputError";
import JetLabel from "@/Jetstream/Label";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetSectionBorder from "@/Jetstream/SectionBorder";
import VuePersianDatetimePicker from "vue-persian-datetime-picker";

export default {
    name: "Index",
    components: {
        Guest,
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
        repair_message: Array,
        national_code: Number,
        tracking_code: Number
    },
    data() {
        return {
            repairTrackingForm: this.$inertia.form({
                '_method': 'POST',
                national_code: this.national_code ?? '',
                tracking_code: this.tracking_code ?? '',
            }, {
                bag: 'repairTrackingForm',
                resetOnSuccess: false
            })

        }
    },
    methods: {
        submitRepairTrackingForm() {
            this.repairTrackingForm.post(route('public.repairs.checkRepair')).then(response => {
                if (!this.repairTrackingForm.hasErrors()) {

                }
            })
        },
    }
}
</script>

<style scoped>

</style>
