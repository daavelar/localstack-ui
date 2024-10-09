<template>
    <div class="mt-6">
        <h3 class="text-lg font-semibold mb-3 text-purple-400">Messages in {{ selectedQueue.name }}</h3>
        <ul class="space-y-2 max-h-80 overflow-y-auto">
            <li v-for="(message, index) in messages" :key="index" class="bg-gray-700 p-2 rounded-lg shadow-md">
                <div class="flex justify-between items-center mb-1">
                    <span class="font-semibold text-yellow-300 text-xs">
                        {{ message.message_id }}
                        <small class="font-xs text-gray-200">({{ message.created_at }})</small>
                    </span>
                    <button @click="$emit('removeMessage', index)"
                            class="text-red-400 hover:text-red-300 transition-colors duration-200 text-xs">
                        Remove
                    </button>
                </div>
                <pre class="text-xs text-gray-300 whitespace-pre-wrap">{{ message.body }}</pre>
            </li>
        </ul>
    </div>
</template>

<script setup>
defineProps(['selectedQueue', 'messages']);
defineEmits(['addMessage', 'removeMessage']);
</script>
