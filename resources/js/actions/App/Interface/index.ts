import Auth from './Auth'
import Appraisal from './Appraisal'

const Interface = {
    Auth: Object.assign(Auth, Auth),
    Appraisal: Object.assign(Appraisal, Appraisal),
}

export default Interface