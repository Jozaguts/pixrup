import PropertyController from './PropertyController'
import SpyHuntController from './SpyHuntController'
import BillingController from './BillingController'

const Controllers = {
    PropertyController: Object.assign(PropertyController, PropertyController),
    SpyHuntController: Object.assign(SpyHuntController, SpyHuntController),
    BillingController: Object.assign(BillingController, BillingController),
}

export default Controllers