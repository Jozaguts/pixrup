import glowup from './glowup'
import properties from './properties'
import usage from './usage'

const api = {
    glowup: Object.assign(glowup, glowup),
    properties: Object.assign(properties, properties),
    usage: Object.assign(usage, usage),
}

export default api