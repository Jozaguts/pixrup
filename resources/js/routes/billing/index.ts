import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import paymentMethod from './payment-method'
import subscription from './subscription'
/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:24
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
* @see app/Interface/Properties/Http/Controllers/BillingController.php:24
* @route '/billing/account'
*/
account.url = (options?: RouteQueryOptions) => {
    return account.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:24
* @route '/billing/account'
*/
account.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:24
* @route '/billing/account'
*/
account.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: account.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:24
* @route '/billing/account'
*/
const accountForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: account.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:24
* @route '/billing/account'
*/
accountForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: account.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::account
* @see app/Interface/Properties/Http/Controllers/BillingController.php:24
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
* @see \App\Interface\Properties\Http\Controllers\BillingController::orderHistory
* @see app/Interface/Properties/Http/Controllers/BillingController.php:70
* @route '/billing/order-history'
*/
export const orderHistory = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orderHistory.url(options),
    method: 'get',
})

orderHistory.definition = {
    methods: ["get","head"],
    url: '/billing/order-history',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::orderHistory
* @see app/Interface/Properties/Http/Controllers/BillingController.php:70
* @route '/billing/order-history'
*/
orderHistory.url = (options?: RouteQueryOptions) => {
    return orderHistory.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::orderHistory
* @see app/Interface/Properties/Http/Controllers/BillingController.php:70
* @route '/billing/order-history'
*/
orderHistory.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orderHistory.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::orderHistory
* @see app/Interface/Properties/Http/Controllers/BillingController.php:70
* @route '/billing/order-history'
*/
orderHistory.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: orderHistory.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::orderHistory
* @see app/Interface/Properties/Http/Controllers/BillingController.php:70
* @route '/billing/order-history'
*/
const orderHistoryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orderHistory.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::orderHistory
* @see app/Interface/Properties/Http/Controllers/BillingController.php:70
* @route '/billing/order-history'
*/
orderHistoryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orderHistory.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\BillingController::orderHistory
* @see app/Interface/Properties/Http/Controllers/BillingController.php:70
* @route '/billing/order-history'
*/
orderHistoryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orderHistory.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

orderHistory.form = orderHistoryForm

const billing = {
    account: Object.assign(account, account),
    orderHistory: Object.assign(orderHistory, orderHistory),
    paymentMethod: Object.assign(paymentMethod, paymentMethod),
    subscription: Object.assign(subscription, subscription),
}

export default billing