<script setup>
const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    position: {
        type: Object,
        default: () => ({
            top: 0,
            left: 0,
        }),
    },
});

const handleAction = (item) => {
    item.action();
};
</script>

<template>
    <Teleport to="body">
        <div class="fixed z-200 w-36 rounded-lg border border-gray-200 bg-white py-1 text-left shadow-lg" :style="{
            top: `${props.position.top}px`,
            left: `${props.position.left}px`,
        }">
            <button v-for="item in props.items" :key="item.label" type="button"
                class="block w-full px-4 py-2 text-left text-sm hover:bg-gray-100" :class="item.danger
                    ? 'text-red-600 hover:bg-red-50'
                    : 'text-gray-700'
                    " @click="handleAction(item)">
                {{ item.label }}
            </button>
        </div>
    </Teleport>
</template>
