import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:19
* @route '/billing/account'
*/
export const account = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

account.definition = {
    methods: ["get","head"],
    url: '/billing/account',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:19
* @route '/billing/account'
*/
account.url = (options?: RouteQueryOptions) => {
    return account.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:19
* @route '/billing/account'
*/
account.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:19
* @route '/billing/account'
*/
account.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: account.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:19
* @route '/billing/account'
*/
const accountForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: account.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:19
* @route '/billing/account'
*/
accountForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: account.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:19
* @route '/billing/account'
*/
accountForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: account.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

account.form = accountForm

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/billing/payment-method',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::updateDefault
* @see app/Interface/Properties/Http/Controllers/BillingController.php:88
* @route '/billing/payment-method/default'
*/
export const updateDefault = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateDefault.url(options),
    method: 'post',
})

updateDefault.definition = {
    methods: ["post"],
    url: '/billing/payment-method/default',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::updateDefault
* @see app/Interface/Properties/Http/Controllers/BillingController.php:88
* @route '/billing/payment-method/default'
*/
updateDefault.url = (options?: RouteQueryOptions) => {
    return updateDefault.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::updateDefault
* @see app/Interface/Properties/Http/Controllers/BillingController.php:88
* @route '/billing/payment-method/default'
*/
updateDefault.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateDefault.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::updateDefault
* @see app/Interface/Properties/Http/Controllers/BillingController.php:88
* @route '/billing/payment-method/default'
*/
const updateDefaultForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateDefault.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::updateDefault
* @see app/Interface/Properties/Http/Controllers/BillingController.php:88
* @route '/billing/payment-method/default'
*/
updateDefaultForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateDefault.url(options),
    method: 'post',
})

updateDefault.form = updateDefaultForm

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::destroy
* @see app/Interface/Properties/Http/Controllers/BillingController.php:109
* @route '/billing/payment-method'
*/
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/billing/payment-method',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::destroy
* @see app/Interface/Properties/Http/Controllers/BillingController.php:109
* @route '/billing/payment-method'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::destroy
* @see app/Interface/Properties/Http/Controllers/BillingController.php:109
* @route '/billing/payment-method'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::destroy
* @see app/Interface/Properties/Http/Controllers/BillingController.php:109
* @route '/billing/payment-method'
*/
const destroyForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::destroy
* @see app/Interface/Properties/Http/Controllers/BillingController.php:109
* @route '/billing/payment-method'
*/
destroyForm.delete = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::subscribe
* @see app/Interface/Properties/Http/Controllers/BillingController.php:139
* @route '/billing/subscription'
*/
export const subscribe = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: subscribe.url(options),
    method: 'post',
})

subscribe.definition = {
    methods: ["post"],
    url: '/billing/subscription',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::subscribe
* @see app/Interface/Properties/Http/Controllers/BillingController.php:139
* @route '/billing/subscription'
*/
subscribe.url = (options?: RouteQueryOptions) => {
    return subscribe.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::subscribe
* @see app/Interface/Properties/Http/Controllers/BillingController.php:139
* @route '/billing/subscription'
*/
subscribe.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: subscribe.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::subscribe
* @see app/Interface/Properties/Http/Controllers/BillingController.php:139
* @route '/billing/subscription'
*/
const subscribeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: subscribe.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::subscribe
* @see app/Interface/Properties/Http/Controllers/BillingController.php:139
* @route '/billing/subscription'
*/
subscribeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: subscribe.url(options),
    method: 'post',
})

subscribe.form = subscribeForm

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::swap
* @see app/Interface/Properties/Http/Controllers/BillingController.php:154
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
* @see app/Interface/Properties/Http/Controllers/BillingController.php:154
* @route '/billing/subscription/swap'
*/
swap.url = (options?: RouteQueryOptions) => {
    return swap.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::swap
* @see app/Interface/Properties/Http/Controllers/BillingController.php:154
* @route '/billing/subscription/swap'
*/
swap.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: swap.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::swap
* @see app/Interface/Properties/Http/Controllers/BillingController.php:154
* @route '/billing/subscription/swap'
*/
const swapForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: swap.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::swap
* @see app/Interface/Properties/Http/Controllers/BillingController.php:154
* @route '/billing/subscription/swap'
*/
swapForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: swap.url(options),
    method: 'post',
})

swap.form = swapForm

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::cancel
* @see app/Interface/Properties/Http/Controllers/BillingController.php:169
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
* @see app/Interface/Properties/Http/Controllers/BillingController.php:169
* @route '/billing/subscription/cancel'
*/
cancel.url = (options?: RouteQueryOptions) => {
    return cancel.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::cancel
* @see app/Interface/Properties/Http/Controllers/BillingController.php:169
* @route '/billing/subscription/cancel'
*/
cancel.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::cancel
* @see app/Interface/Properties/Http/Controllers/BillingController.php:169
* @route '/billing/subscription/cancel'
*/
const cancelForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::cancel
* @see app/Interface/Properties/Http/Controllers/BillingController.php:169
* @route '/billing/subscription/cancel'
*/
cancelForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(options),
    method: 'post',
})

cancel.form = cancelForm

const BillingController = { account, store, updateDefault, destroy, subscribe, swap, cancel }

export default BillingController