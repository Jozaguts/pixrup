import PropertyController from './PropertyController'
import PropertyWorthController from './PropertyWorthController'

const Properties = {
    PropertyController: Object.assign(PropertyController, PropertyController),
    PropertyWorthController: Object.assign(PropertyWorthController, PropertyWorthController),
}

export default Properties