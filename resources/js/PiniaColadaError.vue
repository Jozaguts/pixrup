<script setup lang="ts">
import { watch, toRef } from 'vue'
import Swal from 'sweetalert2'
import {AxiosError} from "axios";

const props = defineProps<{
    error: AxiosError
}>()
const error = toRef(props, 'error');

function isLaravelValidationError(data: any) {
    return data.response?.data?.message !== undefined;
}


watch(error, (value) => {
    if (!value) return;

    let errorMessage = '';

  if(isLaravelValidationError(value)) {
      const data  = (value as AxiosError).response?.data;
      errorMessage = (data as { message:string}).message;
  } else {
      errorMessage = value?.message ?? 'An unknown error occurred. Please try again later.';
  }


    Swal.fire({
        icon: 'error',
        title: 'An error occurred',
        text: errorMessage,
        toast: true,
        position: 'top-end',
        showConfirmButton:false,
        showCloseButton:true,
        timer: 5000,
    });
});
</script>

