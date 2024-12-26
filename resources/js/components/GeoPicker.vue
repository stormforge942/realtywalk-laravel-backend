<template>
  <div class="mb-3">
    <div :class="mapRowClass">
      <div class="row">
        <label for="geo-picker-map">
          {{ $t('component.geopicker.help') }}
        </label>
        <div id="geo-picker-map" class="map mb-3"></div>
      </div>
    </div>
    <div :class="inputRowClass">
      <div class="row">
        <!-- Lat Field -->
        <div class="col-6 pl-0">
          <label for="lat">
            {{ $t('component.geopicker.form.labels.lat') }}
          </label>

          <input-mask
            name="lat"
            mask="
              'alias'             : 'numeric',
              'digits'            : 7,
              'autoGroup'         : true,
              'digitsOptional'    : true,
              'min'               : '-90',
              'max'               : '90',
              'suffix'            : '°',
              'autoUnmask'        : true,
              'removeMaskOnSubmit': true,
              'placeholder'       : '0'
            "
          />
        </div>

        <!-- Lng Field -->
        <div class="col-6 px-0">
          <label for="lng">
            {{ $t('component.geopicker.form.labels.lng') }}
          </label>

          <input-mask
            name="lng"
            mask="
              'alias'             : 'numeric',
              'digits'            : 7,
              'autoGroup'         : true,
              'digitsOptional'    : true,
              'min'               : '-180',
              'max'               : '180',
              'suffix'            : '°',
              'autoUnmask'        : true,
              'removeMaskOnSubmit': true,
              'placeholder'       : '0'
            "
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import "leaflet";
import "leaflet-draw";
import "leaflet-easybutton";
export default {
  name: "GeoPicker",
  props: ["defaultlat", 'defaultlng', 'mapRowClass', 'inputRowClass'],
  data() {
    return {
      map: null,
      marker: null,
      markerMode: false,
      lat: null,
      lng: null
    };
  },
  methods: {
    setMarker(latlng) {
      if (!this.marker) {
        let icon = new L.Icon.Default({
          shadowSize: [0, 0]
        });
        this.marker = L.marker(latlng, {icon}).addTo(this.map);
      }
      this.marker.setLatLng(latlng);
      this.$emit("update", latlng);
    },
    updateLatLngInputs() {
      $('#lat, [name="lat"]').val(this.lat);
      $('#lng, [name="lng"]').val(this.lng);
    },
    updatedInputField() {
      this.lat = $("#lat").val();
      this.lng = $("#lng").val();
      this.setMarker([this.lat, this.lng]);
    }
  },
  mounted() {
    this.lat = this.defaultlat;
    this.lng = this.defaultlng;
    if (this.lat == '') {
      this.lat = null;
    }
    if (this.lng == '') {
      this.lng = null;
    }

    //no initial points, meaning the array is empty, instantiate a new map
    this.map = L.map("geo-picker-map").setView([this.lat ? this.lat : 29.760427, this.lng ? this.lng : -95.369803], this.lat ? 16 : 13);

    L.tileLayer("https://{s}.tile.osm.org/{z}/{x}/{y}.png", {
      attribution:
        '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(this.map);

    L.easyButton({
      states: [{
        icon: 'fa-map-marker',
        stateName: 'navigate',
        title: 'Add Location Marker',
        onClick: (control) => {
          control.state('add-marker');
          this.map.dragging.disable();
          this.markerMode = true;
        }
      }, {
        stateName: 'add-marker',
        icon: 'fa-arrows',
        title: 'Navigate Map',
        onClick: (control) => {
          control.state('navigate');
          this.map.dragging.enable();
          this.markerMode = false;
        }
      }]
    }).addTo(this.map);

    if (this.lat != null && this.lng != null) {
      this.setMarker([this.lat, this.lng]);
      this.updateLatLngInputs();
    }

    this.map.on('click', (event) => {
      if (this.markerMode) {
        this.setMarker(event.latlng);

        this.lat = ((event.latlng.lat + 90) % 180) - 90;
        this.lng = ((event.latlng.lng + 190) % 360) - 190;
        this.updateLatLngInputs();
      }
    });

    this.$nextTick(() => {
      $("#lat, #lng").on('input change', this.updatedInputField);

      // $("#lat").attr('name', 'lat');
      // $("#lng").attr('name', 'lng');
    })
  },
};
</script>

<style>
#geo-picker-map {
  width: 100%;
  height: 600px;
}
#geo-picker-map:not(.leaflet-grab) {
  /* Display crosshair in marker mode*/
  cursor: crosshair;
}
.leaflet-bar .easy-button-button {
  border: none;
  width: 30px;
  height: 30px;
  line-height: 4px;
}
.leaflet-bar .easy-button-button .fa {
  font-size: 14px;
}
</style>
