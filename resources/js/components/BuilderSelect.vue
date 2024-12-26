<template>
  <treeselect
    :name="name"
    :async="true"
    :default-options="selectedOption ? true : false"
    :load-options="loadOptions"
    :disabled="disabled"
    :placeholder="disabled ? $t('component.treeselect.text_loading') : 'Select the builder'"
    v-model="value"
  />
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'
import { ASYNC_SEARCH } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
  name: 'BuilderSelect',
  components: { Treeselect },
  props: {
    name: { type: String, required: true },
    fetchUrl: { type: String, required: true },
    selectedOption: [Number, String, Array],
  },
  data () {
    return {
      value: null,
      disabled: this.$props.selectedOption ? true : false,
      url: this.$props.fetchUrl
    }
  },
  methods: {
    loadOptions({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        let selectedOption = this.$props.selectedOption

        let params = searchQuery == ''
          ? '/?id=' + selectedOption
          : '/?q=' + searchQuery

        axios.get(this.url + params)
        .then(response => {
          if (searchQuery == '') {
            this.value = selectedOption
          }

          this.disabled = false

          callback(null, response.data)
        })
        .catch(error => {
          throw new Error(error)
        })
      }
    },
  }
}
</script>
