import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Reports\PdfReportsController::getLogos
* @see app/Http/Controllers/Reports/PdfReportsController.php:55
* @route '/reports/logos'
*/
export const getLogos = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLogos.url(options),
    method: 'get',
})

getLogos.definition = {
    methods: ["get","head"],
    url: '/reports/logos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::getLogos
* @see app/Http/Controllers/Reports/PdfReportsController.php:55
* @route '/reports/logos'
*/
getLogos.url = (options?: RouteQueryOptions) => {
    return getLogos.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::getLogos
* @see app/Http/Controllers/Reports/PdfReportsController.php:55
* @route '/reports/logos'
*/
getLogos.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getLogos.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::getLogos
* @see app/Http/Controllers/Reports/PdfReportsController.php:55
* @route '/reports/logos'
*/
getLogos.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getLogos.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::storeLogo
* @see app/Http/Controllers/Reports/PdfReportsController.php:25
* @route '/reports/logos/create'
*/
export const storeLogo = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeLogo.url(options),
    method: 'post',
})

storeLogo.definition = {
    methods: ["post"],
    url: '/reports/logos/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::storeLogo
* @see app/Http/Controllers/Reports/PdfReportsController.php:25
* @route '/reports/logos/create'
*/
storeLogo.url = (options?: RouteQueryOptions) => {
    return storeLogo.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Reports\PdfReportsController::storeLogo
* @see app/Http/Controllers/Reports/PdfReportsController.php:25
* @route '/reports/logos/create'
*/
storeLogo.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeLogo.url(options),
    method: 'post',
})

const PdfReportsController = { index, newMethod, getLogos, storeLogo, new: newMethod }

export default PdfReportsController