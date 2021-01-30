<template>
    <Dashboard>
        <template #breadcrumb> / گزارشات / دستگاه ها</template>
        <template #dashboardContent>
            <div class="grid md:grid-cols-4 gap-3">
                <div class="bg-red-500 rounded text-white p-3">
                    <p class="text-xl mb-3">وضعیت سلامت</p>
                    <p class="inline-block mr-1 float-right">سالم: {{ devicesSummary.physicalStatus[0] | persianDigit }} عدد</p>
                    <p class="inline-block ml-1 float-left">خراب: {{ devicesSummary.physicalStatus[1] | persianDigit }} عدد</p>
                </div>
                <div class="bg-blue-500 rounded text-white p-3">
                    <p class="text-xl mb-3">وضعیت انبار</p>
                    <p class="inline-block mr-1 float-right">سالم: {{ devicesSummary.transportStatus[0] | persianDigit }} عدد</p>
                    <p class="inline-block ml-1 float-left">خراب: {{ devicesSummary.transportStatus[1] | persianDigit }} عدد</p>
                </div>
                <div class="bg-green-500 rounded text-white p-3">
                    <p class="text-xl mb-3">وضعیت سرویس دهنده</p>
                    <p class="inline-block mr-1 float-right">سالم: {{ devicesSummary.pspStatus[0] | persianDigit }} عدد</p>
                    <p class="inline-block ml-1 float-left">خراب: {{ devicesSummary.pspStatus[1] | persianDigit }} عدد</p>
                </div>
                <div class="md:col-span-2">

                </div>
                <div class="md:col-span-2">

                </div>
            </div>
        </template>
    </Dashboard>
</template>

<script>
import Dashboard from "@/Pages/Dashboard";

export default {
    name: "Devices",
    components: {Dashboard},
    props: {
        devicesSummary: Object
    }
}
</script>

<style scoped>

</style>
