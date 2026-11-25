<script setup>
import { ref, reactive, computed, onMounted, watch } from "vue";
import Alert from "../components/common/Alert.vue";
import { cancelOrder, detailOrder, getOrders, payOrder } from "../services/order.js";
import router from "../router/index.js";
import { formatCurrency } from "../utils/format.js";
import { VueDatePicker } from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faCancel, faEllipsis, faInfo, faMoneyBill, faPrint } from "@fortawesome/free-solid-svg-icons";
import Confirmation from "../components/common/Confirmation.vue";
import { useRoute, useRouter } from "vue-router";
import Select from "../components/common/Select.vue"
import Menu from "../components/common/Menu.vue";

// STATE
const route = useRoute();
const orders = ref([]);
const loading = ref(false);
const selectedStatus = ref("")
const openMenu = ref(false);

const search = ref("");
const showModal = ref(false);

const errorMessage = ref("");
const successMessage = ref(route.query.success || "");
const alertType = ref(route.query.success ? "Success" : "Info");

if (route.query.success) {
    router.replace({ query: {} });
}
const errors = ref({});

const searchDate = ref([null]);

//state modal
const showConfirm = ref(false);
const selectedOrderId = ref(null);

const pagination = ref({
    page: 1,
    per_page: 10,
    total: 0,
});

const form = reactive({
    name: "",
    email: "",
    phone_number: "",
});

const statusOptions = [
    { value: "", label: "All Status" },
    { value: "pending", label: "Pending" },
    { value: "cancelled", label: "Cancelled" },
    { value: "paid", label: "Paid" },
];

const lastPage = computed(() =>
    Math.max(1, Math.ceil(pagination.value.total / pagination.value.per_page))
);

const activeMenuId = ref(null);

const menuPosition = ref({
    top: 0,
    left: 0,
});

const toggleMenu = (event, orderId) => {
    if (activeMenuId.value === orderId && openMenu.value) {
        openMenu.value = false;
        activeMenuId.value = null;
        return;
    }

    const rect = event.currentTarget.getBoundingClientRect();

    const menuWidth = 160;
    const menuHeight = 120;
    const gap = 10;

    menuPosition.value = {
        top: rect.bottom + menuHeight + gap > window.innerHeight
            ? rect.top - menuHeight - gap
            : rect.bottom + gap,

        left: Math.max(
            8,
            Math.min(rect.right - menuWidth, window.innerWidth - menuWidth - 8)
        ),
    };

    activeMenuId.value = orderId;
    openMenu.value = true;
};

const openCreate = () => {
    router.push("/orders/create")
}

const getActionItems = (order) => [
    ...(order.status === "PENDING"
        ? [{
            label: "Pay",
            action: () => {
                openMenu.value = false;
                payOrders(order.id);
            },
        }]
        : []),

    ...(["PENDING", "PAID"].includes(order.status)
        ? [{
            label: "Cancel",
            action: () => {
                openMenu.value = false;
                cancelOrders(order.id);
            },
        }]
        : []),

    {
        label: "Detail",
        action: () => {
            openMenu.value = false;
            router.push(`/orders/${order.id}`)
        },
    },
];

const formatDate = (date) => {
    if (!date) return null;

    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, "0");
    const day = String(date.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`;
};


async function payOrders(orderId) {
    loading.value = true;
    successMessage.value = "";
    errorMessage.value = "";

    try {
        const response = await payOrder(orderId);
        successMessage.value = response.message;
        await loadOrders(pagination.value.page);
    } catch (error) {
        console.error("Failed to confirm payment:", error);

        errorMessage.value =
            error.response?.data?.message ||
            error.message ||
            "Failed to accept payment.";
    } finally {
        loading.value = false;
    }
}

function cancelOrders(orderId) {
    selectedOrderId.value = orderId;
    showConfirm.value = true;
}

async function confirmCancelOrder() {
    if (!selectedOrderId.value) return;

    loading.value = true;
    successMessage.value = "";
    errorMessage.value = "";

    try {
        const response = await cancelOrder(selectedOrderId.value);

        showConfirm.value = false;
        selectedOrderId.value = null;

        await loadOrders(1);

        successMessage.value = response.message;

    } catch (error) {
        errorMessage.value =
            error.response?.data?.message ||
            error.message ||
            "Failed to cancel order.";
    } finally {
        loading.value = false;
    }
}

// GET orders
async function loadOrders(page = 1) {
    loading.value = true;
    errorMessage.value = "";
    orders.value = [];

    try {
        const response = await getOrders({
            page,
            search: search.value,
            start_date: searchDate.value?.[0]
                ? formatDate(searchDate.value[0])
                : null,
            end_date: searchDate.value?.[1]
                ? formatDate(searchDate.value[1])
                : null,
            status: selectedStatus.value
        });

        const result = response.data;

        if (!result.success) {
            throw new Error(result.message || "Failed to load orders.");
        }

        orders.value = result.data;
        pagination.value = result.meta;

    } catch (error) {
        console.error("Failed to load orders:", error);

        errorMessage.value =
            error.response?.data?.message ||
            error.message ||
            "Failed to load orders.";
    } finally {
        loading.value = false;
    }
}

// SEARCH WITH DEBOUNCE
let searchTimeout;

watch([selectedStatus, searchDate], () => {
    loadOrders(1);
});

// PAGINATION
function changePage(page) {
    if (
        page < 1 ||
        page > lastPage.value ||
        loading.value
    ) {
        return;
    }

    loadOrders(page);
}

const getTotalItems = (items) => {
    return items.reduce((total, item) => total + item.quantity, 0);
};

onMounted(() => {
    loadOrders();
});
</script>

<template>
    <div class="p-6 md:p-8 space-y-6">

        <!-- HEADER -->
        <div class="flex flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Orders
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Manage your store orders.
                </p>
            </div>

            <button @click="router.push({ name: 'order-create' })"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium md:">
                + Add Order
            </button>
        </div>

        <!-- SUCCESS MESSAGE -->
        <div v-if="successMessage">
            <Alert :type="alertType" title="Success" :message="successMessage" @close="successMessage = ''" />
        </div>

        <!-- ERROR MESSAGE -->
        <div v-if="errorMessage && !showModal" class="p-3 rounded-lg bg-red-50 text-red-600 text-sm">
            <Alert type="error" title="Error" :message="errorMessage" @close="errorMessage = ''" />
        </div>

        <!-- order TABLE -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">

            <!-- SEARCH -->
            <div class="flex flex-row p-4 border-b border-gray-100 gap-4">
                <VueDatePicker v-model="searchDate" range :enable-time-picker="true" :format="'dd/MM/yyyy HH:mm'"
                    placeholder="Select date range" auto-apply :clearable="true" class="w-[50%]" />
                <Select class="w-30" v-model="selectedStatus" :options="statusOptions"></Select>

            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-4">No.</th>
                            <th class="px-5 py-4">Order Name</th>
                            <th class="px-5 py-4">Name</th>
                            <th class="px-5 py-4">Total Item</th>
                            <th class="px-5 py-4">Total</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        <tr v-if="loading">
                            <td colspan="12" class="text-center py-10 text-gray-400">
                                Loading orders...
                            </td>
                        </tr>

                        <tr v-else-if="orders.length === 0">
                            <td colspan="12" class="text-center py-10 text-gray-400">
                                No orders found.
                            </td>
                        </tr>

                        <tr v-for="(order, index) in orders" :key="order.id" class="hover:bg-gray-50">
                            <td class="px-5 py-4 text-gray-500">
                                {{
                                    (pagination.page - 1) *
                                    pagination.per_page + index + 1
                                }}
                            </td>

                            <td class="px-5 py-4 font-medium text-gray-800">
                                {{ order.order_number }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                {{ order.customer.name }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">{{ getTotalItems(order.items) }} items</td>

                            <td class="px-5 py-4 text-gray-600">
                                {{ formatCurrency(order.grand_total) }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                {{ order.status }}
                            </td>

                            <td class=" px-5 py-4">
                                <button type="button" @click="toggleMenu($event, order.id)">
                                    <FontAwesomeIcon :icon="faEllipsis" />
                                </button>

                                <Menu v-if="openMenu && activeMenuId === order.id" :items="getActionItems(order)"
                                    :position="menuPosition" />
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 border-t border-gray-100">
                <p class="text-sm text-gray-500">
                    Total {{ pagination.total }} orders
                </p>

                <div class="flex items-center gap-2">
                    <button @click="changePage(pagination.page - 1)" :disabled="pagination.page <= 1 || loading"
                        class="px-3 py-2 border rounded-lg text-sm disabled:opacity-40">
                        Previous
                    </button>

                    <span class="text-sm text-gray-600">
                        {{ pagination.page }} / {{ lastPage }}
                    </span>

                    <button @click="changePage(pagination.page + 1)" :disabled="pagination.page >= lastPage || loading"
                        class="px-3 py-2 border rounded-lg text-sm disabled:opacity-40">
                        Next
                    </button>
                </div>
            </div>
        </div>

    </div>
    <Confirmation :show="showConfirm" title="Cancel" message="Are you sure?" :loading="loading"
        @close="showConfirm = false" @confirm="confirmCancelOrder">
    </Confirmation>
</template>