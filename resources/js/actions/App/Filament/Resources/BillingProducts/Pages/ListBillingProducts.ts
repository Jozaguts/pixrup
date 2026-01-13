import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\BillingProducts\Pages\ListBillingProducts::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/ListBillingProducts.php:7
* @route '/admin/billing-products'
*/
const ListBillingProducts = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListBillingProducts.url(options),
    method: 'get',
})

ListBillingProducts.definition = {
    methods: ["get","head"],
    url: '/admin/billing-products',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\BillingProducts\Pages\ListBillingProducts::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/ListBillingProducts.php:7
* @route '/admin/billing-products'
*/
ListBillingProducts.url = (options?: RouteQueryOptions) => {
    return ListBillingProducts.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\BillingProducts\Pages\ListBillingProducts::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/ListBillingProducts.php:7
* @route '/admin/billing-products'
*/
ListBillingProducts.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListBillingProducts.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\BillingProducts\Pages\ListBillingProducts::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/ListBillingProducts.php:7
* @route '/admin/billing-products'
*/
ListBillingProducts.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListBillingProducts.url(options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\BillingProducts\Pages\ListBillingProducts::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/ListBillingProducts.php:7
* @route '/admin/billing-products'
*/
const ListBillingProductsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListBillingProducts.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\BillingProducts\Pages\ListBillingProducts::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/ListBillingProducts.php:7
* @route '/admin/billing-products'
*/
ListBillingProductsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListBillingProducts.url(options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\BillingProducts\Pages\ListBillingProducts::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/ListBillingProducts.php:7
* @route '/admin/billing-products'
*/
ListBillingProductsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: ListBillingProducts.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

ListBillingProducts.form = ListBillingProductsForm

export default ListBillingProducts