export default {
  namespaced: true,
  state: {
    property: {
      title: null,
      price_from: 100000,
      price_to: null,
      price_format_id: 1,
      type: 1,
      status: null,
      category_id: null,
      year_built: null,
      bedrooms: null,
      bathrooms_full: null,
      bathrooms_half: null,
      styles_id: [],
      sqft: 1000,
      lot_size: null,
      amenities: [],
      polygon_id: null,
      address_number: null,
      address_street: null,
      unit_number: null,
      zipcode: null,
      video_embed: null,
      desc: null,
      lat: null,
      lng: null,
    },
    errors: {},
    amenities: {
      data: [],
      loading: false,
    },
    loading: false,
  },
  getters: {
    getFormData: state => {
      const formData = new FormData()
      for (let key in state.property) {
        if (Array.isArray(state.property[key])) {
          for (let i in state.property[key]) {
            formData.append(key + '[]', state.property[key][i])
          }
        } else {
          if (state.property[key] !== null) {
            formData.append(key, state.property[key])
          }
        }
      }
      return formData
    },
  },
  mutations: {
    CLEAR_FORM (state) {
      state.property = {
        title: null,
        price_from: 100000,
        price_to: null,
        price_format_id: 1,
        type: 1,
        status: null,
        category_id: null,
        year_built: null,
        bedrooms: null,
        bathrooms_full: null,
        bathrooms_half: null,
        styles_id: [],
        sqft: 1000,
        lot_size: null,
        amenities: [],
        polygon_id: null,
        address_number: null,
        address_street: null,
        unit_number: null,
        zipcode: null,
        video_embed: null,
        desc: null,
        lat: null,
        lng: null,
      }
      state.errors = {}
      state.loading = false
    },
    SET_STATE (state, payload) {
      Object.assign(state, { ...payload })
    },
    REMOVE_ERROR (state, payload) {
      delete state.errors[payload]
    }
  },
  actions: {
    getAmenities ({ commit }) {
      commit('SET_STATE', { amenities: { loading: true } })
      axios.get('/api/property/get_amenities')
        .then(res => {
          commit('SET_STATE', {
            amenities: {
              data: res.data,
              loading: false
            }
          })
        })
        .catch(() => {
          commit('SET_STATE', { amenities: { loading: false } })
        })
    },
    async store ({ commit }, payload) {
      return await new Promise ((resolve, reject) => {
        commit('SET_STATE', { loading: true })
        axios.post('/api/property/add', payload, {
          headers: {
            'content-type': 'multipart/form-data'
          }
        })
          .then(res => {
            commit('CLEAR_FORM')
            resolve(res.data)
          })
          .catch(err => {
            commit('SET_STATE', { loading: false })
            if (err.response.status === 400) {
              commit('SET_STATE', { errors: err.response.data })
            }
            reject(err)
          })
      })
    },
  }
}
