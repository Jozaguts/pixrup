import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Interface\Appraisal\Http\Controllers\PropertyWorthController::fetch
* @see app/Interface/Appraisal/Http/Controllers/PropertyWorthController.php:35
* @route '/properties/{property}/worth/fetch'
*/
export const fetch = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: fetch.url(args, options),
    method: 'post',
})

fetch.definition = {
    methods: ["post"],
    url: '/properties/{property}/worth/fetch',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Interface\Appraisal\Http\Controllers\PropertyWorthController::fetch
* @see app/Interface/Appraisal/Http/Controllers/PropertyWorthController.php:35
* @route '/properties/{property}/worth/fetch'
*/
fetch.url = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return fetch.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Interface\Appraisal\Http\Controllers\PropertyWorthController::fetch
* @see app/Interface/Appraisal/Http/Controllers/PropertyWorthController.php:35
* @route '/properties/{property}/worth/fetch'
*/
fetch.post = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: fetch.url(args, options),
    method: 'post',
})

/**
* @see \App\Interface\Appraisal\Http\Controllers\PropertyWorthController::fetch
* @see app/Interface/Appraisal/Http/Controllers/PropertyWorthController.php:35
* @route '/properties/{property}/worth/fetch'
*/
const fetchForm = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: fetch.url(args, options),
    method: 'post',
})

/**
* @see \App\Interface\Appraisal\Http\Controllers\PropertyWorthController::fetch
* @see app/Interface/Appraisal/Http/Controllers/PropertyWorthController.php:35
* @route '/properties/{property}/worth/fetch'
*/
fetchForm.post = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: fetch.url(args, options),
    method: 'post',
})

fetch.form = fetchForm

/**
* @see \App\Http\Controllers\Properties\PropertyWorthController::report
* @see app/Http/Controllers/Properties/PropertyWorthController.php:37
* @route '/properties/{property}/worth/report'
*/
export const report = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: report.url(args, options),
    method: 'post',
})

report.definition = {
    methods: ["post"],
    url: '/properties/{property}/worth/report',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Properties\PropertyWorthController::report
* @see app/Http/Controllers/Properties/PropertyWorthController.php:37
* @route '/properties/{property}/worth/report'
*/
report.url = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return report.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Properties\PropertyWorthController::report
* @see app/Http/Controllers/Properties/PropertyWorthController.php:37
* @route '/properties/{property}/worth/report'
*/
report.post = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: report.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Properties\PropertyWorthController::report
* @see app/Http/Controllers/Properties/PropertyWorthController.php:37
* @route '/properties/{property}/worth/report'
*/
const reportForm = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: report.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Properties\PropertyWorthController::report
* @see app/Http/Controllers/Properties/PropertyWorthController.php:37
* @route '/properties/{property}/worth/report'
*/
reportForm.post = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: report.url(args, options),
    method: 'post',
})

report.form = reportForm

const worth = {
    fetch: Object.assign(fetch, fetch),
    report: Object.assign(report, report),
}

export default worth