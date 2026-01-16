import Auth from './Auth'
import Properties from './Properties'

const Interface = {
    Auth: Object.assign(Auth, Auth),
    Properties: Object.assign(Properties, Properties),
}

export default Interface