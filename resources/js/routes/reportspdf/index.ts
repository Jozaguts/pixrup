import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import logosB52404 from './logos'
/**
* @see \App\Http\Controllers\Reports\PdfReportsController::index
* @see app/Http/Controllers/Reports/PdfReportsController.php:15
* @route '/reports'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::index
* @see app/Http/Controllers/Reports/PdfReportsController.php:15
* @route '/reports'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::index
* @see app/Http/Controllers/Reports/PdfReportsController.php:15
* @route '/reports'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::index
* @see app/Http/Controllers/Reports/PdfReportsController.php:15
* @route '/reports'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::newMethod
* @see app/Http/Controllers/Reports/PdfReportsController.php:20
* @route '/reports/new'
*/
export const newMethod = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: newMethod.url(options),
    method: 'get',
})

newMethod.definition = {
    methods: ["get","head"],
    url: '/reports/new',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::newMethod
* @see app/Http/Controllers/Reports/PdfReportsController.php:20
* @route '/reports/new'
*/
newMethod.url = (options?: RouteQueryOptions) => {
    return newMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::newMethod
* @see app/Http/Controllers/Reports/PdfReportsController.php:20
* @route '/reports/new'
*/
newMethod.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: newMethod.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::newMethod
* @see app/Http/Controllers/Reports/PdfReportsController.php:20
* @route '/reports/new'
*/
newMethod.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: newMethod.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::logos
* @see app/Http/Controllers/Reports/PdfReportsController.php:55
* @route '/reports/logos'
*/
export const logos = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logos.url(options),
    method: 'get',
})

logos.definition = {
    methods: ["get","head"],
    url: '/reports/logos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::logos
* @see app/Http/Controllers/Reports/PdfReportsController.php:55
* @route '/reports/logos'
*/
logos.url = (options?: RouteQueryOptions) => {
    return logos.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::logos
* @see app/Http/Controllers/Reports/PdfReportsController.php:55
* @route '/reports/logos'
*/
logos.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logos.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::logos
* @see app/Http/Controllers/Reports/PdfReportsController.php:55
* @route '/reports/logos'
*/
logos.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logos.url(options),
    method: 'head',
})

const reportspdf = {
    index: Object.assign(index, index),
    new: Object.assign(newMethod, newMethod),
    logos: Object.assign(logos, logosB52404),
}

export default reportspdf