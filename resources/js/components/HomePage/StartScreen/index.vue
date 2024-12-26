<template>
  <div class="py-3">
    <div class="row">
      <div class="col-12">
        <div class="text-right mb-3">
          <a href="javascript:;" @click="goToMap()" class="go-to-map">
            {{ $t('home.start_screen.btn_go_to_map') }}
          </a>
        </div>
        <h2 class="text-center mb-4 mb-md-5">
          {{ $t('home.start_screen.title') }}
        </h2>
      </div>
    </div>

    <div class="row" v-if="isLoading">
      <div class="col-12">
        <div class="loading-state">
          <div class="lds-ripple">
            <div></div>
            <div></div>
          </div>
        </div>
      </div>
    </div>

    <div class="container grid-container" v-else>
      <div class="row" v-if="polygons.length > 0">
        <div v-for="item in polygons" :key="item.id" class="col-md-3 col-6">
          <div class="polygon-item" :class="{ 'no-pic': !item.featured_image_url }" @click="goToMap(item.id, item.title)">
            <template v-if="item.featured_image_url">
              <img :src="item.featured_image_url" />
              <div class="overlay" />
            </template>

            <span>{{ item.title }}</span>
          </div>
        </div>
      </div>

      <infinite-loading @distance="10" @infinite="infiniteHandler">
        <div slot="spinner">
          {{ $t("home.result.infinite.loading") }}
        </div>
        <div slot="no-more">
          {{ $t("home.result.infinite.no_more") }}
        </div>
        <div slot="no-results">
          {{ $t("home.result.infinite.no_results") }}
        </div>
      </infinite-loading>
    </div>
  </div>
</template>

<script>
import { mapActions } from 'vuex';
import { EventBus } from '../../helpers';

export default {
  data () {
    return {
      isLoading: true,
      polygons: null,
      page: 1,
      lastPage: null,
    }
  },
  methods: {
    ...mapActions('home', ['saveHistory']),

    async goToMap(polygonId, title = '') {
      let IDs = polygonId ? [polygonId] : [];
      let map;

      if (polygonId) {
        const response = await axios.get("/api/polygon/trunk/" + polygonId);
        const coordinates = await axios.get("/api/polygon/coordinates/" + polygonId);
        const { min_lat, min_lng, max_lat, max_lng } = coordinates.data || {};

        if (min_lat && min_lng && max_lat && max_lng) {
          map = {
            center: {
              lat: ((+min_lat) + (+max_lat)) / 2,
              lng: ((+min_lng) + (+max_lng)) / 2,
            },
            zoom: 13,
          };
        }
        IDs = response.data.ids.filter((value, index, self) => self.indexOf(value) === index);
      }

      localStorage.setItem('selectedPolygons', JSON.stringify([]));

      this.saveHistory({
        showHomeMap: true,
        showNeighborhoodPanel: !!polygonId,
        toggles: {
          showInstructions: !!polygonId,
          activeNeighborhood: title,
        },
        map,
      });

      this.$emit('selected', polygonId);
    },
    async loadPolygons(nextPage = false, infiniteState) {
      if (this.polygons === null) this.polygons = {};

      try {
        let page = nextPage ? this.page + 1 : 1;

        if (!nextPage) {
          this.isLoading = true;
          this.polygons = null;
        }

        await axios
          .get(`/api/home-grid/list?page=${page}&per_page=16`)
          .then(({ data }) => {
            if (nextPage) {
              data.data.forEach((item) => this.polygons.push(item));
              this.page += 1;

              if (this.page >= data.last_page) {
                this.page = data.last_page;
                infiniteState.complete();
              } else {
                infiniteState.loaded();
              }
            } else {
              this.polygons = data.data;
            }

            this.lastPage = data.last_page;
            this.isLoading = false;
            EventBus.$emit('gridUpdated', { data: { ...this.polygons } });
          });
      } catch (err) {
        console.error(err);
        this.errorMsg = getErrorMessage(err);
        this.isLoading = false;
      }

      if (!nextPage) this.page = 1;
    },
    infiniteHandler($state) {
      if (this.page > this.lastPage) {
        return;
      }
      this.loadPolygons(true, $state);
    },
  },
  created() {
    this.loadPolygons().then(() => {
      this.isLoading = false;
    });
  },
}
</script>

<style lang="scss" scoped>
.go-to-map {
  font-size: 0.9rem;
  color: #012e55;
  transition: 250ms;

  &:hover {
    text-decoration: none;
    color: lighten($color: #012e55, $amount: 10%);
  }
}

h2 {
  font-size: 2.25rem;
  color: #012e55;
}
.polygon-item {
  position: relative;
  display: flex;
  border-radius: 6px;
  background-color: #012e55;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 15px;
  margin-bottom: 30px;
  transition: 250ms;
  cursor: pointer;
  overflow: hidden;

  img {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    max-width: 100%;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
  }

  .overlay {
    position: absolute;
    z-index: 2;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    height: 100%;
    background-color: #212019;
    opacity: 0.25;
    transition: 250ms;
  }

  span {
    position: relative;
    z-index: 3;
    color: #ffffff;
    font-weight: 700;
    font-size: 1.5rem;
  }

  &::before {
    content: "";
    width: 1px;
    margin-left: -1px;
    float: left;
    height: 0;
    padding-top: 100%;
  }

  &::after {
    /* to clear float */
    content: "";
    display: table;
    clear: both;
  }

  &.no-pic:hover {
    background-color: lighten(#012e55, 10%);
    text-decoration: none;
  }

  &:not(.no-pic):hover .overlay {
    opacity: 0;
  }
}

@media (min-width: 1400px) {
  .container.grid-container {
    max-width: 1340px;
  }
}
</style>
