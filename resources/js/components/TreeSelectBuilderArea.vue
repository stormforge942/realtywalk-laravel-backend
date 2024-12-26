<template>
  <treeselect
    :name="name"
    :multiple="true"
    :flat="true"
    :options="options"
    :load-options="loadOptions"
    :placeholder="disabled ? $t('component.treeselect.text_loading') : placeholder"
    :disabled="disabled"
    :default-expand-level="1"
    value-consists-of="LEAF_PRIORITY"
    v-model="value"
  />
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'
import { LOAD_CHILDREN_OPTIONS } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
  name: 'TreeSelectBuilderArea',
  components: { Treeselect },
  props: {
    name: { type: String, required: true },
    fetchUrl: { type: String, required: true },
    selectedOptions: [Number, String, Array],
    placeholder: { type: String, default: 'Select options below' },
  },
  data () {
    return {
      options: [],
      disabled: false,
      value: null,
    }
  },
  mounted() {
    return this.fetchData()
  },
  methods: {
    fetchData() {
      this.disabled = true

      axios.get(this.fetchUrl)
        .then(response => {
          this.options  = response.data
          this.value    = this.$props.selectedOptions
          this.disabled = false
        })
    },
    loadOptions ({ action, parentNode, callback }) {
      if (action === LOAD_CHILDREN_OPTIONS) {
        let zone_id = parentNode.zone_id
        let parent_id = parentNode.descendants_count > 0 && parentNode.isPolygon ? parentNode.id : null
        let childrenURL = `${this.fetchUrl}&zone_id=${zone_id}${parent_id ? `&parent_id=${parent_id}` : ``}`

        axios.get(childrenURL)
          .then(response => {
            parentNode.children = response.data
            callback()
          })
          .catch(error => {
            callback(new Error(`Whoops, there is something wrong when loading the polygons.`))
          })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.vue-treeselect::v-deep {
  .vue-treeselect__list-item.vue-treeselect__indent-level-0,
  .vue-treeselect__list-item.vue-treeselect__indent-level-1,
  .vue-treeselect__list-item.vue-treeselect__indent-level-2 {
    > .vue-treeselect__option--disabled .vue-treeselect__label-container {
      color: #3c4b64;
    }

    > .vue-treeselect__option--disabled
      > .vue-treeselect__label-container
      > .vue-treeselect__checkbox-container {
      width: 0;

      > .vue-treeselect__checkbox.vue-treeselect__checkbox--unchecked.vue-treeselect__checkbox--disabled {
        display: none;
      }
    }
  }
}
</style>
