import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::store
* @see app/Interface/Auth/Http/Controllers/AuthController.php:28
* @route '/register'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::store
* @see app/Interface/Auth/Http/Controllers/AuthController.php:28
* @route '/register'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::store
* @see app/Interface/Auth/Http/Controllers/AuthController.php:28
* @route '/register'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

const register = {
    show: Object.assign(show, show),
    store: Object.assign(store, store),
}

export default register