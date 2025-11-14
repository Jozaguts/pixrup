import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/api/v1/usage'
*/
const UsageSummaryController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: UsageSummaryController.url(options),
    method: 'get',
})

UsageSummaryController.definition = {
    methods: ["get","head"],
    url: '/api/v1/usage',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/api/v1/usage'
*/
UsageSummaryController.url = (options?: RouteQueryOptions) => {
    return UsageSummaryController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/api/v1/usage'
*/
UsageSummaryController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: UsageSummaryController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/api/v1/usage'
*/
UsageSummaryController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: UsageSummaryController.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/api/v1/usage'
*/
const UsageSummaryControllerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: UsageSummaryController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/api/v1/usage'
*/
UsageSummaryControllerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: UsageSummaryController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/api/v1/usage'
*/
UsageSummaryControllerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: UsageSummaryController.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

UsageSummaryController.form = UsageSummaryControllerForm

export default UsageSummaryController