import DashboardController from './DashboardController'
import Properties from './Properties'
import GlowUp from './GlowUp'
import Api from './Api'
import Settings from './Settings'

const Controllers = {
    DashboardController: Object.assign(DashboardController, DashboardController),
    Properties: Object.assign(Properties, Properties),
    GlowUp: Object.assign(GlowUp, GlowUp),
    Api: Object.assign(Api, Api),
    Settings: Object.assign(Settings, Settings),
}

export default Controllers