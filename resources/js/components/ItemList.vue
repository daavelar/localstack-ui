<template>
    <div>
        <div v-if="loading" class="flex justify-center items-center">
            <div class="loader"></div>
        </div>
        <ul v-else class="space-y-2">
            <li v-for="item in items" :key="item"
                class="flex justify-between items-center bg-gray-800 p-2 rounded-lg shadow-md">
                <span>{{ item.arn }}</span>
                <div>
                    <button v-if="itemType === 'queue'" @click="$emit('selectItem', item)"
                            class="text-blue-400 hover:text-blue-300 mr-2 transition-colors duration-200 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12h6m-6 4h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/>
                        </svg>
                    </button>
                    <button v-if="itemType !== 'subscription'" @click="$emit('removeItem', item)"
                            class="text-red-400 hover:text-red-300 transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
defineProps(['items', 'itemType', 'loading']);
defineEmits(['removeItem', 'selectItem']);
</script>

<style scoped>
.loader {
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top: 4px solid #fff;
    width: 24px;
    height: 24px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
