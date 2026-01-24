import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\DashboardController::test
* @see app/Http/Controllers/DashboardController.php:50
* @route '/test/{property}'
*/
export const test = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: test.url(args, options),
    method: 'get',
})

test.definition = {
    methods: ["get","head"],
    url: '/test/{property}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::test
* @see app/Http/Controllers/DashboardController.php:50
* @route '/test/{property}'
*/
test.url = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return test.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::test
* @see app/Http/Controllers/DashboardController.php:50
* @route '/test/{property}'
*/
test.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: test.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::test
* @see app/Http/Controllers/DashboardController.php:50
* @route '/test/{property}'
*/
test.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: test.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::test
* @see app/Http/Controllers/DashboardController.php:50
* @route '/test/{property}'
*/
const testForm = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: test.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::test
* @see app/Http/Controllers/DashboardController.php:50
* @route '/test/{property}'
*/
testForm.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: test.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::test
* @see app/Http/Controllers/DashboardController.php:50
* @route '/test/{property}'
*/
testForm.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: test.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

test.form = testForm

const report = {
    test: Object.assign(test, test),
}

export default report