import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\BillingProducts\Pages\EditBillingProduct::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/EditBillingProduct.php:7
* @route '/admin/billing-products/{record}/edit'
*/
const EditBillingProduct = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditBillingProduct.url(args, options),
    method: 'get',
})

EditBillingProduct.definition = {
    methods: ["get","head"],
    url: '/admin/billing-products/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\BillingProducts\Pages\EditBillingProduct::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/EditBillingProduct.php:7
* @route '/admin/billing-products/{record}/edit'
*/
EditBillingProduct.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { record: args }
    }

    if (Array.isArray(args)) {
        args = {
            record: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        record: args.record,
    }

    return EditBillingProduct.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\BillingProducts\Pages\EditBillingProduct::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/EditBillingProduct.php:7
* @route '/admin/billing-products/{record}/edit'
*/
EditBillingProduct.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditBillingProduct.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\BillingProducts\Pages\EditBillingProduct::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/EditBillingProduct.php:7
* @route '/admin/billing-products/{record}/edit'
*/
EditBillingProduct.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditBillingProduct.url(args, options),
    method: 'head',
})

/**
* @see \App\Filament\Resources\BillingProducts\Pages\EditBillingProduct::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/EditBillingProduct.php:7
* @route '/admin/billing-products/{record}/edit'
*/
const EditBillingProductForm = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditBillingProduct.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\BillingProducts\Pages\EditBillingProduct::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/EditBillingProduct.php:7
* @route '/admin/billing-products/{record}/edit'
*/
EditBillingProductForm.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditBillingProduct.url(args, options),
    method: 'get',
})

/**
* @see \App\Filament\Resources\BillingProducts\Pages\EditBillingProduct::__invoke
* @see app/Filament/Resources/BillingProducts/Pages/EditBillingProduct.php:7
* @route '/admin/billing-products/{record}/edit'
*/
EditBillingProductForm.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: EditBillingProduct.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

EditBillingProduct.form = EditBillingProductForm

export default EditBillingProduct