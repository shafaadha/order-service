<script setup>
import { ref, reactive, computed, onMounted, watch } from "vue";
import {
    getCustomers,
    createCustomer,
} from "../services/customer";
import Alert from "../components/common/Alert.vue";

// STATE
const customers = ref([]);
const loading = ref(false);
const saving = ref(false);

const search = ref("");
const showModal = ref(false);

const errorMessage = ref("");
const successMessage = ref("");
const errors = ref({});

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

const lastPage = computed(() =>
    Math.max(1, Math.ceil(pagination.value.total / pagination.value.per_page))
);

// GET CUSTOMERS
async function loadCustomers(page = 1) {
    loading.value = true;
    errorMessage.value = "";

    try {
        const response = await getCustomers({
            page,
            search: search.value,
        });

        const result = response.data;

        if (!result.success) {
            throw new Error(result.message || "Failed to load customers.");
        }

        customers.value = result.data;
        pagination.value = result.meta;

    } catch (error) {
        console.error(
            "Failed to load customers:",
            error.response?.data || error
        );

        errorMessage.value =
            error.response?.data?.message ||
            error.message ||
            "Failed to load customers.";
    } finally {
        loading.value = false;
    }
}

// SEARCH WITH DEBOUNCE
let searchTimeout;

watch(search, () => {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        loadCustomers(1);
    }, 500);
});

// RESET FORM
function resetForm() {
    Object.assign(form, {
        name: "",
        email: "",
        phone_number: "",
    });

    errors.value = {};
}

// OPEN MODAL
function openCreateModal() {
    resetForm();
    errorMessage.value = "";
    showModal.value = true;
}

// CLOSE MODAL
function closeModal() {
    showModal.value = false;
    resetForm();
}

// CREATE CUSTOMER
async function saveCustomer() {
    errorMessage.value = "";
    successMessage.value = "";
    errors.value = {};

    saving.value = true;

    try {
        const payload = {
            name: form.name,
            email: form.email,
            phone_number: form.phone_number,
        };

        const response = await createCustomer(payload);
        const result = response.data;

        if (!result.success) {
            throw new Error(result.message || "Failed to create customer.");
        }

        successMessage.value = result.message;

        closeModal();
        await loadCustomers(1);

    } catch (error) {
        console.error(
            "Failed to create customer:",
            error.response?.data || error
        );

        errorMessage.value =
            error.response?.data?.message ||
            error.message ||
            "Something went wrong.";

        errors.value = error.response?.data?.errors || {};
    } finally {
        saving.value = false;
    }
}

// PAGINATION
function changePage(page) {
    if (
        page < 1 ||
        page > lastPage.value ||
        loading.value
    ) {
        return;
    }

    loadCustomers(page);
}

onMounted(() => {
    loadCustomers();
});
</script>

<template>
    <div class="p-6 md:p-8 space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Customers
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Manage your store customers.
                </p>
            </div>

            <button @click="openCreateModal"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium">
                + Add Customer
            </button>
        </div>

        <!-- SUCCESS MESSAGE -->
        <div v-if="successMessage">
            <Alert :type="success" :title="success" :message="successMessage" />
        </div>

        <!-- ERROR MESSAGE -->
        <div v-if="errorMessage && !showModal" class="p-3 rounded-lg bg-red-50 text-red-600 text-sm">
            <Alert :type="error" :title="error" :message="errorMessage" />
        </div>

        <!-- CUSTOMER TABLE -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">

            <!-- SEARCH -->
            <div class="p-4 border-b border-gray-100">
                <input v-model="search" type="text" placeholder="Search customer name or email..."
                    class="w-full sm:w-80 border border-gray-200 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500" />
            </div>

            <!-- TABLE -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-4">No.</th>
                            <th class="px-5 py-4">Customer Name</th>
                            <th class="px-5 py-4">Email</th>
                            <th class="px-5 py-4">Phone</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        <tr v-if="loading">
                            <td colspan="5" class="text-center py-10 text-gray-400">
                                Loading customers...
                            </td>
                        </tr>

                        <tr v-else-if="customers.length === 0">
                            <td colspan="5" class="text-center py-10 text-gray-400">
                                No customers found.
                            </td>
                        </tr>

                        <tr v-for="(customer, index) in customers" :key="customer.id" class="hover:bg-gray-50">
                            <td class="px-5 py-4 text-gray-500">
                                {{
                                    (pagination.page - 1) *
                                    pagination.per_page + index + 1
                                }}
                            </td>

                            <td class="px-5 py-4 font-medium text-gray-800">
                                {{ customer.name }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                {{ customer.email }}
                            </td>

                            <td class="px-5 py-4 text-gray-600">
                                {{ customer.phone_number || "-" }}
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 border-t border-gray-100">
                <p class="text-sm text-gray-500">
                    Total {{ pagination.total }} customers
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

        <!-- CREATE CUSTOMER MODAL -->
        <div v-if="showModal" class="fixed inset-0 z-[100] bg-black/40 flex items-center justify-center p-4"
            @click.self="closeModal">
            <div class="bg-white w-full max-w-lg rounded-xl shadow-xl max-h-[90vh] overflow-y-auto">

                <!-- MODAL HEADER -->
                <div class="flex justify-between items-center p-5 border-b">
                    <h2 class="text-lg font-bold text-gray-800">
                        Add Customer
                    </h2>

                    <button @click="closeModal" class="text-gray-400 hover:text-gray-700 text-xl">
                        ×
                    </button>
                </div>

                <!-- FORM -->
                <form @submit.prevent="saveCustomer" class="p-5 space-y-4">

                    <!-- NAME -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Customer Name
                        </label>

                        <input v-model="form.name" type="text" placeholder="Enter customer name"
                            class="w-full border rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100"
                            :class="errors.name ? 'border-red-500' : 'border-gray-200'" />

                        <p v-if="errors.name" class="text-red-500 text-xs mt-1">
                            {{ errors.name[0] }}
                        </p>
                    </div>

                    <!-- EMAIL -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Email
                        </label>

                        <input v-model="form.email" type="email" placeholder="Enter customer email"
                            class="w-full border rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100"
                            :class="errors.email ? 'border-red-500' : 'border-gray-200'" />

                        <p v-if="errors.email" class="text-red-500 text-xs mt-1">
                            {{ errors.email[0] }}
                        </p>
                    </div>

                    <!-- PHONE -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Phone
                        </label>

                        <input v-model="form.phone_number" type="text" placeholder="Enter phone number"
                            class="w-full border rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100"
                            :class="errors.phone ? 'border-red-500' : 'border-gray-200'" />

                        <p v-if="errors.phone" class="text-red-500 text-xs mt-1">
                            {{ errors.phone[0] }}
                        </p>
                    </div>

                    <!-- ERROR MESSAGE -->
                    <div v-if="errorMessage" class="p-3 rounded-lg bg-red-50 text-red-600 text-sm">
                        <Alert :type="error" :title="error" :message="errorMessage" />
                    </div>

                    <!-- BUTTONS -->
                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" @click="closeModal"
                            class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600">
                            Cancel
                        </button>

                        <button type="submit" :disabled="saving"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">
                            {{ saving ? "Saving..." : "Save Customer" }}
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</template>