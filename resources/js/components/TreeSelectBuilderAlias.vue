<template>
  <div v-if="!loading">
    <treeselect ref="treeselect" :name="name" :multiple="false" :flat="true" :async="true" :default-options="options"
      :load-options="loadOptions" :placeholder="disabled ? $t('component.treeselect.text_loading') : placeholder"
      :disabled="disabled" v-model="value" @select="onSelect" />

    <div v-if="similars && similars.length" class="mt-2 mb-3">
      Alias by similar
      <ul>
        <li v-for="builder of similars" :key="builder.id">
          <a href @click.prevent="selectSimilar(builder)" :class="{ 'font-weight-bold': value == builder.id }">
            {{ builder.name }} (ID: {{ builder.id }})
          </a>
        </li>
      </ul>
    </div>
  </div>

  <div v-else>
    Please wait...
  </div>
</template>

<script>
import Treeselect from "@riophae/vue-treeselect";
import "@riophae/vue-treeselect/dist/vue-treeselect.css";
import { ASYNC_SEARCH } from "@riophae/vue-treeselect";

export default {
  name: "TreeSelectBuilderAlias",
  components: { Treeselect },
  props: {
    name: { type: String },
    selectedOptions: [Number, String, Array],
    similars: [],
    placeholder: { type: String, default: "Select options below" },
    builderId: [Number, String],
    aliasId: [Number, String]
  },
  data() {
    return {
      loading: true,
      options: [],
      disabled: false,
      value: null,
    };
  },
  methods: {
    loadOptions({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        axios
          .post("/system/builders/similar-to", {
            search: searchQuery,
            exclude_builder_id: this.builderId,
            // builder_id: this.aliasId
          })
          .then(res => {
            const results = res.data.map(builder => ({
              id: builder.id,
              label: `${builder.name} (ID: ${builder.id}) (ALIASES: ${builder.aliases_count})`
            }))

            callback(null, results);
          })
          .catch(error => {
            throw new Error(error)
          });
      }
    },
    onSelect(node, instanceId) {
      if (this.options.find(x => x.id === node.id) === undefined) {
        this.options.push(node);
      }
    },

    getDefaultOptions(id) {
      this.loading = true;
      axios.post("/system/builders/similar-to", { builder_id: id })
        .then(res => {
          // deep clone
          const oldOptions = JSON.parse(JSON.stringify(this.options));
          this.options = res.data.map(builder => ({
            id: builder.id,
            label: `${builder.name} (ID: ${builder.id}) (ALIASES: ${builder.aliases_count})`
          }));

          for (let i = 0; i < oldOptions.length; i++) {
            const option = oldOptions[i];

            if (this.options.filter(o => i.id === option.id).length) {
              continue;
            }

            this.options.push(option);
          }

          this.value = id;
        })
        .catch(error => {
          throw new Error(error)
        })
        .finally(() => this.loading = false);
    },
    selectSimilar(builder) {
      if (this.value === builder.id) return;
      this.getDefaultOptions(builder.id);

      // this.loadOptions({
      //   action: ASYNC_SEARCH,
      //   selectSimilar: true,
      //   searchQuery: builder.name,
      //   callback: (x, options) => {
      //     this.options = options;
      //   }
      // });
    },
  },
  created() {
    if (this.aliasId) {
      this.getDefaultOptions(this.aliasId);
    } else {
      this.loading = false;
    }
  }
};
</script>

<style lang="scss" scoped>
.vue-treeselect::v-deep {

  .vue-treeselect__list-item.vue-treeselect__indent-level-0,
  .vue-treeselect__list-item.vue-treeselect__indent-level-1,
  .vue-treeselect__list-item.vue-treeselect__indent-level-2 {
    >.vue-treeselect__option--disabled .vue-treeselect__label-container {
      color: #3c4b64;
    }

    >.vue-treeselect__option--disabled>.vue-treeselect__label-container>.vue-treeselect__checkbox-container {
      width: 0;

      >.vue-treeselect__checkbox.vue-treeselect__checkbox--unchecked.vue-treeselect__checkbox--disabled {
        display: none;
      }
    }
  }
}
</style>
