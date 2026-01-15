import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:158
* @route '/billing/subscription'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/billing/subscription',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:158
* @route '/billing/subscription'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:158
* @route '/billing/subscription'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:158
* @route '/billing/subscription'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:158
* @route '/billing/subscription'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::swap
* @see app/Interface/Properties/Http/Controllers/BillingController.php:173
* @route '/billing/subscription/swap'
*/
export const swap = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: swap.url(options),
    method: 'post',
})

swap.definition = {
    methods: ["post"],
    url: '/billing/subscription/swap',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::swap
* @see app/Interface/Properties/Http/Controllers/BillingController.php:173
* @route '/billing/subscription/swap'
*/
swap.url = (options?: RouteQueryOptions) => {
    return swap.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::swap
* @see app/Interface/Properties/Http/Controllers/BillingController.php:173
* @route '/billing/subscription/swap'
*/
swap.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: swap.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::swap
* @see app/Interface/Properties/Http/Controllers/BillingController.php:173
* @route '/billing/subscription/swap'
*/
const swapForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: swap.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::swap
* @see app/Interface/Properties/Http/Controllers/BillingController.php:173
* @route '/billing/subscription/swap'
*/
swapForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: swap.url(options),
    method: 'post',
})

swap.form = swapForm

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::cancel
* @see app/Interface/Properties/Http/Controllers/BillingController.php:188
* @route '/billing/subscription/cancel'
*/
export const cancel = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(options),
    method: 'post',
})

cancel.definition = {
    methods: ["post"],
    url: '/billing/subscription/cancel',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::cancel
* @see app/Interface/Properties/Http/Controllers/BillingController.php:188
* @route '/billing/subscription/cancel'
*/
cancel.url = (options?: RouteQueryOptions) => {
    return cancel.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::cancel
* @see app/Interface/Properties/Http/Controllers/BillingController.php:188
* @route '/billing/subscription/cancel'
*/
cancel.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::cancel
* @see app/Interface/Properties/Http/Controllers/BillingController.php:188
* @route '/billing/subscription/cancel'
*/
const cancelForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::cancel
* @see app/Interface/Properties/Http/Controllers/BillingController.php:188
* @route '/billing/subscription/cancel'
*/
cancelForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(options),
    method: 'post',
})

cancel.form = cancelForm

const subscription = {
    store: Object.assign(store, store),
    swap: Object.assign(swap, swap),
    cancel: Object.assign(cancel, cancel),
}

export default subscription