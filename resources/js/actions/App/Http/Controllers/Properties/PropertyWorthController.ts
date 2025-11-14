import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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

const PropertyWorthController = { report }

export default PropertyWorthController