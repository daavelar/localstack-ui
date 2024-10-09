import {createRouter, createWebHistory} from 'vue-router';
import TopicAndQueues from "./components/MainPage.vue";

const routes = [
    {
        path: '/',
        name: 'TopicAndQueues',
        component: TopicAndQueues
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
