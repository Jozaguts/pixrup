import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import worth from './worth'
import glowup from './glowup'
import spyhunt from './spyhunt'
/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::index
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:21
* @route '/properties'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/properties',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::index
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:21
* @route '/properties'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::index
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:21
* @route '/properties'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::index
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:21
* @route '/properties'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::index
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:21
* @route '/properties'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::index
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:21
* @route '/properties'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::index
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:21
* @route '/properties'
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
* @see \App\Interface\Properties\Http\Controllers\PropertyController::newMethod
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:58
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
* @see \App\Interface\Properties\Http\Controllers\PropertyController::newMethod
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:58
* @route '/properties/new'
*/
newMethod.url = (options?: RouteQueryOptions) => {
    return newMethod.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::newMethod
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:58
* @route '/properties/new'
*/
newMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: newMethod.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::newMethod
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:58
* @route '/properties/new'
*/
newMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: newMethod.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::newMethod
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:58
* @route '/properties/new'
*/
const newMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: newMethod.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::newMethod
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:58
* @route '/properties/new'
*/
newMethodForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: newMethod.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::newMethod
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:58
* @route '/properties/new'
*/
newMethodForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: newMethod.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

newMethod.form = newMethodForm

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::store
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:187
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
* @see \App\Interface\Properties\Http\Controllers\PropertyController::store
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:187
* @route '/properties'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::store
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:187
* @route '/properties'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::store
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:187
* @route '/properties'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::store
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:187
* @route '/properties'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::show
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:62
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
* @see \App\Interface\Properties\Http\Controllers\PropertyController::show
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:62
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
* @see \App\Interface\Properties\Http\Controllers\PropertyController::show
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:62
* @route '/properties/{property}'
*/
show.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::show
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:62
* @route '/properties/{property}'
*/
show.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::show
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:62
* @route '/properties/{property}'
*/
const showForm = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::show
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:62
* @route '/properties/{property}'
*/
showForm.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::show
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:62
* @route '/properties/{property}'
*/
showForm.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Interface\Properties\Http\Controllers\PropertyController::overview
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:214
* @route '/properties/{property}/overview'
*/
export const overview = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(args, options),
    method: 'get',
})

overview.definition = {
    methods: ["get","head"],
    url: '/properties/{property}/overview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::overview
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:214
* @route '/properties/{property}/overview'
*/
overview.url = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return overview.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::overview
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:214
* @route '/properties/{property}/overview'
*/
overview.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overview.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::overview
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:214
* @route '/properties/{property}/overview'
*/
overview.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: overview.url(args, options),
    method: 'head',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::overview
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:214
* @route '/properties/{property}/overview'
*/
const overviewForm = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::overview
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:214
* @route '/properties/{property}/overview'
*/
overviewForm.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(args, options),
    method: 'get',
})

/**
* @see \App\Interface\Properties\Http\Controllers\PropertyController::overview
* @see app/Interface/Properties/Http/Controllers/PropertyController.php:214
* @route '/properties/{property}/overview'
*/
overviewForm.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: overview.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

overview.form = overviewForm

const properties = {
    index: Object.assign(index, index),
    new: Object.assign(newMethod, newMethod),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    overview: Object.assign(overview, overview),
    worth: Object.assign(worth, worth),
    glowup: Object.assign(glowup, glowup),
    spyhunt: Object.assign(spyhunt, spyhunt),
}

export default properties