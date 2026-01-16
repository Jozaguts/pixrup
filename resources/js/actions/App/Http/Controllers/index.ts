import Billing from './Billing'
import DashboardController from './DashboardController'
import GlowUp from './GlowUp'
import Api from './Api'
import Reports from './Reports'
import Settings from './Settings'
import BlogController from './BlogController'
import FeaturesController from './FeaturesController'

const Controllers = {
    Billing: Object.assign(Billing, Billing),
    DashboardController: Object.assign(DashboardController, DashboardController),
    GlowUp: Object.assign(GlowUp, GlowUp),
    Api: Object.assign(Api, Api),
    Reports: Object.assign(Reports, Reports),
    Settings: Object.assign(Settings, Settings),
    BlogController: Object.assign(BlogController, BlogController),
    FeaturesController: Object.assign(FeaturesController, FeaturesController),
}

export default Controllers