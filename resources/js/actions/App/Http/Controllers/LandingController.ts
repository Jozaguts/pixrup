import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LandingController::__invoke
* @see app/Http/Controllers/LandingController.php:14
* @route '/'
*/
const LandingController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LandingController.url(options),
    method: 'get',
})

LandingController.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LandingController::__invoke
* @see app/Http/Controllers/LandingController.php:14
* @route '/'
*/
LandingController.url = (options?: RouteQueryOptions) => {
    return LandingController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LandingController::__invoke
* @see app/Http/Controllers/LandingController.php:14
* @route '/'
*/
LandingController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: LandingController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LandingController::__invoke
* @see app/Http/Controllers/LandingController.php:14
* @route '/'
*/
LandingController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: LandingController.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LandingController::__invoke
* @see app/Http/Controllers/LandingController.php:14
* @route '/'
*/
const LandingControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: LandingController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LandingController::__invoke
* @see app/Http/Controllers/LandingController.php:14
* @route '/'
*/
LandingControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: LandingController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LandingController::__invoke
* @see app/Http/Controllers/LandingController.php:14
* @route '/'
*/
LandingControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: LandingController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

LandingController.form = LandingControllerForm

export default LandingController