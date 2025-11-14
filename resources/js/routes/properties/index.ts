import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import worth from './worth'
import glowup from './glowup'
/**
* @see \App\Http\Controllers\Properties\PropertyController::newMethod
* @see app/Http/Controllers/Properties/PropertyController.php:20
* @route '/properties/new'
*/
export const newMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: newMethod.url(options),
    method: 'get',
})

newMethod.definition = {
    methods: ["get","head"],
    url: '/properties/new',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Properties\PropertyController::newMethod
* @see app/Http/Controllers/Properties/PropertyController.php:20
* @route '/properties/new'
*/
newMethod.url = (options?: RouteQueryOptions) => {
    return newMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Properties\PropertyController::newMethod
* @see app/Http/Controllers/Properties/PropertyController.php:20
* @route '/properties/new'
*/
newMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: newMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Properties\PropertyController::newMethod
* @see app/Http/Controllers/Properties/PropertyController.php:20
* @route '/properties/new'
*/
newMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: newMethod.url(options),
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

const properties = {
    new: Object.assign(newMethod, newMethod),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    worth: Object.assign(worth, worth),
    glowup: Object.assign(glowup, glowup),
}

export default properties