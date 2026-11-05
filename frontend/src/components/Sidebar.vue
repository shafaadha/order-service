<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { getUser, logout, isAuthenticated } from "../services/auth.js";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faArrowRightFromBracket, faEllipsis } from "@fortawesome/free-solid-svg-icons";

const router = useRouter();

const openMenu = ref(false);
const isLoggedIn = ref(isAuthenticated());

const props = defineProps({
    side: { type: String, default: "left" },
    menu: { type: Array, default: () => [] },
    user: { type: Object, default: () => ({}) },
});

const currentUser = computed(() => {
    return props.user?.name ? props.user : getUser();
});

const menuPosition = ref({
    top: 0,
    left: 0,
});

function toggleMenu(event) {
    if (openMenu.value) {
        openMenu.value = false;
        return;
    }

    const rect = event.currentTarget.getBoundingClientRect();

    menuPosition.value = {
        top: rect.top - 50,
        left: rect.left + 25,
    };

    openMenu.value = true;
}

async function logOut() {
    openMenu.value = false;
    try {
        logout();
    } catch (error) {
        console.error("Logout gagal:", error);
    } finally {
        isLoggedIn.value = false;
        await router.replace("/login");
    }
}


function initials() {
    const name = currentUser.value?.name || "";
    const words = name.trim().split(/\s+/);

    return (
        (words[0]?.charAt(0).toUpperCase() || "") +
        (words[1]?.charAt(0).toUpperCase() || "")
    );
}
</script>


<template>
    <aside class="fixed inset-y-0 z-50 flex w-64 flex-col
           justify-between border-r border-gray-100 bg-white
           transition-transform duration-300 ease-in-out" :class="[
            side === 'left' ? 'left-0' : 'right-0',
        ]">
        <!-- TOP -->
        <div class="flex flex-col justify-between">
            <!-- Logo -->
            <div class="flex items-center gap-2 px-6 py-4 font-bold text-lg text-gray-800">
                <span class="text-blue-600"></span>
                <div class="flex flex-col">
                    <h1>Dashboard Admin</h1>
                </div>
            </div>

            <!-- Menu -->
            <nav class="flex flex-col gap-3 px-2 py-2 text-gray-600">
                <router-link v-for="item in menu" :key="item.name" :to="item.to"
                    class="flex items-center justify-between px-4 py-2 rounded-lg hover:bg-gray-100"
                    active-class="bg-blue-50 text-blue-600">
                    <div class="flex items-center gap-3">
                        <component :is="item.icon" class="w-5 h-5" />
                        <span>{{ item.name }}</span>
                    </div>

                    <span v-if="item.badge" class="text-xs bg-gray-200 px-2 py-0.5 rounded-full">
                        {{ item.badge }}
                    </span>
                </router-link>
            </nav>
        </div>

        <div class="flex items-center gap-3 px-4 py-4 border-t border-gray-300 text-gray-700">
            <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                {{ initials() }}
            </div>

            <div class="flex-1 min-w-0 text-sm text-gray-600">
                <p class="font-semibold truncate">
                    {{ currentUser?.name || "Guest" }}
                </p>
                <p class="text-gray-500 text-xs truncate">
                    {{ currentUser?.email || "" }}
                </p>
            </div>

            <button type="button" @click="logOut"
                class="rounded-lg p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                <FontAwesomeIcon :icon="faArrowRightFromBracket" />
            </button>
        </div>

    </aside>


</template>
