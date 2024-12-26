<template>
  <Modal ref="modal" :showCloseBtn="false">
    <template v-slot:body>
      <div class="modal-body">
        <h4 class="mb-4">Terms of Service
          <span style="float:right" class="btn btn-sm" @click="closeModal">
            Close
          </span>
        </h4>

        <div class="loading-state" v-if="loading">
          <div class="lds-ripple">
            <div></div>
            <div></div>
          </div>
        </div>

        <div v-else v-html="content" />
      </div>
    </template>
  </Modal>
</template>

<script>
import Modal from '../../utils/Modal/Modal'

export default {
  name: 'TosModal',
  components: {
    Modal
  },
  data () {
    return {
      loading: false,
      content: null,
    }
  },
  mounted () {
    this.getData()
  },
  methods: {
    closeModal () {
      let element = this.$refs.modal.$el
      $(element).modal("hide")
    },
    getData () {
      this.loading = true
      axios.get('/api/settings?key=terms_of_service').then(({ data }) => {
        this.content = data
        this.loading = false
      })
    }
  },
}
</script>

<style scoped>
.modal-body {
  position: relative;
}
.loading-state {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 100%;
  height: 100%;
}
</style>
