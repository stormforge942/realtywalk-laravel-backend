export default {
  namespaced: true,
  state: {
    builder: {
      name: null,
      descr: null,
      email: null,
      address1: null,
      address2: null,
      address3: null,
      city: null,
      phone: null,
      website: null,
      logo: null,
      gallery: [],
      builder_areas: [],
    },
    errors: {},
    loading: false,
  },
  getters: {
    getFormData: state => {
      const formData = new FormData();
      for (let key in state.builder) {
        if (Array.isArray(state.builder[key])) {
          for (let i in state.builder[key]) {
            formData.append(key + '[]', state.builder[key][i]);
          }
        } else {
          if (state.builder[key] !== null) {
            formData.append(key, state.builder[key]);
          }
        }
      }
      return formData;
    },
  },
  mutations: {
    RESET_FORM(state) {
      state.errors = {};
      state.loading = false;
    },
    SET_STATE(state, payload) {
      Object.assign(state, { ...payload });
    },
    REMOVE_ERROR(state, payload) {
      delete state.errors[payload];
    }
  },
  actions: {
    getData ({ commit }) {
      commit('SET_STATE', { loading: true });
      return new Promise((resolve, reject) => {
        axios.get('/api/user/builder')
          .then(res => {
            commit('SET_STATE', {
              builder: res.data,
              loading: false
            });
            resolve(res.data);
          })
          .catch(err => reject(err));
      });
    },
    async store ({ commit }, payload) {
      commit('SET_STATE', { loading: true });
      return await new Promise((resolve, reject) => {
        axios.post('/api/user/builder', payload, {
          headers: {
            'content-type': 'multipart/form-data'
          }
        })
          .then(res => {
            commit('SET_STATE', { loading: false });
            resolve(res.data);
          })
          .catch(err => {
            commit('SET_STATE', { loading: false });
            if (err.response.status === 400) {
              commit('SET_STATE', { errors: err.response.data });
            }
            reject(err);
          })
      });
    }
  }
}
