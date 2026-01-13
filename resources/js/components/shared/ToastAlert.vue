<script setup lang="ts">
import Swal from 'sweetalert2';
import { watch, withDefaults } from 'vue';

interface Props {
    visible: boolean;
    type: 'success' | 'error' | 'info' | 'warning';
    title?: string;
    msg?: string;
    position?:
        | 'top'
        | 'top-start'
        | 'top-end'
        | 'center'
        | 'center-start'
        | 'center-end'
        | 'bottom'
        | 'bottom-start'
        | 'bottom-end';
    showCloseButton?: boolean;
    showConfirmButton?: boolean;
    timer?: number;
    showProgressBar?: boolean;
    redirectUrl?: string;
}

const props = withDefaults(defineProps<Props>(), {
    type: 'success',
    title: 'Operation Successful',
    msg: '',
    position: 'top-end',
    showCloseButton: true,
    showConfirmButton: false,
    hasTimer: 5000,
    showProgressBar: true,
});

const showToast = () =>
    Swal.fire({
        icon: props.type,
        position: props.position,
        title: props.title,
        toast: true,
        showConfirmButton: props.showConfirmButton,
        showCloseButton: props.showCloseButton,
        timer: props.timer,
        timerProgressBar: props.showProgressBar,
        iconColor: 'black',
        customClass: {
            popup: '!bg-surface npo-form-shadow shadow-neu-in text-accent border-1',
            header: '!text-accent',
            title: '!text-accent',
            // closeButton: '!bg-surface',
            // icon: '!bg-surface',
            // image: '!bg-surface',
            htmlContainer: '!text-accent',
            // input: '!bg-surface',
            // inputLabel: '!bg-surface',
            validationMessage: '!text-accent',
            // actions: '!bg-surface',
            confirmButton: '!text-accent',
            denyButton: '!text-accent',
            cancelButton: '!text-accent',
            loader: '!bg-background',
            // footer: '!bg-surface.',
            timerProgressBar: '!bg-primary',
        },
        closeButtonColor: 'black',
        html: `
            <div class="text-sm ">
        <p>${props.msg}</p>
        ${
            props.redirectUrl
                ? `<a href="${props.redirectUrl}" target="_blank" class="underline font-semibold">
                Upgrade your plan
                <svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256" class="w-5 h-5 inline-block ml-1 bg-primary" >
                    <path fill="#000" d="M200 64v104a8 8 0 0 1-13.66 5.66L140 127.31l-70.34 70.35a8 8 0 0 1-11.32-11.32L128.69 116L82.34 69.66A8 8 0 0 1 88 56h104a8 8 0 0 1 8 8" stroke-width="6.5" stroke="#000" />
                </svg>
              </a>`
                : ''
        }
      </div>
        `,
    });

watch(
    () => props.visible,
    (val) => {
        if (val) showToast();
        else Swal.close();
    },
    { immediate: true },
);
</script>

<template>
    <div></div>
</template>
