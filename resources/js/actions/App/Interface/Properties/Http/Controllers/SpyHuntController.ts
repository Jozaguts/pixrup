import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::fetch
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:16
* @route '/properties/{property}/fetch'
*/
export const fetch = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fetch.url(args, options),
    method: 'get',
})

fetch.definition = {
    methods: ["get","head"],
    url: '/properties/{property}/fetch',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::fetch
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:16
* @route '/properties/{property}/fetch'
*/
fetch.url = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return fetch.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::fetch
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:16
* @route '/properties/{property}/fetch'
*/
fetch.get = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fetch.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::fetch
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:16
* @route '/properties/{property}/fetch'
*/
fetch.head = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: fetch.url(args, options),
    method: 'head',
})

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

const SpyHuntController = { fetch, mlsRefresh, mls-refresh: mlsRefresh }

export default SpyHuntController