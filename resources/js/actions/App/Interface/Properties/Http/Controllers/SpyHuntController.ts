import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::show
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/spyhunt'
*/
export const show = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/properties/{property}/spyhunt',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::show
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/spyhunt'
*/
show.url = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { property: args }
    }

    if (Array.isArray(args)) {
        args = {
            property: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        property: args.property,
    }

    return show.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::show
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/spyhunt'
*/
show.get = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::show
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/spyhunt'
*/
show.head = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::show
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/spyhunt'
*/
const showForm = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::show
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/spyhunt'
*/
showForm.get = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::show
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/spyhunt'
*/
showForm.head = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mlsRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
export const mlsRefresh = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mlsRefresh.url(args, options),
    method: 'get',
})

mlsRefresh.definition = {
    methods: ["get","head"],
    url: '/properties/{property}/mls-refresh',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mlsRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
mlsRefresh.url = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { property: args }
    }

    if (Array.isArray(args)) {
        args = {
            property: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        property: args.property,
    }

    return mlsRefresh.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mlsRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
mlsRefresh.get = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mlsRefresh.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mlsRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
mlsRefresh.head = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mlsRefresh.url(args, options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mlsRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
const mlsRefreshForm = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mlsRefresh.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mlsRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
mlsRefreshForm.get = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mlsRefresh.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mlsRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
mlsRefreshForm.head = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mlsRefresh.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

mlsRefresh.form = mlsRefreshForm

const SpyHuntController = { show, mlsRefresh, mls-refresh: mlsRefresh }

export default SpyHuntController