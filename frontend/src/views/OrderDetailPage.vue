<script setup>
import { onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import { detailOrder } from "../services/order";
import { formatCurrency, formatDate } from "../utils/format";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faArrowLeft } from "@fortawesome/free-solid-svg-icons";
import router from "../router/index.js";

const route = useRoute();

const loading = ref(false);
const data = ref(null);
const errorMessage = ref("");
const orderItems = ref([])




const getTime = (time) => {
    const date = new Date(time);
    const timeWithAmPm = date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
    return timeWithAmPm;
}

async function loadOrder() {
    loading.value = true;
    console.log(data)

    try {
        const orderId = route.params.id;

        const response = await detailOrder(orderId);

        data.value = response.data;
        orderItems.value = response.data.items
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message ||
            error.message ||
            "Failed to load order.";
    } finally {
        loading.value = false;
    }
}

onMounted(() => {
    loadOrder();
});
</script>

<template>
    <div class="p-4">
        <div v-if="loading">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm items-center">
                <p>Loading order...</p>
            </div>
        </div>
        <p v-else-if="errorMessage" class="text-red-500">
            {{ errorMessage }}
        </p>

        <div v-if="data">
            <div class="flex flex-row items-center p-4 gap-2">
                <button @click="router.push({ name: 'orders' })">
                    <FontAwesomeIcon :icon="faArrowLeft" />
                </button>

                <div>
                    <p>
                        <span class="font-semibold text-gray-900">Order Number: </span>
                        <span class="font-semibold text-blue-500">
                            {{ data.order_number }}
                        </span>
                    </p>
                </div>
            </div>

            <div class="grid md:grid-cols-2 grid-cols-1 gap-4">
                <div class="flex flex-col gap-4">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden p-4">
                        <div
                            class="grid grid-cols-12 gap-2 border-b border-gray-200 px-2 pb-3 text-gray-500 font-medium">
                            <div class="col-span-5">Item summary</div>
                            <div class="col-span-1 text-center text-sm">Qty</div>
                            <div class="col-span-3 text-right text-sm">Price</div>
                            <div class="col-span-3 text-right text-sm">Subtotal</div>
                        </div>

                        <!-- Items -->
                        <div v-for="(item, index) in orderItems" :key="index"
                            class="grid grid-cols-12 items-center gap-2 border-b border-gray-100 px-2 py-4 text-xs last:border-0">
                            <div class="col-span-5 font-medium text-gray-800">
                                {{ item.product_name }}
                            </div>

                            <div class="col-span-1 text-center text-gray-600">
                                {{ item.quantity }}
                            </div>

                            <div class="col-span-3 text-right text-gray-600 tabular-nums">
                                {{ formatCurrency(item.price) }}
                            </div>

                            <div class="col-span-3 text-right font-medium text-gray-800 tabular-nums">
                                {{ formatCurrency(item.subtotal) }}
                            </div>
                        </div>

                        <div v-if="orderItems.length === 0" class="py-8 text-center text-sm text-gray-400">
                            No order items found.
                        </div>
                    </div>

                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden p-4">
                        <p class="font-medium mb-2 border-b pb-3 border-gray-200 ">Customer Details</p>
                        <div class="flex flex-col text-xs gap-1">
                            <div class="flex flex-row justify-between">
                                <p class="font-medium text-xs">Customer Name</p>
                                <p>{{ data.customer.name }}</p>
                            </div>
                            <div class="flex flex-row justify-between">
                                <p class="font-medium text-xs">Phone Number</p>
                                <p>{{ data.customer.phone_number }}</p>
                            </div>

                        </div>


                    </div>
                </div>



                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                    <p class="font-semibold text-gray-800 mb-4">
                        Order Summary
                    </p>

                    <div class="flex flex-col gap-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Order Created</span>
                            <span class="font-medium text-gray-800">
                                {{ formatDate(data.created_at) }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Order Time</span>
                            <span class="font-medium text-gray-800">
                                {{ formatDate(data.created_at) }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium text-gray-800">
                                {{ formatCurrency(data.subtotal) }}
                            </span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-500">Tax (11%)</span>
                            <span class="font-medium text-gray-800">
                                {{ formatCurrency(data.tax_amount) }}
                            </span>
                        </div>

                        <div class="border-t border-gray-200 pt-3 mt-1 flex justify-between">
                            <span class="font-semibold text-gray-800">Grand Total</span>
                            <span class="font-bold text-blue-600">
                                {{ formatCurrency(data.grand_total) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>