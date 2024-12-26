<template>
  <treeselect
    :name="name"
    :multiple="multiple"
    :close-on-select="closeOnSelect"
    :options="options"
    :placeholder="loading ? $t('component.treeselect.text_loading') : placeholder"
    :disabled="disabled || loading"
    :disable-branch-nodes="true"
    :show-count="showCount"
    :default-expand-level="defaultExpandLevel"
    v-model="value"
  />
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
  name: 'TreeSelectPolygonAreas',
  components: { Treeselect },
  props: {
    name: { type: String, required: true },
    disabled: {type: Boolean},
    fetchUrl: { type: String, required: true },
    selectedOptions: [Number, String, Array],
    closeOnSelect: { type: Boolean, default: true },
    placeholder: { type: String, default: 'Select options below' },
    showCount: { type: Boolean, default: false },
    multiple: { type: Boolean, default: false },
    defaultExpandLevel: { type: Number, default: 0 },
  },
  data () {
    return {
      options: [],
      zones: [],
      polygons: [],
      chunk: 10000,
      loading: false,
      value: null,
    }
  },
  mounted() {
    this.fetchData()
  },
  methods: {
    async fetchData() {
      this.loading = true;

      // load the zones first
      const { data } = await axios.get(`${this.fetchUrl}?type=zone`);
      this.zones = data;

      await this.fetchPolygons();
    },
    async fetchPolygons(page = 1) {
      await axios.get(`${this.fetchUrl}?type=polygon&page=${page}&per_page=${this.chunk}`).then(response => {
        if (response.data.length) {
          this.polygons.push(...response.data);
          this.fetchPolygons(page + 1)
        } else {
          this.options = this.setNestedItems();
          this.polygons = [];
          this.zones = [];
          this.value = this.$props.selectedOptions;
          this.loading = false;
        }
      })
    },
    setNestedItems() {
      const zones = JSON.parse(JSON.stringify(this.zones))
      const polygons = JSON.parse(JSON.stringify(this.polygons))

      const zoneMap = {};
      const nestedPolygons = this.buildNestedPolygons(polygons)

      // Initialize the map and assign an empty children array to each zone
      for (let i = 0; i < zones.length; i++) {
        const zonePolygons = nestedPolygons.filter(p => p.zone_id === zones[i].id);

        zoneMap[zones[i].id] = { ...zones[i], children: zonePolygons};
      }

      // Create the nested structure
      const nestedItems = [];
      for (let i = 0; i < zones.length; i++) {
        if (zones[i].parent_id === null) {
          nestedItems.push(zoneMap[zones[i].id]);
        } else {
          zoneMap[zones[i].parent_id].children.push(zoneMap[zones[i].id]);
        }
      }

      return nestedItems;
    },

    // Function to build nested structure from flat list of polygons
    buildNestedPolygons(polygons) {
      const polygonMap = {};

      // Initialize the map and assign an empty children array to each polygon
      for (let i = 0; i < polygons.length; i++) {
        polygonMap[polygons[i].id] = { ...polygons[i], children: [] };
      }

      // Create the nested structure
      const nestedPolygons = [];
      for (let i = 0; i < polygons.length; i++) {
        if (polygons[i].parent_id === null) {
          nestedPolygons.push(polygonMap[polygons[i].id]);
        } else {
          polygonMap[polygons[i].parent_id].children.push(polygonMap[polygons[i].id]);
        }
      }

      return nestedPolygons;
    },
  }
}
</script>
