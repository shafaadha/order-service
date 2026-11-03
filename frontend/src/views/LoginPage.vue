<script setup>
import { ref } from "vue";
import { useRouter, useRoute } from "vue-router";
import { login } from "../services/auth.js";
import Alert from "../components/common/Alert.vue";
import Button from "../components/common/Button.vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faEye, faEyeSlash } from "@fortawesome/free-solid-svg-icons";

const router = useRouter();
const route = useRoute();

const email = ref("");
const password = ref("");
const loading = ref(false);
const errorMessage = ref("");
const showPassword = ref(false);

const handleLogin = async () => {
    loading.value = true;
    errorMessage.value = "";

    try {
        await login({
            email: email.value,
            password: password.value,
        });

        const redirect = route.query.redirect;

        router.push("/");
    } catch (err) {
        console.error(err);

        errorMessage.value =
            err.response?.data?.message ||
            "Email atau password salah.";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen w-full bg-white flex items-center justify-center">
        <!-- Container Form -->
        <div class="w-full max-w-md bg-white shadow-lg rounded-2xl border border-gray-300 p-8">
            <h1 class="text-2xl font-bold text-center mb-6 text-gray-700">
                Login
            </h1>

            <div>
                <Alert v-if="errorMessage" type="error" title="Login Error" :message="errorMessage" />
            </div>

            <form @submit.prevent="handleLogin" class="space-y-4">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm/6 font-medium text-gray-500">Email address</label>
                    <div class="mt-2">
                        <input type="email" v-model="email" name="email" id="email" autocomplete="email" required=""
                            class="block w-full rounded-md px-3 py-2 text-neutral-700 outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6 border border-gray-500" />
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm/6 font-medium text-gray-500">Password</label>
                    <div class="relative mt-2">
                        <input :type="showPassword ? 'text' : 'password'" v-model="password" id="password"
                            autocomplete="current-password" required
                            class="block w-full rounded-md px-3 py-2 text-neutral-700 outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6 border border-gray-500" />

                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-gray-700">
                            <FontAwesomeIcon :icon="faEye" v-if="!showPassword" class="w-5 h-5" />
                            <FontAwesomeIcon :icon="faEyeSlash" v-else class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                <!-- Tombol -->
                <div class="flex justify-center mt-6">
                    <!-- <button
                            type="submit"
                            class="bg-sky-500 rounded-md w-full text-white py-1.5 disabled:opacity-50"
                        >
                            {{ loading ? "Loggin in..." : "Login" }}
                        </button> -->

                    <Button type="submit" :disabled="loading" :text="loading ? 'Logging in...' : 'Login'"
                        class="w-full" />
                </div>
            </form>
        </div>
    </div>
</template>
