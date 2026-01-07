import Cashier from './Cashier'
import Fortify from './Fortify'
import Horizon from './Horizon'
import Telescope from './Telescope'

const Laravel = {
    Cashier: Object.assign(Cashier, Cashier),
    Fortify: Object.assign(Fortify, Fortify),
    Horizon: Object.assign(Horizon, Horizon),
    Telescope: Object.assign(Telescope, Telescope),
}

export default Laravel