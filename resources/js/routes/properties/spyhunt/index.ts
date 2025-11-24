import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
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
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mslRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
export const mslRefresh = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mslRefresh.url(args, options),
    method: 'get',
})

mslRefresh.definition = {
    methods: ["get","head"],
    url: '/properties/{property}/mls-refresh',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mslRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
mslRefresh.url = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return mslRefresh.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mslRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
mslRefresh.get = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mslRefresh.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mslRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
mslRefresh.head = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mslRefresh.url(args, options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mslRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
const mslRefreshForm = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mslRefresh.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mslRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
mslRefreshForm.get = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mslRefresh.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\SpyHuntController::mslRefresh
* @see app/Interface/Properties/Http/Controllers/SpyHuntController.php:0
* @route '/properties/{property}/mls-refresh'
*/
mslRefreshForm.head = (args: { property: string | number } | [property: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: mslRefresh.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

mslRefresh.form = mslRefreshForm

const spyhunt = {
    show: Object.assign(show, show),
    mslRefresh: Object.assign(mslRefresh, mslRefresh),
}

export default spyhunt