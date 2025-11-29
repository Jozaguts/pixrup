import PropertyController from './PropertyController'
import SpyHuntController from './SpyHuntController'

const Controllers = {
    PropertyController: Object.assign(PropertyController, PropertyController),
    SpyHuntController: Object.assign(SpyHuntController, SpyHuntController),
}

export default Controllers