<template>
    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-4 text-green-400">Manage Subscriptions</h2>
        <ul class="space-y-2">
            <li v-for="subscription in subscriptions" :key="subscription.arn_resource"
                class="flex items-center justify-between bg-gray-800 p-2 rounded-lg shadow-md">
                <div class="flex items-center flex-grow overflow-hidden">
                    <div class="flex items-center min-w-0">
                        <div class="flex items-center mr-2 min-w-0">
                            <span class="text-xs text-gray-500 mr-1 flex-shrink-0">queue:</span>
                            <span class="text-yellow-300 truncate">{{ subscription.queue }}</span>
                        </div>
                        <span class="text-gray-400 mx-2 flex-shrink-0">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                      d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z"
                      clip-rule="evenodd"/>
              </svg>
            </span>
                        <div class="flex items-center min-w-0">
                            <span class="text-xs text-gray-500 mr-1 flex-shrink-0">topic:</span>
                            <span class="text-blue-300 truncate">{{ subscription.topic }}</span>
                        </div>
                    </div>
                </div>
                <button @click="deleteSubscription(subscription)"
                        class="text-red-400 hover:text-red-300 transition-colors duration-200 ml-2 flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                              clip-rule="evenodd"/>
                    </svg>
                </button>
            </li>
        </ul>
        <div class="bg-gray-800 p-4 rounded-lg shadow-md mt-4">
            <h3 class="text-lg font-semibold mb-3 text-purple-400">Create New Subscription</h3>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mb-3">
                <select
                    v-model="newSubscription.queue"
                    class="flex-1 bg-gray-700 text-white rounded-md px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-full"
                >
                    <option value="">Select Queue</option>
                    <option v-for="queue in queues" :key="queue.arn" :value="queue">{{ queue.name }}</option>
                </select>
                <select
                    v-model="newSubscription.topic"
                    class="flex-1 bg-gray-700 text-white rounded-md px-2 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm w-full"
                >
                    <option value="">Select Topic</option>
                    <option v-for="topic in topics" :key="topic.arn" :value="topic">{{ topic.name }}</option>
                </select>
            </div>
            <button @click="addSubscription"
                    class="w-full px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-500 transition-colors duration-200 text-sm">
                Create Subscription
            </button>
        </div>
    </div>
</template>

<script setup>
import {ref} from 'vue';

const props = defineProps(['subscriptions', 'queues', 'topics']);
const emit = defineEmits(['addSubscription', 'deleteSubscription']);

const newSubscription = ref({queue: '', topic: ''});

const addSubscription = () => {
    if (newSubscription.value.queue && newSubscription.value.topic) {
        emit('addSubscription', {
            queue: newSubscription.value.queue,
            topic: newSubscription.value.topic
        });
        newSubscription.value = {queue: '', topic: ''};
    }
};

const deleteSubscription = (subscriptionArn) => {
    emit('deleteSubscription', subscriptionArn);
};
</script>
