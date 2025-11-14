import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/glowup/jobs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::attach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:116
* @route '/api/glowup/jobs/{glowupJob}/attach'
*/
export const attach = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: attach.url(args, options),
    method: 'post',
})

attach.definition = {
    methods: ["post"],
    url: '/api/glowup/jobs/{glowupJob}/attach',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::attach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:116
* @route '/api/glowup/jobs/{glowupJob}/attach'
*/
attach.url = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { glowupJob: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { glowupJob: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            glowupJob: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        glowupJob: typeof args.glowupJob === 'object'
        ? args.glowupJob.id
        : args.glowupJob,
    }

    return attach.definition.url
            .replace('{glowupJob}', parsedArgs.glowupJob.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::attach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:116
* @route '/api/glowup/jobs/{glowupJob}/attach'
*/
attach.post = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: attach.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::attach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:116
* @route '/api/glowup/jobs/{glowupJob}/attach'
*/
const attachForm = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: attach.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::attach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:116
* @route '/api/glowup/jobs/{glowupJob}/attach'
*/
attachForm.post = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: attach.url(args, options),
    method: 'post',
})

attach.form = attachForm

const jobs = {
    index: Object.assign(index, index),
    attach: Object.assign(attach, attach),
}

export default jobs