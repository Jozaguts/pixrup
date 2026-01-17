import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

export type LandingTranslations = {
    nav?: {
        features?: string;
        use_cases?: string;
        pricing?: string;
        blog?: string;
        support?: string;
        cta?: {
            dashboard?: string;
            get_started?: string;
            sign_up?: string;
            log_in?: string;
        };
        sr?: {
            home?: string;
            toggle_navigation?: string;
            close_menu?: string;
        };
    };
    page?: {
        title?: string;
        intro?: string;
    };
    hero?: {
        title?: string;
        subtitle?: string;
    };
    address_search?: {
        placeholder?: string;
        loading?: string;
        errors?: {
            missing_api_key?: string;
            missing_place?: string;
            unexpected?: string;
        };
    };
    continue?: {
        alerts?: {
            select_address?: string;
            open_on_phone?: string;
        };
        alt?: {
            apple?: string;
            android?: string;
        };
    };
    features?: {
        badge?: string;
        title?: string;
        description?: string;
        note?: string;
        cta?: string;
    };
    use_cases?: {
        aria_label?: string;
        badge?: string;
        title?: string;
        description?: string;
        testimonial?: {
            name?: string;
            role?: string;
            initials?: string;
        };
        before_alt?: string;
        after_alt?: string;
    };
    pricing?: {
        badge?: string;
        title?: string;
        billing?: {
            monthly?: string;
            yearly?: string;
            toggle_aria?: string;
            per_month?: string;
            per_year?: string;
        };
        cta?: string;
        plans?: {
            simplified?: {
                name?: string;
                description?: string;
                features?: {
                    single_payment?: string;
                    sell_items?: string;
                    integrations?: string;
                };
            };
            basic?: {
                name?: string;
                description?: string;
                features?: {
                    bandwidth?: string;
                    promo_tools?: string;
                    single_payment?: string;
                    sell_items?: string;
                    integrations?: string;
                };
            };
            enhanced?: {
                name?: string;
                description?: string;
                features?: {
                    sell_conditions?: string;
                    seamless_integrations?: string;
                    real_time?: string;
                };
            };
        };
    };
    support?: {
        aria_label?: string;
        badge?: string;
        title?: string;
        description?: string;
        form?: {
            name?: {
                label?: string;
                placeholder?: string;
            };
            phone?: {
                label?: string;
                placeholder?: string;
            };
            email?: {
                label?: string;
                placeholder?: string;
            };
            subject?: {
                label?: string;
                placeholder?: string;
            };
            message?: {
                label?: string;
                placeholder?: string;
            };
            terms?: {
                text?: string;
                link?: string;
            };
            submit?: string;
        };
    };
    worth_preview?: {
        description?: string;
        estimated_label?: string;
        estimated_fallback?: string;
        estimated_note?: string;
        comparable_label?: string;
        comparable_empty?: string;
        comparable_unit_a?: string;
        comparable_unit_b?: string;
        cta?: string;
    };
    footer?: {
        rights?: string;
        terms?: string;
        and?: string;
        privacy?: string;
    };
};

export const useLandingTranslations = () => {
    const page = usePage();

    return computed<LandingTranslations>(() => {
        const translations = page.props.translations as
            | { landing?: LandingTranslations }
            | undefined;

        return translations?.landing ?? {};
    });
};
