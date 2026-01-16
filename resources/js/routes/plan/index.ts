import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:101
* @route '/plan/upgrade'
*/
export const upgrade = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upgrade.url(options),
    method: 'get',
})

upgrade.definition = {
    methods: ["get","head"],
    url: '/plan/upgrade',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:101
* @route '/plan/upgrade'
*/
upgrade.url = (options?: RouteQueryOptions) => {
    return upgrade.definition.url + queryParams(options)
}

/**
* @see routes/web.php:101
* @route '/plan/upgrade'
*/
upgrade.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upgrade.url(options),
    method: 'get',
})

/**
* @see routes/web.php:101
* @route '/plan/upgrade'
*/
upgrade.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: upgrade.url(options),
    method: 'head',
})

/**
* @see routes/web.php:101
* @route '/plan/upgrade'
*/
const upgradeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: upgrade.url(options),
    method: 'get',
})

/**
* @see routes/web.php:101
* @route '/plan/upgrade'
*/
upgradeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: upgrade.url(options),
    method: 'get',
})

/**
* @see routes/web.php:101
* @route '/plan/upgrade'
*/
upgradeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: upgrade.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

upgrade.form = upgradeForm

const plan = {
    upgrade: Object.assign(upgrade, upgrade),
}

export default plan