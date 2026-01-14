import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\FeaturesController::index
* @see app/Http/Controllers/FeaturesController.php:13
* @route '/features'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/features',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\FeaturesController::index
* @see app/Http/Controllers/FeaturesController.php:13
* @route '/features'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\FeaturesController::index
* @see app/Http/Controllers/FeaturesController.php:13
* @route '/features'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FeaturesController::index
* @see app/Http/Controllers/FeaturesController.php:13
* @route '/features'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\FeaturesController::index
* @see app/Http/Controllers/FeaturesController.php:13
* @route '/features'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FeaturesController::index
* @see app/Http/Controllers/FeaturesController.php:13
* @route '/features'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FeaturesController::index
* @see app/Http/Controllers/FeaturesController.php:13
* @route '/features'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\FeaturesController::show
* @see app/Http/Controllers/FeaturesController.php:18
* @route '/features/{feature}'
*/
export const show = (args: { feature: string | { slug: string } } | [feature: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/features/{feature}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\FeaturesController::show
* @see app/Http/Controllers/FeaturesController.php:18
* @route '/features/{feature}'
*/
show.url = (args: { feature: string | { slug: string } } | [feature: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { feature: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { feature: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            feature: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        feature: typeof args.feature === 'object'
        ? args.feature.slug
        : args.feature,
    }

    return show.definition.url
            .replace('{feature}', parsedArgs.feature.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\FeaturesController::show
* @see app/Http/Controllers/FeaturesController.php:18
* @route '/features/{feature}'
*/
show.get = (args: { feature: string | { slug: string } } | [feature: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FeaturesController::show
* @see app/Http/Controllers/FeaturesController.php:18
* @route '/features/{feature}'
*/
show.head = (args: { feature: string | { slug: string } } | [feature: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\FeaturesController::show
* @see app/Http/Controllers/FeaturesController.php:18
* @route '/features/{feature}'
*/
const showForm = (args: { feature: string | { slug: string } } | [feature: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FeaturesController::show
* @see app/Http/Controllers/FeaturesController.php:18
* @route '/features/{feature}'
*/
showForm.get = (args: { feature: string | { slug: string } } | [feature: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\FeaturesController::show
* @see app/Http/Controllers/FeaturesController.php:18
* @route '/features/{feature}'
*/
showForm.head = (args: { feature: string | { slug: string } } | [feature: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const features = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
}

export default features