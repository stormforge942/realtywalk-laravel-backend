<template>
  <div>
    <label class="mt-3" v-if="disableLabel === undefined">Map:</label>
    <div id="poly-map" class="map"></div>
    <input name="geoJson" type="hidden" v-model="geoJson" />
  </div>
</template>

<script>
import _ from 'lodash';
import "leaflet";
import "leaflet-draw";
import "leaflet.markercluster";
import "leaflet.fullscreen";

export default {
  name: "PolyMap",
  props: ["initialGeoJson", "polygonId", "showNeighbors", "initialZoom", "disableTools", "disableLabel", "disableLinks", "clickableNeighbors", "fullScreen"],
  data() {
    return {
      points: [],
      map: null,
      drawLayer: null,
      drawControl: null,
      zoom: null,
      loadedPolygonIds: [],
      loadedPolygons: [],
      neighboringGeoJson: "",
      geoJson: "",
    };
  },
  methods: {
    updategeoJson() {
      let polygons = [];
      let originalGeoJson = this.drawLayer.toGeoJSON();
      originalGeoJson.features.forEach((layer) => {
        if (!layer.geometry) {
            console.log('skipping as it has no geometry', layer);
          return;
        }
        if (layer.geometry.type == "Polygon") {
          polygons.push(layer.geometry.coordinates);
        } else if (layer.geometry.type == "MultiPolygon") {
          layer.geometry.coordinates.forEach((poly) => polygons.push(poly));
        }
      });
      this.geoJson = JSON.stringify({
        type: "MultiPolygon",
        coordinates: polygons,
      });
    },
    initLoadNeighboringPolygons() {
      const zoomSelect = document.getElementById("zoom");

      if (zoomSelect) {
        zoomSelect.addEventListener('change', this.onZoomChange)
      }

      this.zoom = this.initialZoom || zoomSelect.value;

      if (this.polygonId) {
        this.loadedPolygonIds.push(this.polygonId);
      }

      if (this.zoom) {
        this.loadPolygonsinViewport();
      }

      this.map.addEventListener('zoomend', _.debounce(this.loadPolygonsinViewport, 300));
      this.map.addEventListener('moveend', _.debounce(this.loadPolygonsinViewport, 300));
    },
    loadPolygonsinViewport() {
      const bounds = this.map.getBounds();

      if (this.showNeighbors) {
        axios
          .post("/api/polygons/list-points", {
            bounds: [bounds._southWest.lat, bounds._northEast.lng, bounds._northEast.lat, bounds._southWest.lng],
            excludeList: this.loadedPolygonIds,
            zoom: this.zoom,
          })
          .then((response) => {
            response.data.forEach(geoData => {
              const geometry = JSON.parse(geoData.geometry);
              const feature = L.geoJSON(geometry, {
                style: feature => ({color: '#888888'})
              }).on('click', () => {
                if (this.disableLinks === undefined || this.disableLinks === false) {
                  window.location.href = geoData.page_url;
                }
              }).on('mouseover', function () {
                this.setStyle({color: '#dddddd'});
              }).on('mouseout', function () {
                this.resetStyle();
              }).bindTooltip(function () {
                return geoData.title;
              }).addTo(this.map);

              this.loadedPolygons.push(feature);
              this.loadedPolygonIds.push(geoData.id);
            });
          })
          .catch((err) => {
            throw err;
        });
      }
    },
    onZoomChange(event) {
      this.zoom = event.target.value;
      this.loadedPolygonIds = [];
      this.loadedPolygons.forEach(feature => {
        this.map.removeLayer(feature);
      });
      this.loadPolygonsinViewport();
    }
  },
  mounted() {
    //no initial points, meaning the array is empty, instantiate a new map
    const options = this.fullScreen === undefined ? {} : {
	  fullscreenControl: true,
	  fullscreenControlOptions: {
		position: 'topleft'
	  },
    };
    this.map = L.map("poly-map", options).setView([29.760427, -95.369803], 13);

    L.tileLayer("http://{s}.tile.osm.org/{z}/{x}/{y}.png", {
      attribution:
        '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(this.map);

    this.drawLayer = new L.featureGroup().addTo(this.map);

    if (this.disableTools === undefined) {
      this.drawControl = new L.Control.Draw({
        draw: {
          polygon: {
            allowIntersection: false, // Restricts shapes to simple polygons
            drawError: {
              color: "#e1e100", // Color the shape will turn when intersects
              message: "<strong>Oh snap!<strong> you can't draw that!", // Message that will show when intersect
            },
            shapeOptions: {
              color: "red",
            },
          },
          polyline: false,
          circle: false,
          circlemarker: false,
          marker: false,
          rectangle: false,
        },
        edit: {
          featureGroup: this.drawLayer,
        },
      });
      this.map.addControl(this.drawControl);

      this.map.on("draw:created", (e) => {
        this.drawLayer.addLayer(e.layer);
        this.updategeoJson();
      });

      this.map.on("draw:edited", (e) => {
        this.updategeoJson();
      });

      this.map.on("draw:deleted", (e) => {
        this.updategeoJson();
      });
    }

    let geoJson = {'type': 'FeatureCollection', 'features': []};
    let geoJsonRaw = this.initialGeoJson;
    if (geoJsonRaw) {
      if (typeof geoJsonRaw != 'object') {
        geoJsonRaw = JSON.parse(geoJsonRaw);
      }
      geoJsonRaw.coordinates.forEach(coords => geoJson.features.push({
          type: 'Feature', geometry: {'type': 'Polygon', coordinates: coords}
      }));
      L.geoJson(geoJson, {
        onEachFeature: (feature, layer) => {
            layer.options = layer.options||{};
            this.drawLayer.addLayer(layer);
        },
      });

      this.map.fitBounds(this.drawLayer.getBounds());

      this.updategeoJson();
    }

    if (this.showNeighbors) {
      this.initLoadNeighboringPolygons();
    }
  }
};
</script>

<style>
#poly-map {
  width: 100%;
  height: 60vh;
}
.remove-lats > div > ul {
  display: none;
}
.remove-lats > div {
  margin-bottom: 50px;
}

@media screen and (max-width: 576px) {
  #poly-map {
    height: 50vh;
  }
}
.fullscreen-icon {
	background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 52"><script xmlns="" async="false" type="text/javascript" src="chrome-extension://fnjhmkhhmkbjkkabndcnnogagogbneec/in-page.js"/><script xmlns=""/><path d="M20.6 36.7H16a.9.9 0 0 1-.8-.8v-4.5c0-.2.2-.4.4-.4h1.4c.3 0 .5.2.5.4v3h3c.2 0 .4.2.4.5v1.4c0 .2-.2.4-.4.4zm-9.9-.8v-4.5c0-.2-.2-.4-.4-.4H8.9c-.3 0-.5.2-.5.4v3h-3c-.2 0-.4.2-.4.5v1.4c0 .2.2.4.4.4H10c.4 0 .8-.4.8-.8zm0 10.7V42c0-.4-.4-.8-.8-.8H5.4c-.2 0-.4.2-.4.4v1.4c0 .3.2.5.4.5h3v3c0 .2.2.4.5.4h1.4c.2 0 .4-.2.4-.4zm6.9 0v-3h3c.2 0 .4-.2.4-.5v-1.4c0-.2-.2-.4-.4-.4H16c-.4 0-.8.4-.8.8v4.5c0 .2.2.4.4.4h1.4c.3 0 .5-.2.5-.4zM5 10.3V5.9c0-.5.4-.9.9-.9h4.4c.2 0 .4.2.4.4V7c0 .2-.2.4-.4.4h-3v3c0 .2-.2.4-.4.4H5.4a.4.4 0 0 1-.4-.4zm10.3-4.9V7c0 .2.2.4.4.4h3v3c0 .2.2.4.4.4h1.5c.2 0 .4-.2.4-.4V5.9c0-.5-.4-.9-.9-.9h-4.4c-.2 0-.4.2-.4.4zm5.3 9.9H19c-.2 0-.4.2-.4.4v3h-3c-.2 0-.4.2-.4.4v1.5c0 .2.2.4.4.4h4.4c.5 0 .9-.4.9-.9v-4.4c0-.2-.2-.4-.4-.4zm-9.9 5.3V19c0-.2-.2-.4-.4-.4h-3v-3c0-.2-.2-.4-.4-.4H5.4c-.2 0-.4.2-.4.4v4.4c0 .5.4.9.9.9h4.4c.2 0 .4-.2.4-.4z" fill="currentColor"/><script xmlns=""/></svg>');
	background-size: 26px 52px;
}

.fullscreen-icon.leaflet-fullscreen-on {
	background-position: 0 -26px;
}

.leaflet-touch .fullscreen-icon {
	background-position: 2px 2px;
}

.leaflet-touch .fullscreen-icon.leaflet-fullscreen-on {
	background-position: 2px -24px;
}

/* Safari still needs this vendor-prefix: https://caniuse.com/mdn-css_selectors_fullscreen */
/* stylelint-disable-next-line selector-no-vendor-prefix */
.leaflet-container:-webkit-full-screen {
	width: 100% !important;
	height: 100% !important;
	z-index: 99999;
}

.leaflet-container:fullscreen {
	width: 100% !important;
	height: 100% !important;
	z-index: 99999;
}

.leaflet-pseudo-fullscreen {
	position: fixed !important;
	width: 100% !important;
	height: 100% !important;
	top: 0 !important;
	left: 0 !important;
	z-index: 99999;
}
</style>
