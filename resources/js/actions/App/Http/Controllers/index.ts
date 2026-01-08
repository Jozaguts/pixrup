import Billing from './Billing'
import DashboardController from './DashboardController'
import GlowUp from './GlowUp'
import Api from './Api'
import FeaturesController from './FeaturesController'
import BlogController from './BlogController'
import Settings from './Settings'
import Reports from './Reports'

const Controllers = {
    Billing: Object.assign(Billing, Billing),
    DashboardController: Object.assign(DashboardController, DashboardController),
    GlowUp: Object.assign(GlowUp, GlowUp),
    Api: Object.assign(Api, Api),
    FeaturesController: Object.assign(FeaturesController, FeaturesController),
    BlogController: Object.assign(BlogController, BlogController),
    Settings: Object.assign(Settings, Settings),
    Reports: Object.assign(Reports, Reports),
}

export default Controllers