import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Interface\Auth\Http\Controllers\SocialAuthController::redirect
* @see app/Interface/Auth/Http/Controllers/SocialAuthController.php:17
* @route '/auth/google/redirect'
*/
export const redirect = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirect.url(options),
    method: 'get',
})

redirect.definition = {
    methods: ["get","head"],
    url: '/auth/google/redirect',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Auth\Http\Controllers\SocialAuthController::redirect
* @see app/Interface/Auth/Http/Controllers/SocialAuthController.php:17
* @route '/auth/google/redirect'
*/
redirect.url = (options?: RouteQueryOptions) => {
    return redirect.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\SocialAuthController::redirect
* @see app/Interface/Auth/Http/Controllers/SocialAuthController.php:17
* @route '/auth/google/redirect'
*/
redirect.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: redirect.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Auth\Http\Controllers\SocialAuthController::redirect
* @see app/Interface/Auth/Http/Controllers/SocialAuthController.php:17
* @route '/auth/google/redirect'
*/
redirect.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: redirect.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Auth\Http\Controllers\SocialAuthController::callback
* @see app/Interface/Auth/Http/Controllers/SocialAuthController.php:22
* @route '/auth/google/callback'
*/
export const callback = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: callback.url(options),
    method: 'get',
})

callback.definition = {
    methods: ["get","head"],
    url: '/auth/google/callback',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Auth\Http\Controllers\SocialAuthController::callback
* @see app/Interface/Auth/Http/Controllers/SocialAuthController.php:22
* @route '/auth/google/callback'
*/
callback.url = (options?: RouteQueryOptions) => {
    return callback.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\SocialAuthController::callback
* @see app/Interface/Auth/Http/Controllers/SocialAuthController.php:22
* @route '/auth/google/callback'
*/
callback.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: callback.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Auth\Http\Controllers\SocialAuthController::callback
* @see app/Interface/Auth/Http/Controllers/SocialAuthController.php:22
* @route '/auth/google/callback'
*/
callback.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: callback.url(options),
    method: 'head',
})

const SocialAuthController = { redirect, callback }

export default SocialAuthController