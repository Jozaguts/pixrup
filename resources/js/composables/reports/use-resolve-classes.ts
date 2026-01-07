import { computed } from 'vue'
import { cn } from "@/lib/utils";
import type { ClassValue } from "clsx";

type Props = {
    wrapperCLass?: ClassValue,
    contentClass?: ClassValue,
    menuWrapperClass?: ClassValue,
    controlsPosition?: 'top' | 'bottom'
}

const useResolveClasses = (props: Props) => {

    const wrapperClasses = computed(() =>
        cn([
            'grid md:grid-cols-[250px_auto] sm:grid-cols-1',
            'sm:gap-0 h-full rounded-[12px] overflow-hidden',
            props.wrapperCLass
        ])
    );

    const contentClasses = computed(() =>
        cn([
            'col-span-1 h-full',
            props.controlsPosition === 'top' ? 'grid-rows-[auto_auto_1fr]' : 'grid-rows-[auto_1fr_auto]',
            'grid h-full gap-4',
            'overflow-y-auto overflow-hidden',
            props.contentClass
        ])
    );

    const menuWrapperClasses = computed(() =>
        cn([
            'hidden md:flex md:flex-col',
            'relative bg-surface md:gap-y-2 sm:gap-x-2',
            'w-full h-full col-span-1',
            'items-center justify-start md:py-4 sm:py-0',
            props.menuWrapperClass
        ])
    );

    return {
        wrapperClasses,
        contentClasses,
        menuWrapperClasses
    }
}

export default useResolveClasses;
