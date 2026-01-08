import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:13
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
* @see app/Interface/Properties/Http/Controllers/BillingController.php:13
* @route '/billing/account'
*/
account.url = (options?: RouteQueryOptions) => {
    return account.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:13
* @route '/billing/account'
*/
account.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:13
* @route '/billing/account'
*/
account.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: account.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:13
* @route '/billing/account'
*/
const accountForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: account.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:13
* @route '/billing/account'
*/
accountForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: account.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:13
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
* @see \App\Interface\Properties\Http\Controllers\BillingController::updateDefault
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
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
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method/default'
*/
updateDefault.url = (options?: RouteQueryOptions) => {
    return updateDefault.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::updateDefault
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method/default'
*/
updateDefault.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateDefault.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::updateDefault
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method/default'
*/
const updateDefaultForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateDefault.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::updateDefault
* @see app/Interface/Properties/Http/Controllers/BillingController.php:67
* @route '/billing/payment-method/default'
*/
updateDefaultForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateDefault.url(options),
    method: 'post',
})

updateDefault.form = updateDefaultForm

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

const BillingController = { account, store, updateDefault, destroy }

export default BillingController