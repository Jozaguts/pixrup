import {cn} from "@/lib/utils";
import {ClassValue} from "clsx";

type Props = {
    wrapperCLass?: ClassValue,
    contentClass?: ClassValue,
    menuWrapperClass?: ClassValue,
}

const useResolveClasses = (props:Props) => {
    const wrapperClasses = cn([
        'grid md:grid-cols-[250px_auto] sm:grid-cols-1',
        'sm:gap-0 h-full rounded-[12px] overflow-hidden',
        props.wrapperCLass
    ]);

    const contentClasses = cn([
        'col-span-1 h-full',
        'overflow-hidden flex flex-col gap-4',
        'overflow-y-auto',
        props.contentClass
    ]);

    const menuWrapperClasses = cn([
        'hidden md:flex md:flex-col',
        'relative bg-gray-200 md:gap-y-2 sm:gap-x-2',
        'w-full h-full col-span-1 ',
        'items-center justify-start md:py-4 sm:py-0',
        props.menuWrapperClass
    ]);

    return {
        wrapperClasses,
        contentClasses,
        menuWrapperClasses
    }
}

export default useResolveClasses;
