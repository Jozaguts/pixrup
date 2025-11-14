import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:18
* @route '/login'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:18
* @route '/login'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:18
* @route '/login'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:18
* @route '/login'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::store
* @see app/Interface/Auth/Http/Controllers/AuthController.php:52
* @route '/login'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::store
* @see app/Interface/Auth/Http/Controllers/AuthController.php:52
* @route '/login'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::store
* @see app/Interface/Auth/Http/Controllers/AuthController.php:52
* @route '/login'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

const login = {
    show: Object.assign(show, show),
    store: Object.assign(store, store),
}

export default login