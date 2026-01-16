import BillingController from './BillingController'
import PropertyController from './PropertyController'
import SpyHuntController from './SpyHuntController'

const Controllers = {
    BillingController: Object.assign(BillingController, BillingController),
    PropertyController: Object.assign(PropertyController, PropertyController),
    SpyHuntController: Object.assign(SpyHuntController, SpyHuntController),
}

export default Controllers