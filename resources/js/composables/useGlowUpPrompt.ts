import { buildPrompt, type RoomType, type Style } from '@/lib/glowupPrompt';
import { gsap } from '@/lib/gsap';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

interface GlowUpPromptForm {
    room_type: string | null;
    style: string | null;
    image: File | null;
    prompt: string;
}

export const useGlowUpPrompt = (form: GlowUpPromptForm) => {
    const promptDraft = ref('');
    const promptDirty = ref(false);
    const autoPromptActive = ref(false);
    let typingTween: gsap.core.Tween | null = null;
    const skeletonActive = ref(false);
    let skeletonTimeout: number | null = null;

    const canShowPrompt = computed(() => Boolean(form.image && form.room_type && form.style));

    const stopTyping = () => {
        if (typingTween) {
            typingTween.kill();
            typingTween = null;
        }
    };

    const stopSkeleton = () => {
        if (skeletonTimeout !== null) {
            window.clearTimeout(skeletonTimeout);
            skeletonTimeout = null;
        }
        skeletonActive.value = false;
    };

    const triggerSkeleton = (duration: number) => {
        stopSkeleton();
        skeletonActive.value = true;
        skeletonTimeout = window.setTimeout(() => {
            skeletonActive.value = false;
            skeletonTimeout = null;
        }, duration * 1000);
    };

    const applyPromptWithTyping = (text: string) => {
        const trimmed = text.trim();
        stopTyping();
        stopSkeleton();

        if (!trimmed) {
            autoPromptActive.value = false;
            promptDraft.value = '';
            form.prompt = '';
            promptDirty.value = false;
            return;
        }

        autoPromptActive.value = true;
        const state = { progress: 0 };
        const target = trimmed.length;
        const duration = Math.min(7, Math.max(0.8, target / 30));
        triggerSkeleton(duration);

        typingTween = gsap.to(state, {
            progress: target,
            duration,
            ease: 'none',
            onUpdate: () => {
                promptDraft.value = trimmed.slice(0, Math.round(state.progress));
            },
            onComplete: () => {
                promptDraft.value = trimmed;
                autoPromptActive.value = false;
                typingTween = null;
            },
        });
    };

    watch(
        [() => form.room_type, () => form.style, () => form.image],
        ([room, style, image], [prevRoom, prevStyle, prevImage]) => {
            if (!room || !style || !image) {
                stopTyping();
                stopSkeleton();
                autoPromptActive.value = false;
                promptDirty.value = false;
                promptDraft.value = '';
                form.prompt = '';
                return;
            }

            if (room !== prevRoom || style !== prevStyle || image !== prevImage) {
                promptDirty.value = false;
            }

            if (!promptDirty.value) {
                const { positive } = buildPrompt({
                    room: room as RoomType,
                    style: style as Style,
                    includeNegatives: false,
                });
                promptDirty.value = false;
                applyPromptWithTyping(positive);
            }
        },
        { immediate: true },
    );

    watch(
        () => promptDraft.value,
        (value) => {
            form.prompt = value;
            if (!autoPromptActive.value) {
                promptDirty.value = true;
            }
        },
    );

    const regeneratePrompt = () => {
        if (!canShowPrompt.value || !form.room_type || !form.style) {
            return;
        }

        promptDirty.value = false;
        const { positive } = buildPrompt({
            room: form.room_type as RoomType,
            style: form.style as Style,
            includeNegatives: false,
        });
        applyPromptWithTyping(positive);
    };

    const handlePromptInput = (value: string) => {
        stopTyping();
        stopSkeleton();
        autoPromptActive.value = false;
        promptDraft.value = value;
    };

    const promptHelper = computed(() =>
        canShowPrompt.value
            ? 'Modify the prompt if you want to guide the AI differently.'
            : 'Add an image, room type, and style to preview the prompt.',
    );

    onBeforeUnmount(() => {
        stopTyping();
        stopSkeleton();
    });

    return {
        promptDraft,
        promptHelper,
        skeletonActive,
        canShowPrompt,
        regeneratePrompt,
        handlePromptInput,
    };
};
