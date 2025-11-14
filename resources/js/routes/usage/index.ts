import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/v1/usage'
*/
export const summary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

summary.definition = {
    methods: ["get","head"],
    url: '/v1/usage',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/v1/usage'
*/
summary.url = (options?: RouteQueryOptions) => {
    return summary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/v1/usage'
*/
summary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\UsageSummaryController::__invoke
* @see app/Http/Controllers/Api/UsageSummaryController.php:18
* @route '/v1/usage'
*/
summary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summary.url(options),
    method: 'head',
})

const usage = {
    summary: Object.assign(summary, summary),
}

export default usage