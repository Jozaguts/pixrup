import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import login from './login'
import register from './register'
import google from './google'
/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::logout
* @see app/Interface/Auth/Http/Controllers/AuthController.php:85
* @route '/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::logout
* @see app/Interface/Auth/Http/Controllers/AuthController.php:85
* @route '/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::logout
* @see app/Interface/Auth/Http/Controllers/AuthController.php:85
* @route '/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::logout
* @see app/Interface/Auth/Http/Controllers/AuthController.php:85
* @route '/logout'
*/
const logoutForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::logout
* @see app/Interface/Auth/Http/Controllers/AuthController.php:85
* @route '/logout'
*/
logoutForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

logout.form = logoutForm

const auth = {
    login: Object.assign(login, login),
    logout: Object.assign(logout, logout),
    register: Object.assign(register, register),
    google: Object.assign(google, google),
}

export default auth