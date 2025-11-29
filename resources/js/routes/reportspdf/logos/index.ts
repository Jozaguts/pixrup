import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Reports\PdfReportsController::newMethod
* @see app/Http/Controllers/Reports/PdfReportsController.php:25
* @route '/reports/logos/create'
*/
export const newMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: newMethod.url(options),
    method: 'post',
})

newMethod.definition = {
    methods: ["post"],
    url: '/reports/logos/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::newMethod
* @see app/Http/Controllers/Reports/PdfReportsController.php:25
* @route '/reports/logos/create'
*/
newMethod.url = (options?: RouteQueryOptions) => {
    return newMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::newMethod
* @see app/Http/Controllers/Reports/PdfReportsController.php:25
* @route '/reports/logos/create'
*/
newMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: newMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::newMethod
* @see app/Http/Controllers/Reports/PdfReportsController.php:25
* @route '/reports/logos/create'
*/
const newMethodForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: newMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::newMethod
* @see app/Http/Controllers/Reports/PdfReportsController.php:25
* @route '/reports/logos/create'
*/
newMethodForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: newMethod.url(options),
    method: 'post',
})

newMethod.form = newMethodForm

const logos = {
    new: Object.assign(newMethod, newMethod),
}

export default logos