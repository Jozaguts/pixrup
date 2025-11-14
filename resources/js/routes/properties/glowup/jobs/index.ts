import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/properties/{property}/glowup/jobs'
*/
export const index = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/properties/{property}/glowup/jobs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/properties/{property}/glowup/jobs'
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
* @route '/properties/{property}/glowup/jobs'
*/
index.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/properties/{property}/glowup/jobs'
*/
index.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/properties/{property}/glowup/jobs'
*/
const indexForm = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/properties/{property}/glowup/jobs'
*/
indexForm.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::index
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:25
* @route '/properties/{property}/glowup/jobs'
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
* @route '/properties/{property}/glowup/jobs'
*/
export const store = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/properties/{property}/glowup/jobs',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::store
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:58
* @route '/properties/{property}/glowup/jobs'
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
* @route '/properties/{property}/glowup/jobs'
*/
store.post = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::store
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:58
* @route '/properties/{property}/glowup/jobs'
*/
const storeForm = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::store
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:58
* @route '/properties/{property}/glowup/jobs'
*/
storeForm.post = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/properties/{property}/glowup/jobs/{glowupJob}'
*/
export const show = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/properties/{property}/glowup/jobs/{glowupJob}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/properties/{property}/glowup/jobs/{glowupJob}'
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
* @route '/properties/{property}/glowup/jobs/{glowupJob}'
*/
show.get = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/properties/{property}/glowup/jobs/{glowupJob}'
*/
show.head = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/properties/{property}/glowup/jobs/{glowupJob}'
*/
const showForm = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/properties/{property}/glowup/jobs/{glowupJob}'
*/
showForm.get = (args: { property: number | { id: number }, glowupJob: number | { id: number } } | [property: number | { id: number }, glowupJob: number | { id: number } ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\GlowUp\GlowUpJobController::show
* @see app/Http/Controllers/GlowUp/GlowUpJobController.php:51
* @route '/properties/{property}/glowup/jobs/{glowupJob}'
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

const jobs = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
}

export default jobs