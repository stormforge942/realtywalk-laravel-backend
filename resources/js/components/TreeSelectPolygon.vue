<template>
  <div>
    <!-- Zone Field -->
    <div class="form-group col-md-6">
      <label for="zone_id">Zone:</label>

      <treeselect
        name="zone_id"
        v-model="zone_id"
        :close-on-select="true"
        :options="zoneOptions"
        :placeholder="selectZoneDisabled ? $t('component.treeselect.text_loading') : 'Select the zone'"
        :disabled="selectZoneDisabled"
        :disable-branch-nodes="true"
        @input="selectZone"
      />
    </div>

    <!-- Parent Field -->
    <div class="form-group col-md-6">
      <label for="parent_id">Parent:</label>

      <treeselect
        name="parent_id"
        v-model="parent_id"
        :close-on-select="true"
        :options="parentOptions"
        :placeholder="zone_id === null || zone_id === undefined
          ? 'Choose the zone to load the parent data'
          : placeholderText"
        :disabled="parentDisabled"
      />
    </div>
  </div>
</template>

<script>
import Treeselect from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
  name: 'TreeSelectPolygon',
  components: { Treeselect },
  props: {
    fetchZoneUrl: { type: String, required: true },
    selectedZone: [Number, String],
    fetchParentUrl: { type: String, required: true },
    selectedParent: [Number, String],
  },
  data () {
    return {
      selectZoneDisabled: true,
      zoneOptions: [],
      zone_id: null,
      parentOptions: [],
      parentDisabled: true,
      parent_id: null,
    }
  },
  computed: {
    placeholderText() {
      return this.parentDisabled
        ? (this.isLevel1() ? 'Parent is disabled for Level 1 Polygons' : 'Loading the data...')
        : 'Select the parent. Leave it empty for automatic';
    },
  },
  mounted () {
    this.fetchZoneData(this.fetchZoneUrl)

    if (this.selectedParent) {
      let url = this.fetchParentUrl +'/?zone_id='+ this.selectedZone

      this.fetchParentData(url)
    }

    document.getElementById('zoom').addEventListener('change', () => {
      this.parentDisabled = this.isLevel1()
      this.parent_id = null
    })
  },
  methods: {
    isLevel1() {
      const zoom = document.getElementById('zoom');

      return zoom && zoom.value == 1;
    },
    selectZone(value, instanceId) {
      if (value) {
        let url = this.$props.fetchParentUrl +'/?zone_id='+ value

        this.fetchParentData(url)
      } else {
        this.resetParent()
      }
    },
    fetchZoneData(url) {
      this.selectZoneDisabled = true

      axios.get(url)
        .then(response => {
          this.zoneOptions = response.data
          this.zone_id = this.$props.selectedZone
          this.selectZoneDisabled = false
        })
    },
    fetchParentData(url) {
      this.resetParent()

      axios.get(url)
        .then(response => {
          this.parentOptions  = response.data
          this.parent_id      = this.$props.selectedParent
          this.parentDisabled = false
          this.parentDisabled = this.isLevel1()
        })
    },
    resetParent() {
      this.parentOptions  = []
      this.parent_id      = null
      this.parentDisabled = true
    }
  }
}
</script>
