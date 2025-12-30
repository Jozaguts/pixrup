import AuthController from './AuthController'
import SocialAuthController from './SocialAuthController'

const Controllers = {
    AuthController: Object.assign(AuthController, AuthController),
    SocialAuthController: Object.assign(SocialAuthController, SocialAuthController),
}

export default Controllers