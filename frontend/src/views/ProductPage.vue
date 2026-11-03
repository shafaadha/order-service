<script setup>
import { ref, reactive, computed, onMounted, watch } from "vue";
import {
    getProducts,
    createProduct,
    updateProduct,
} from "../services/product";
import Alert from "../components/common/Alert.vue";

const products = ref([]);
const loading = ref(false);
const saving = ref(false);
const search = ref("");
const showModal = ref(false);
const isEditing = ref(false);
const errorMessage = ref("");

const pagination = ref({
    page: 1,
    per_page: 10,
    total: 0,
});

const form = reactive({
    id: null,
    name: "",
    description: "",
    price: "",
    stock: 0,
});

const errors = ref({});
const successMessage = ref("");

const modalTitle = computed(() =>
    isEditing.value ? "Edit Product" : "Add Product"
);

async function loadProducts(page = 1) {
    loading.value = true;

    try {
        const response = await getProducts({
            page,
            search: search.value,
        });

        const result = response.data;

        if (!result.success) {
            throw new Error(result.message);
        }

        products.value = result.data;

        pagination.value = result.meta;

    } catch (error) {
        console.error(
            "Failed to load products:",
            error.response?.data || error
        );
    } finally {
        loading.value = false;
    }
}

let searchTimeout;

watch(search, () => {
    clearTimeout(searchTimeout);

    searchTimeout = setTimeout(() => {
        loadProducts(1);
    }, 1000);
});

// RESET FORM
function resetForm() {
    Object.assign(form, {
        id: null,
        name: "",
        description: "",
        price: "",
        stock: 0,
    });

    errors.value = {};
    isEditing.value = false;
}

// OPEN CREATE MODAL
function openCreateModal() {
    resetForm();
    showModal.value = true;
}

// OPEN EDIT MODAL
function openEditModal(product) {
    resetForm();

    isEditing.value = true;

    Object.assign(form, {
        id: product.id,
        name: product.name,
        description: product.description || "",
        price: product.price,
        stock: product.stock,
    });

    showModal.value = true;
}

// CLOSE MODAL
function closeModal() {
    showModal.value = false;
    resetForm();
}

// CREATE / UPDATE
const saveProduct = async () => {
    errorMessage.value = "";
    successMessage.value = "";
    saving.value = true;

    try {
        const payload = {
            name: form.name,
            description: form.description,
            price: Number(form.price),
            stock: Number(form.stock),
        };

        const response = isEditing.value
            ? await updateProduct(form.id, payload)
            : await createProduct(payload);

        const result = response.data;

        if (!result.success) {
            throw new Error(result.message);
        }

        successMessage.value = result.message;

        await loadProducts(pagination.value.page);
        closeModal();

    } catch (error) {
        console.error(error.response?.data || error);

        errorMessage.value =
            error.response?.data?.message ||
            error.message ||
            "Something went wrong.";

        errors.value = error.response?.data?.errors || {};
    } finally {
        saving.value = false;
    }
};

function changePage(page) {
    if (
        page < 1 ||
        page > pagination.value.last_page ||
        loading.value
    ) {
        return;
    }

    loadProducts(page);
}

const lastPage = computed(() =>
    Math.ceil(pagination.value.total / pagination.value.per_page)
);

function formatPrice(price) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0,
    }).format(price);
}

onMounted(() => {
    loadProducts();
});
</script>

<template>
    <div class="p-6 md:p-8 space-y-6">

        <!-- HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Products
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Manage your store products.
                </p>
            </div>

            <button @click="openCreateModal"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium">
                + Add Product
            </button>
        </div>

        <!-- SUCCESS MESSAGE -->
        <div v-if="successMessage">
            <Alert :type="success" :title="success" :message="successMessage" />
        </div>

        <!-- PRODUCT TABLE -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">

            <!-- SEARCH -->
            <div class="p-4 border-b border-gray-100">
                <input v-model="search" type="text" placeholder="Search product name"
                    class="w-full sm:w-80 border border-gray-200 rounded-lg px-4 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-500" />
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-600">
                        <tr>
                            <th class="px-5 py-4">No.</th>
                            <th class="px-5 py-4">Product Name</th>
                            <th class="px-5 py-4">Price</th>
                            <th class="px-5 py-4">Stock</th>
                            <th class="px-5 py-4 text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        <tr v-if="loading">
                            <td colspan="5" class="text-center py-10 text-gray-400">
                                Loading products...
                            </td>
                        </tr>

                        <tr v-else-if="products.length === 0">
                            <td colspan="5" class="text-center py-10 text-gray-400">
                                No products found.
                            </td>
                        </tr>

                        <tr v-for="(product, index) in products" :key="product.id" class="hover:bg-gray-50">
                            <td class="px-5 py-4 text-gray-500">
                                {{
                                    (pagination.page - 1) * pagination.per_page + index + 1
                                }}
                            </td>

                            <td class="px-5 py-4 text-gray-800">
                                <div class="font-medium">
                                    {{ product.name }}
                                </div>
                                <div class="text-xs text-gray-400 mt-1">
                                    {{ product.description || "-" }}
                                </div>
                            </td>

                            <td class="px-5 py-4 text-gray-700">
                                {{ formatPrice(product.price) }}
                            </td>

                            <td class="px-5 py-4">
                                <span :class="product.stock > 0
                                    ? 'bg-green-50 text-green-700'
                                    : 'bg-red-50 text-red-600'" class="px-2.5 py-1 rounded-full text-xs font-medium">
                                    {{ product.stock }}
                                </span>
                            </td>

                            <td class="px-5 py-4 text-center">
                                <button @click="openEditModal(product)"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                    Edit
                                </button>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 border-t border-gray-100">
                <p class="text-sm text-gray-500">
                    Total {{ pagination.total }} products
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

        <!-- MODAL -->
        <div v-if="showModal" class="fixed inset-0 z-[100] bg-black/40 flex items-center justify-center p-4"
            @click.self="closeModal">
            <div class="bg-white w-full max-w-lg rounded-xl shadow-xl max-h-[90vh] overflow-y-auto">

                <div class="flex justify-between items-center p-5 border-b">
                    <h2 class="text-lg font-bold text-gray-800">
                        {{ modalTitle }}
                    </h2>

                    <button @click="closeModal" class="text-gray-400 hover:text-gray-700 text-xl">
                        ×
                    </button>
                </div>

                <form @submit.prevent="saveProduct" class="p-5 space-y-4">
                    <!-- NAME -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Product Name
                        </label>
                        <input v-model="form.name" type="text" placeholder="Enter product name"
                            class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-blue-100 outline-none"
                            :class="errors.name ? 'border-red-500' : 'border-gray-200'" />
                        <p v-if="errors.name" class="text-red-500 text-xs mt-1">
                            {{ errors.name[0] }}
                        </p>
                    </div>

                    <!-- DESCRIPTION -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea v-model="form.description" rows="3" placeholder="Enter product description"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100"></textarea>
                    </div>

                    <!-- PRICE -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Price (IDR)
                        </label>
                        <input v-model="form.price" type="number" min="0.01" step="0.01" placeholder="Enter price"
                            class="w-full border rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100"
                            :class="errors.price ? 'border-red-500' : 'border-gray-200'" />
                        <p v-if="errors.price" class="text-red-500 text-xs mt-1">
                            {{ errors.price[0] }}
                        </p>
                    </div>

                    <!-- STOCK -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Stock
                        </label>
                        <input v-model="form.stock" type="number" min="0" step="1" placeholder="Enter stock"
                            class="w-full border rounded-lg px-3 py-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-100"
                            :class="errors.stock ? 'border-red-500' : 'border-gray-200'" />
                        <p v-if="errors.stock" class="text-red-500 text-xs mt-1">
                            {{ errors.stock[0] }}
                        </p>
                    </div>

                    <!-- BUTTONS -->
                    <div class="flex justify-end gap-3 pt-3">
                        <button type="button" @click="closeModal"
                            class="px-4 py-2.5 border border-gray-200 rounded-lg text-sm text-gray-600">
                            Cancel
                        </button>

                        <button type="submit" :disabled="saving"
                            class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium disabled:opacity-50">
                            {{ saving ? "Saving..." : "Save Product" }}
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</template>
