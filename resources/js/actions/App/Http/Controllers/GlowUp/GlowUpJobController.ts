import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
export const history = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(options),
    method: 'get',
})

history.definition = {
    methods: ["get","head"],
    url: '/api/glowup/jobs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
history.url = (options?: RouteQueryOptions) => {
    return history.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
history.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: history.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
history.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: history.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
const historyForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: history.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
*/
historyForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: history.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::history
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:36
* @route '/api/glowup/jobs'
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
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/api/properties/{property}/glowup/jobs'
*/
export const index = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/properties/{property}/glowup/jobs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/api/properties/{property}/glowup/jobs'
*/
index.url = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/api/properties/{property}/glowup/jobs'
*/
index.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/api/properties/{property}/glowup/jobs'
*/
index.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/api/properties/{property}/glowup/jobs'
*/
const indexForm = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/api/properties/{property}/glowup/jobs'
*/
indexForm.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/api/properties/{property}/glowup/jobs'
*/
indexForm.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::store
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:58
* @route '/api/properties/{property}/glowup/jobs'
*/
export const store = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/properties/{property}/glowup/jobs',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::store
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:58
* @route '/api/properties/{property}/glowup/jobs'
*/
store.url = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::store
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:58
* @route '/api/properties/{property}/glowup/jobs'
*/
store.post = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::store
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:58
* @route '/api/properties/{property}/glowup/jobs'
*/
const storeForm = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::store
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:58
* @route '/api/properties/{property}/glowup/jobs'
*/
storeForm.post = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/api/properties/{property}/glowup/jobs/{glowupJob}'
*/
export const show = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/properties/{property}/glowup/jobs/{glowupJob}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/api/properties/{property}/glowup/jobs/{glowupJob}'
*/
show.url = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            property: args[0],
            glowupJob: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        property: typeof args.property === 'object'
        ? args.property.id
        : args.property,
        glowupJob: typeof args.glowupJob === 'object'
        ? args.glowupJob.id
        : args.glowupJob,
    }

    return show.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace('{glowupJob}', parsedArgs.glowupJob.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/api/properties/{property}/glowup/jobs/{glowupJob}'
*/
show.get = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/api/properties/{property}/glowup/jobs/{glowupJob}'
*/
show.head = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/api/properties/{property}/glowup/jobs/{glowupJob}'
*/
const showForm = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/api/properties/{property}/glowup/jobs/{glowupJob}'
*/
showForm.get = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/api/properties/{property}/glowup/jobs/{glowupJob}'
*/
showForm.head = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

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

const GlowUpJobController = { history, index, store, show, attach }

export default GlowUpJobController