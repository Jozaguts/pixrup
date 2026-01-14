import billingProducts from './billing-products'
import features from './features'
import users from './users'

const resources = {
    billingProducts: Object.assign(billingProducts, billingProducts),
    features: Object.assign(features, features),
    users: Object.assign(users, users),
}

export default resources