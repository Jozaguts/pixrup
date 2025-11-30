import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useGlobalStore = defineStore('globalStore', () =>{
    const estimatePrice = ref<number>(0.0)


    return {
        estimatePrice
    }
})