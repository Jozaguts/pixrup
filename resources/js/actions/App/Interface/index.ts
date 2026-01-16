import Auth from './Auth'
import Properties from './Properties'
import Appraisal from './Appraisal'

const Interface = {
    Auth: Object.assign(Auth, Auth),
    Properties: Object.assign(Properties, Properties),
    Appraisal: Object.assign(Appraisal, Appraisal),
}

export default Interface