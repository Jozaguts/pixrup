import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
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
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
const showForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
showForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::show
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
showForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

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

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::store
* @see app/Interface/Auth/Http/Controllers/AuthController.php:28
* @route '/register'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::store
* @see app/Interface/Auth/Http/Controllers/AuthController.php:28
* @route '/register'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const register = {
    show: Object.assign(show, show),
    store: Object.assign(store, store),
}

export default register