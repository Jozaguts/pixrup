import { InertiaLinkProps } from '@inertiajs/vue3';
import { clsx, type ClassValue } from 'clsx';
import { twMerge } from 'tailwind-merge';
import { onMounted, onUnmounted, Ref } from 'vue';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(...inputs));
}

export function urlIsActive(
    urlToCheck: NonNullable<InertiaLinkProps['href']>,
    currentUrl: string,
) {
    return currentUrl.includes(toUrl(urlToCheck));
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>) {
    return typeof href === 'string' ? href : href?.url;
}
export function useHideNavbarOnScroll(navRef: Ref<HTMLElement | null>) {
    let trigger: ScrollTrigger | null = null;

    onMounted(()=>{
        const nav = navRef.value
        if (!nav) return
        const ACTIVATION_OFFSET = 200;
        gsap.set(nav, {y:0, zIndex:9999})
        trigger = ScrollTrigger.create({
            start: 0,
            end: 'max',
            onUpdate(self) {
                const scrollY = self.scroll()
                if (scrollY <= ACTIVATION_OFFSET){
                    gsap.to(nav,{
                        y: 0,
                        duration: 0.25,
                        ease: 'back.out',
                    })
                    return
                }
                if (self.direction === 1){
                    gsap.to(nav,{
                        // y: -NAV_HEIGHT,
                        opacity: 0,
                        duration: 0.40,
                        ease: 'back.out',
                    })

                }
                if (self.direction === -1) {
                    gsap.to(nav,{
                        y:0,
                        opacity:1,
                        duration: 0.40,
                        ease: 'back.out',
                    })
                }
            }
        })
    })
    onUnmounted(() => {
        trigger?.kill()
        trigger = null
    })
}
