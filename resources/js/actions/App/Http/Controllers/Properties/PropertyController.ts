import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Properties\PropertyController::create
* @see app/Http/Controllers/Properties/PropertyController.php:20
* @route '/properties/new'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/properties/new',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Properties\PropertyController::create
* @see app/Http/Controllers/Properties/PropertyController.php:20
* @route '/properties/new'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Properties\PropertyController::create
* @see app/Http/Controllers/Properties/PropertyController.php:20
* @route '/properties/new'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Properties\PropertyController::create
* @see app/Http/Controllers/Properties/PropertyController.php:20
* @route '/properties/new'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Properties\PropertyController::store
* @see app/Http/Controllers/Properties/PropertyController.php:154
* @route '/properties'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/properties',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Properties\PropertyController::store
* @see app/Http/Controllers/Properties/PropertyController.php:154
* @route '/properties'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Properties\PropertyController::store
* @see app/Http/Controllers/Properties/PropertyController.php:154
* @route '/properties'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Properties\PropertyController::show
* @see app/Http/Controllers/Properties/PropertyController.php:25
* @route '/properties/{property}'
*/
export const show = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/properties/{property}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Properties\PropertyController::show
* @see app/Http/Controllers/Properties/PropertyController.php:25
* @route '/properties/{property}'
*/
show.url = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { property: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { property: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            property: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        property: typeof args.property === 'object'
        ? args.property.id
        : args.property,
    }

    return show.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Properties\PropertyController::show
* @see app/Http/Controllers/Properties/PropertyController.php:25
* @route '/properties/{property}'
*/
show.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Properties\PropertyController::show
* @see app/Http/Controllers/Properties/PropertyController.php:25
* @route '/properties/{property}'
*/
show.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

const PropertyController = { create, store, show }

export default PropertyController