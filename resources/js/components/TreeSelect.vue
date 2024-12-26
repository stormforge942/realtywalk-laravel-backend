<template>
  <treeselect
    :name="name"
    :multiple="multiple"
    :close-on-select="closeOnSelect"
    :options="options"
    :placeholder="loading ? $t('component.treeselect.text_loading') : placeholder"
    :disabled="disabled || loading"
    :disable-branch-nodes="disableBranchNodes"
    :show-count="showCount"
    :default-expand-level="defaultExpandLevel"
    v-model="value"
  />
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
  name: 'TreeSelect',
  components: { Treeselect },
  props: {
    name: { type: String, required: true },
    disabled: {type: Boolean},
    fetchUrl: { type: String, required: true },
    selectedOptions: [Number, String, Array],
    closeOnSelect: { type: Boolean, default: true },
    placeholder: { type: String, default: 'Select options below' },
    disableBranchNodes: { type: Boolean, default: false },
    showCount: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false },
    defaultExpandLevel: { type: Number, default: 0 }
  },
  data () {
    return {
      options: [],
      loading: false,
      value: null,
    }
  },
  mounted() {
    this.fetchData(this.fetchUrl)
  },
  methods: {
    fetchData(url) {
      this.loading = true

      axios.get(url)
        .then(response => {
          this.options  = response.data
          this.value    = this.$props.selectedOptions
          this.loading = false
        })
    },
  }
}
</script>
