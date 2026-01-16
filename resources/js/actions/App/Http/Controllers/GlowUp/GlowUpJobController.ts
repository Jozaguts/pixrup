import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:37
* @route '/glowup/jobs'
*/
export const history = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(options),
    method: 'get',
})

history.definition = {
    methods: ["get","head"],
    url: '/glowup/jobs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:37
* @route '/glowup/jobs'
*/
history.url = (options?: RouteQueryOptions) => {
    return history.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:37
* @route '/glowup/jobs'
*/
history.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:37
* @route '/glowup/jobs'
*/
history.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: history.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:37
* @route '/glowup/jobs'
*/
const historyForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: history.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:37
* @route '/glowup/jobs'
*/
historyForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: history.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:37
* @route '/glowup/jobs'
*/
historyForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: history.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

history.form = historyForm

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::attach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:140
* @route '/glowup/jobs/{glowupJob}/attach'
*/
export const attach = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: attach.url(args, options),
    method: 'post',
})

attach.definition = {
    methods: ["post"],
    url: '/glowup/jobs/{glowupJob}/attach',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::attach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:140
* @route '/glowup/jobs/{glowupJob}/attach'
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
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:140
* @route '/glowup/jobs/{glowupJob}/attach'
*/
attach.post = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: attach.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::attach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:140
* @route '/glowup/jobs/{glowupJob}/attach'
*/
const attachForm = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: attach.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::attach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:140
* @route '/glowup/jobs/{glowupJob}/attach'
*/
attachForm.post = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: attach.url(args, options),
    method: 'post',
})

attach.form = attachForm

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::detach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:172
* @route '/glowup/jobs/{glowupJob}/detach'
*/
export const detach = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: detach.url(args, options),
    method: 'post',
})

detach.definition = {
    methods: ["post"],
    url: '/glowup/jobs/{glowupJob}/detach',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::detach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:172
* @route '/glowup/jobs/{glowupJob}/detach'
*/
detach.url = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return detach.definition.url
            .replace('{glowupJob}', parsedArgs.glowupJob.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::detach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:172
* @route '/glowup/jobs/{glowupJob}/detach'
*/
detach.post = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: detach.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::detach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:172
* @route '/glowup/jobs/{glowupJob}/detach'
*/
const detachForm = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: detach.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::detach
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:172
* @route '/glowup/jobs/{glowupJob}/detach'
*/
detachForm.post = (args: { glowupJob: number | { id: number } } | [glowupJob: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: detach.url(args, options),
    method: 'post',
})

detach.form = detachForm

const GlowUpJobController = { history, attach, detach }

export default GlowUpJobController