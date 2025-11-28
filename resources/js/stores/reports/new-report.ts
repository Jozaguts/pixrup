import { defineStore } from 'pinia';
import { ref, computed } from 'vue';

const useNewReportStore = defineStore('new-report', () => {
    const image = ref<File | null>(null);

    const setImage = (file: File | null) => {
        if(file === null) {
            image.value = null;
            return;
        }
        image.value = file;
    }
    const imageValidated = computed(() => image.value !== null);

    return {
        image,
        imageValidated,
        setImage,
    };
});

export default useNewReportStore;
