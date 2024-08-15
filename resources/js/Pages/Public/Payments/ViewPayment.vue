<template>
    <guest>
        <template #header>
            <div class="text-2xl">گزارش پرداخت</div>
        </template>
        <template #contents>
            <div class="w-full text-base lg:w-1/3 mx-auto">
                <div class="flex items-center justify-between border-b pb-1 mt-2">
                    <div>مبلغ:</div>
                    <div>{{ payment.priceText }}</div>
                </div>
                <div class="flex items-center justify-between border-b pb-1 mt-2">
                    <div>زمان:</div>
                    <div>{{ payment.jDate }}</div>
                </div>
                <div class="flex items-center justify-center border-b pb-1 mt-2">
                    <div
                        :class="`${payment.status===255 ? 'bg-red-700' : 'bg-green-700'} rounded-lg ly-2 px-3 text-white`">
                        {{ payment.statusText }}
                    </div>
                </div>
                <template v-if="payment.status===2">
                    <div class="flex items-center justify-between border-b pb-1 mt-2">
                        <div>شماره پیگیری پرداخت:</div>
                        <div>{{ payment.ref_code }}</div>
                    </div>
                </template>
                <template v-else>
                    <div class="flex items-center justify-between border-b pb-1 mt-2">
                        <div>نتیجه پرداخت:</div>
                        <div>{{ payment.description }}</div>
                    </div>
                </template>
                <div v-if="route" class="text-center pt-4">
                    <a class="bg-green-700 text-green-100 hover:bg-green-600 transition px-3 py-2 rounded-md" :href="route">بازگشت</a>
                </div>
            </div>
        </template>
    </guest>
</template>

<script>
import Guest from "@/Layouts/Guest";

export default {
    name: "ViewPayment",
    components: {
        Guest,
    },
    props: {
        payment: Object,
        route: String
    }
}
</script>

<style scoped>

</style>
