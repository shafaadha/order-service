```vue
<script setup>
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faTrash, faXmark } from "@fortawesome/free-solid-svg-icons";

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "Konfirmasi Hapus",
    },
    message: {
        type: String,
        default: "Apakah kamu yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.",
    },
    loading: {
        type: Boolean,
        default: false,
    },
    buttonText: {
        type: String,
        default: "Yes"
    }
});

const emit = defineEmits(["close", "confirm"]);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="!loading && emit('close')">
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <!-- Header -->
            <div class="mb-1 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">
                    {{ title }}
                </h2>

                <button @click="emit('close')" :disabled="loading"
                    class="text-gray-400 hover:text-gray-600 disabled:opacity-50">
                    <FontAwesomeIcon :icon="faXmark" />
                </button>
            </div>

            <!-- Content -->
            <div class="mb-4 flex items-start gap-3">
                <p class="text-sm leading-relaxed text-gray-500">
                    {{ message }}
                </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
                <button @click="emit('close')" :disabled="loading"
                    class="rounded-lg border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 disabled:opacity-50">
                    Cancel
                </button>

                <button @click="emit('confirm')" :disabled="loading"
                    class="flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50">
                    {{ loading ? "Process" : buttonText }}
                </button>
            </div>
        </div>
    </div>
</template>
```