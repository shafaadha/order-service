<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    options: {
        type: Array,
        default: () => [],
    },
});

defineEmits(["update:modelValue"]);
</script>

<template>
    <select :value="modelValue" @change="$emit('update:modelValue', $event.target.value)"
        class="h-10 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        <option v-for="option in options" :key="option.value" :value="option.value">
            {{ option.label }}
        </option>
    </select>
</template>
