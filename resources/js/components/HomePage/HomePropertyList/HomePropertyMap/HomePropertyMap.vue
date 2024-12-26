<template>
  <div class="wrapper-map">
    <div class="loading-state" v-if="loading || geometry_loading || markers_loading">
      <div class="lds-ripple">
        <div></div>
        <div></div>
      </div>
    </div>

    <div id="results-map"></div>

    <div class="reloading-state" :class="{ active: reloading }">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
        <g stroke="currentColor">
          <circle cx="12" cy="12" r="9.5" fill="none" stroke-linecap="round" stroke-width="3">
            <animate attributeName="stroke-dasharray" calcMode="spline" dur="1.5s" keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" keyTimes="0;0.475;0.95;1" repeatCount="indefinite" values="0 150;42 150;42 150;42 150"/>
            <animate attributeName="stroke-dashoffset" calcMode="spline" dur="1.5s" keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" keyTimes="0;0.475;0.95;1" repeatCount="indefinite" values="0;-16;-59;-59"/>
          </circle>
          <animateTransform attributeName="transform" dur="2s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/>
        </g>
      </svg>

      <span class="ml-2">Applying</span>
    </div>
  </div>
</template>

<script>
import _ from 'lodash';
import "leaflet";
import "leaflet-draw";
import "leaflet/dist/leaflet.css";
import { mapActions, mapState } from 'vuex';

export default {
  name: "HomePropertyMap",
  props: {
    loading: {
      type: Boolean,
      default: true,
    },
    reloading: {
      type: Boolean,
      default: false,
    },
    properties: {
      type: Array,
      default: () => [],
    },
    hasPolygons: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      markers: undefined,
      geoJsonLayer: undefined,
      popup: null,
      geometry_loading: false,
      markers_loading: true,
      map: undefined
    };
  },
  watch: {
    loading: function (val) {
      if (!val) {
        this.addPropertyMarkers();
      }
    },
    reloading: function (val) {
      if (!this.loading && !val) {
        this.addPropertyMarkers();
      }
    },
  },
  computed: {
    ...mapState('home', { homeData: state => state.data }),
  },
  methods: {
    ...mapActions('home', ['saveHistory']),
    formatPrice: function (property) {
      if (
        property &&
        property.pfi &&
        property.pf !== null
      ) {
        if (property.pfi === 2 && property.pt !== null) {
          return `$${property.pf.toLocaleString()} - $${property.pt.toLocaleString()}`;
        } else if (property.pfi === 1) {
          return `$${property.pf.toLocaleString()}`;
        }
      }

      return this.$t("property.list_details.to_be_determined_abbr"); //price_format_id === 3 or bad data
    },
    addPropertyMarkers() {
      if (this.markers === undefined) {
        this.markers = L.layerGroup().addTo(this.map);
      }

      this.markers.clearLayers();

      if (!this.properties.length) {
        this.markers_loading = false;
        return;
      }

      // console.time("Plot to map time")
      this.markers_loading = true;
      const siteUrl = window.location.origin;
      const iconSettings = {
        mapIconUrl: '<svg data-name="Home Icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20.26 25" class="property-map-marker"><polygon class="polygon-home" points="0.25 0.25 20.01 0.25 20.01 14.66 10.45 24.65 0.25 14.66 0.25 0.25" style="stroke:#fff;stroke-miterlimit:10;stroke-width:0.5px"/><polygon points="15.31 7.86 15.31 3.62 12.18 3.62 12.18 4.71 10.12 2.64 2.06 10.66 4.06 10.66 4.06 15.63 7.47 15.63 7.47 10.66 12.46 10.66 12.46 15.63 16.07 15.63 16.07 10.61 18.04 10.61 15.31 7.86" style="fill:#fff"/></svg>',
        mapIconColor: '#cc756b',
        mapIconColorInnerCircle: '#fff',
        pinInnerCircleRadius: 48
      }
      const icon = new L.divIcon({
        html: L.Util.template(iconSettings.mapIconUrl, iconSettings),
        iconSize: [20, 14],
        popupAnchor: [0, -15],
      });

      this.properties.forEach((property) => {
        if (!property || !property.lat || !property.lng) return;

        const latlng = [property.lat, property.lng];
        const marker = L.marker(latlng, { icon });
        const path = `${siteUrl}${property.pu}`;
        const popupContent = `<h5><a href="${path}">${property.fa}</a></h5>${this.formatPrice(property)}`;
        const popup = marker.bindPopup(popupContent).openPopup();

        marker.on('mouseover', function () {
          popup.openPopup();
        });

        marker.on('popupopen', function () {
          const element = this.getElement();
          if (element) {
            element.classList.add('active');
          }
        });

        marker.on('popupclose', function () {
          const element = this.getElement();
          if (element) {
            element.classList.remove('active');
          }
        });

        marker.on("click", () => {
          if (!path) {
            popup.openPopup();
            return;
          }
          window.open(path, "_blank").focus();
        });

        this.markers.addLayer(marker);
      });

      this.map.addEventListener('zoomend', _.debounce(this.setLatestPosition, 500));
      this.map.addEventListener('moveend', _.debounce(this.setLatestPosition, 500));
      this.markers_loading = false;
      // console.timeEnd("Plot to map time")
    },
    setLatestPosition(e) {
      this.saveHistory({ property: {
        map: {
          center: e.target.getCenter(),
          zoom: e.target.getZoom()
        }
      }});
    },
    resetPosition() {
      this.saveHistory({ property: {
        map: {
          center: {
            lat: 29.760427,
            lng: -95.369803
          },
          zoom: 10,
        }
      }})
    },
    async setGeoJson() {
      this.geometry_loading = true;
      const ids = this.homeData.property.map.selected_polygons;

      if (this.geoJsonLayer !== undefined) {
        this.geoJsonLayer.clearLayers();
      }

      if (ids === null || ids === undefined || (Array.isArray(ids) && ids.length === 0)) {
        this.geometry_loading = false;
        this.drawPolygons();
        return;
      }

      try {
        const { data } = await axios.post(`/api/polygons/geometry`, { ids });
        this.drawPolygons(data);
      } catch (err) {
        console.error(err)
      } finally {
        this.geometry_loading = false;
      }
    },
    drawPolygons(geometry_list = []) {
      const validToParse = str => {
        try { JSON.parse(str); return true; } catch (e) { return false; }
      };

      const mapGeometry = polygon => {
        return {
          type: "Feature",
          geometry: validToParse(polygon.geometry) ? JSON.parse(polygon.geometry) : polygon.geometry
        }
      };

      const geojsonData = {
        type: 'FeatureCollection',
        features: geometry_list.map(item => mapGeometry(item))
      };

      this.geoJsonLayer = L.geoJSON(geojsonData, {
        style: feature => ({
          color: '#60a4df',
          weight: 3,
          fillOpacity: .4
        })
      }).addTo(this.map);

      if (this.geoJsonLayer.getBounds().isValid()) {
        this.map.fitBounds(this.geoJsonLayer.getBounds(), {
          padding: [20, 20],
          maxZoom: 15
        });
      } else {
        this.resetPosition();
        const propertyMap = this.homeData.property.map;
        const centerLat = typeof propertyMap.center.lat === 'function' ? propertyMap.center.lat() : propertyMap.center.lat;
        const centerLng = typeof propertyMap.center.lng === 'function' ? propertyMap.center.lng() : propertyMap.center.lng;
        const center = [centerLat, centerLng];
        this.map.setView(center, propertyMap.zoom);
      }
    },
    getMapInfo() {
      const bounds = this.map.getBounds().pad(0.05);
      const southWest = bounds.getSouthWest();
      const northEast = bounds.getNorthEast();

      return {
        zoom: this.map.getZoom(),
        bounds: {
          min_lat: southWest.lat,
          min_lng: southWest.lng,
          max_lat: northEast.lat,
          max_lng: northEast.lng,
        }
      }
    },
    updateEvent() {
      this.$emit('update', { map_info: this.getMapInfo() });
    },
    resizeLeafletMap() {
      setTimeout(() => {
        if (this.map !== undefined) {
          try { this.map.invalidateSize(); } catch (err) {}
        }
      }, 500);
    },
    initTheMap() {
      const propertyMap = this.homeData.property.map;
      const centerLat = typeof propertyMap.center.lat === 'function' ? propertyMap.center.lat() : propertyMap.center.lat;
      const centerLng = typeof propertyMap.center.lng === 'function' ? propertyMap.center.lng() : propertyMap.center.lng;
      const center = [centerLat, centerLng];
      this.map = L.map('results-map', { preferCanvas: true, renderer: L.canvas() }).setView(center, propertyMap.zoom);

      L.tileLayer("https://{s}.tile.osm.org/{z}/{x}/{y}.png", {
        attribution: '&copy; <a href="https://osm.org/copyright">OpenStreetMap</a> contributors',
      }).addTo(this.map);

      this.setGeoJson();

      setTimeout(this.addPropertyMarkers, 200);

      this.map.addEventListener('zoomend', _.debounce(this.updateEvent, 500));
      this.map.addEventListener('dragend', _.debounce(this.updateEvent, 500));

      this.$emit('init', { map_info: this.getMapInfo() });

      this.resizeLeafletMap();

      window.addEventListener("resize", this.resizeLeafletMap);
    }
  },
  mounted() {
    setTimeout(() => this.initTheMap(), 500);
  },
  destroyed() {
    if (this.map !== undefined) {
      this.map.remove();
    }
  }
};
</script>
<style lang="scss" scoped>
.wrapper-map {
  position: relative;
}
.wrapper-map,
#results-map {
  width: 100%;
  height: 100%;
}
.leaflet-marker-icon {
  height: 45px;
  width: 40px;
}
.loading-state {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
  height: 100%;
  background: rgba(255, 255, 255, 0.85);
  z-index: 1001;
}
.reloading-state {
  display: flex;
  align-items: center;
  padding: 2px 8px;
  background: rgba(255,255,255,0.8);
  color: #012e55;
  position: absolute;
  bottom: 30px;
  left: 50%;
  border-radius: 4px;
  z-index: 999;
  user-select: none;
  pointer-events: none;
  transform: translateX(-50%);
  opacity: 0%;
  visibility: hidden;
  transition-duration: 0s;

  span {
    position: relative;
    top: 1px;
  }

  &.active {
    opacity: 100%;
    visibility: visible;
    transition: all 0.45s ease-out;
  }
}
</style>

<style lang="scss">
#results-map {
  .leaflet-pane.leaflet-map-pane {
    height: 100%;
  }

  .property-map-marker .polygon-home {
    fill: #268541;
    transition: fill .3s ease;
  }

  .active .property-map-marker .polygon-home {
    fill: #012e55;
  }

  .leaflet-div-icon {
    background: transparent;
    border: none;
  }
}
</style>
