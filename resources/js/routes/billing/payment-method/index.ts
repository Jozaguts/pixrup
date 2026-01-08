import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:46
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
* @see app/Interface/Properties/Http/Controllers/BillingController.php:46
* @route '/billing/payment-method'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:46
* @route '/billing/payment-method'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:46
* @route '/billing/payment-method'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::store
* @see app/Interface/Properties/Http/Controllers/BillingController.php:46
* @route '/billing/payment-method'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::defaultMethod
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method/default'
*/
export const defaultMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: defaultMethod.url(options),
    method: 'post',
})

defaultMethod.definition = {
    methods: ["post"],
    url: '/billing/payment-method/default',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::defaultMethod
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method/default'
*/
defaultMethod.url = (options?: RouteQueryOptions) => {
    return defaultMethod.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::defaultMethod
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method/default'
*/
defaultMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: defaultMethod.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::defaultMethod
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method/default'
*/
const defaultMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: defaultMethod.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::defaultMethod
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method/default'
*/
defaultMethodForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: defaultMethod.url(options),
    method: 'post',
})

defaultMethod.form = defaultMethodForm

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::destroy
* @see app/Interface/Properties/Http/Controllers/BillingController.php:88
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
* @see app/Interface/Properties/Http/Controllers/BillingController.php:88
* @route '/billing/payment-method'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::destroy
* @see app/Interface/Properties/Http/Controllers/BillingController.php:88
* @route '/billing/payment-method'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::destroy
* @see app/Interface/Properties/Http/Controllers/BillingController.php:88
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
* @see app/Interface/Properties/Http/Controllers/BillingController.php:88
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

const paymentMethod = {
    store: Object.assign(store, store),
    default: Object.assign(defaultMethod, defaultMethod),
    destroy: Object.assign(destroy, destroy),
}

export default paymentMethod