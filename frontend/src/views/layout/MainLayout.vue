<template>
    <div class="min-h-screen bg-gray-100">
        <Sidebar v-if="!route.meta.hideSidebar" :menu="menu" :user="user" />

        <main class="min-h-screen md:ml-64">
            <div class="max-w-7xl mx-auto p-5 md:p-6">
                <router-view />
            </div>
        </main>
    </div>
</template>