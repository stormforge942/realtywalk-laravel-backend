import Vue from 'vue'
import Vuex from 'vuex'
import auth from './modules/auth'
import home from './modules/home'
import alert from './modules/alert'
import property from './modules/property'
import builder from './modules/builder'

Vue.use(Vuex)

export default new Vuex.Store({
    modules: {
        auth,
        home,
        alert,
        property,
        builder,
    }
})
