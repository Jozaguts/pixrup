import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::showLogin
* @see app/Interface/Auth/Http/Controllers/AuthController.php:18
* @route '/login'
*/
export const showLogin = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showLogin.url(options),
    method: 'get',
})

showLogin.definition = {
    methods: ["get","head"],
    url: '/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::showLogin
* @see app/Interface/Auth/Http/Controllers/AuthController.php:18
* @route '/login'
*/
showLogin.url = (options?: RouteQueryOptions) => {
    return showLogin.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::showLogin
* @see app/Interface/Auth/Http/Controllers/AuthController.php:18
* @route '/login'
*/
showLogin.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showLogin.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::showLogin
* @see app/Interface/Auth/Http/Controllers/AuthController.php:18
* @route '/login'
*/
showLogin.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showLogin.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::login
* @see app/Interface/Auth/Http/Controllers/AuthController.php:52
* @route '/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

login.definition = {
    methods: ["post"],
    url: '/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::login
* @see app/Interface/Auth/Http/Controllers/AuthController.php:52
* @route '/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::login
* @see app/Interface/Auth/Http/Controllers/AuthController.php:52
* @route '/login'
*/
login.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

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
* @see \App\Interface\Auth\Http\Controllers\AuthController::showRegister
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
export const showRegister = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showRegister.url(options),
    method: 'get',
})

showRegister.definition = {
    methods: ["get","head"],
    url: '/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::showRegister
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
showRegister.url = (options?: RouteQueryOptions) => {
    return showRegister.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::showRegister
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
showRegister.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showRegister.url(options),
    method: 'get',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::showRegister
* @see app/Interface/Auth/Http/Controllers/AuthController.php:23
* @route '/register'
*/
showRegister.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showRegister.url(options),
    method: 'head',
})

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::register
* @see app/Interface/Auth/Http/Controllers/AuthController.php:28
* @route '/register'
*/
export const register = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: register.url(options),
    method: 'post',
})

register.definition = {
    methods: ["post"],
    url: '/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::register
* @see app/Interface/Auth/Http/Controllers/AuthController.php:28
* @route '/register'
*/
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Interface\Auth\Http\Controllers\AuthController::register
* @see app/Interface/Auth/Http/Controllers/AuthController.php:28
* @route '/register'
*/
register.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: register.url(options),
    method: 'post',
})

const AuthController = { showLogin, login, logout, showRegister, register }

export default AuthController