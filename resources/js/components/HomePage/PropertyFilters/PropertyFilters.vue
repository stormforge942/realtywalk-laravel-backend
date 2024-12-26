<template>
  <div class="property-filters pl-xl-2 pr-lg-1 mt-2 mt-xl-4">
    <div class="property-filters-container">
      <div class="mb-3 mb-xl-4">
        <div class="polygon-search-wrapper">
          <treeselect class="polygon-search" :async="true" :load-options="loadNeighborhoods" :clearable="false"
            @select="onSelectPolygon" placeholder="Neighborhood Search">
            <template #option-label="{ node }">
              <label>
                <span v-if="node.raw.breadcrumbs.length" style="color: #9ca3af">
                  <span v-html="node.raw.breadcrumbs" /> &rsaquo;
                </span>
                <span style="color: #012e55">{{ node.label }}</span>
              </label>
            </template>
          </treeselect>
        </div>
      </div>

      <div class="form-row mb-1 mb-xl-2">
        <div class="col">
          <div class="form-group">
            <div class="select-wrapper mb-1 mb-xl-3">
              <select name="minimumprice" class="form-control" :disabled="isLoading" v-model="filters.minimumPrice"
                @change="refreshSearch">
                <option :value="null">
                  {{ $t("home.filter.minimum_price") }}
                </option>

                <!-- <option value="1" selected>
                                    ${{ numberWithCommas(1) }}
                                </option> -->

                <option :value="index * 25000" v-for="index in 1000000 / 25000" :key="index * 25000" :disabled="Boolean(filters.maximumPrice) && index * 25000 > filters.maximumPrice
                  ">
                  ${{ numberWithCommas(index * 25000) }}
                </option>
                <option :value="index * 250000 + 1000000" v-for="index in 1500000 / 250000 - 1"
                  :key="index * 250000 + 1000000" :disabled="Boolean(filters.maximumPrice) &&
                    index * 250000 + 1000000 > filters.maximumPrice
                    ">
                  ${{ numberWithCommas(index * 250000 + 1000000) }}
                </option>
                <option value="2500000" :disabled="Boolean(filters.maximumPrice) && 2500000 > filters.maximumPrice">
                  $2,500,000+
                </option>
              </select>
            </div>
            <div class="select-wrapper mb-3">
              <select name="maximumprice" class="form-control" :disabled="isLoading" v-model="filters.maximumPrice"
                @change="refreshSearch">
                <option :value="null">
                  {{ $t("home.filter.maximum_price") }}
                </option>

                <option :value="index * 25000" v-for="index in 1000000 / 25000" :key="index * 25000" :disabled="Boolean(filters.minimumPrice) && index * 25000 < filters.minimumPrice
                  ">
                  ${{ numberWithCommas(index * 25000) }}
                </option>
                <option :value="index * 250000 + 1000000" v-for="index in 1500000 / 250000 - 1"
                  :key="index * 250000 + 1000000" :disabled="Boolean(filters.minimumPrice) &&
                    index * 250000 + 1000000 < filters.minimumPrice
                    ">
                  ${{ numberWithCommas(index * 250000 + 1000000) }}
                </option>
                <option value="2500000">$2,500,000+</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row mb-1 mb-xl-2">
        <div class="col">
          <div class="form-group">
            <label>
              {{ $t("home.filter.criteria.listing_status.label") }}
            </label>
            <div class="select-wrapper">
              <select name="listingStatus" class="form-control" :disabled="isLoading" v-model="filters.listingStatus"
                @change="refreshSearch">
                <option value="any">
                  {{ $t("home.filter.criteria.listing_status.options.any") }}
                </option>
                <option value="Active">
                  {{ $t("home.filter.criteria.listing_status.options.active") }}
                </option>
                <option value="Pending">
                  {{ $t("home.filter.criteria.listing_status.options.pending") }}
                </option>
                <option value="Pending Continue to Show">
                  {{ $t("home.filter.criteria.listing_status.options.pending_continue") }}
                </option>
                <option value="Option Pending">
                  {{ $t("home.filter.criteria.listing_status.options.option_pending") }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row mb-1 mb-xl-2">
        <div class="col">
          <div class="form-group">
            <label>{{ $t("home.filter.criteria.property_type.label") }}</label>
            <div class="select-wrapper">
              <treeselect class="property-type" :options="propertyTypeOptions" :multiple="true"
                v-model="filters.selectedPropertyTypes" @input="refreshSearch" placeholder="All property types">
              </treeselect>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row mb-1 mb-xl-2">
        <div class="col">
          <div class="form-group">
            <label>{{ $t("home.filter.criteria.bedrooms.label") }}</label>
            <div class="select-wrapper mb-1">
              <select class="form-control" :disabled="isLoading" v-model="filters.bedroomsMin" @change="refreshSearch">
                <option value="any">
                  {{ $t("home.filter.criteria.bedrooms.options.any_min") }}
                </option>
                <option v-for="index in 9" :key="index" :value="index">
                  {{
                    $tc("home.filter.criteria.bedrooms.options.bed", index, {
                      n: index,
                    })
                  }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row mb-1 mb-xl-2">
        <div class="col">
          <div class="form-group">
            <label>{{ $t("home.filter.criteria.bathrooms.label") }}</label>
            <div class="select-wrapper mb-1">
              <select class="form-control" :disabled="isLoading" v-model="filters.bathrooms" @change="refreshSearch">
                <option value="any">
                  {{ $t("home.filter.criteria.bathrooms.options.any_full") }}
                </option>
                <option :value="1">
                  {{ $tc("home.filter.criteria.bathrooms.options.full_bathroom", 1, { n: 1 }) }}
                </option>
                <option :value="2">
                  {{ $tc("home.filter.criteria.bathrooms.options.full_bathroom", 2, { n: 2 }) }}
                </option>
                <option :value="3">
                  {{ $tc("home.filter.criteria.bathrooms.options.full_bathroom", 3, { n: 3 }) }}
                </option>
                <option :value="4">
                  {{ $tc("home.filter.criteria.bathrooms.options.full_bathroom", 4, { n: 4 }) }}
                </option>
                <option :value="5">
                  {{ $tc("home.filter.criteria.bathrooms.options.full_bathroom", 5, { n: "5" }) }}
                </option>
              </select>
            </div>
            <div class="select-wrapper">
              <select class="form-control" :disabled="isLoading" v-model="filters.bathroomsHalf" @change="refreshSearch">
                <option value="any">
                  {{ $t("home.filter.criteria.bathrooms.options.any_half") }}
                </option>
                <option :value="1">
                  {{ $tc("home.filter.criteria.bathrooms.options.half_bathroom", 1, { n: 1 }) }}
                </option>
                <option :value="2">
                  {{ $tc("home.filter.criteria.bathrooms.options.half_bathroom", 2, { n: 2 }) }}
                </option>
                <option :value="3">
                  {{ $tc("home.filter.criteria.bathrooms.options.half_bathroom", 3, { n: 3 }) }}
                </option>
                <option :value="4">
                  {{ $tc("home.filter.criteria.bathrooms.options.half_bathroom", 4, { n: 4 }) }}
                </option>
                <option :value="5">
                  {{ $tc("home.filter.criteria.bathrooms.options.half_bathroom", 5, { n: 5 }) }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row mb-1 mb-xl-2">
        <div class="col">
          <div class="form-group">
            <label>{{ $t("home.filter.criteria.square_feet.label") }}</label>

            <div class="select-wrapper mb-1">
              <select class="form-control" :disabled="isLoading" @change="refreshSearch" v-model="filters.squareFeetMin">
                <option value="any">
                  {{ $t("home.filter.criteria.square_feet.options.any_min") }}
                </option>
                <option :value="value" v-for="value in sqrFeetOptions" :key="value" :disabled="Boolean(filters.squareFeetMax) && value > filters.squareFeetMax">
                  {{ numberWithCommas(value) }}
                </option>
              </select>
            </div>

            <div class="select-wrapper">
              <select class="form-control" v-model="filters.squareFeetMax" @change="refreshSearch" :disabled="isLoading">
                <option value="any">
                  {{ $t("home.filter.criteria.square_feet.options.any_max") }}
                </option>

                <option :value="value" v-for="value in sqrFeetOptions" :key="value" :disabled="Boolean(filters.squareFeetMin) && value < filters.squareFeetMin">
                  {{ numberWithCommas(value) }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row mb-1 mb-xl-2">
        <div class="col">
          <div class="form-group mb-0">
            <label>{{ $t("home.filter.criteria.garage_capacity.label") }}</label>
            <div class="select-wrapper mb-0">
              <select class="form-control mb-0" :disabled="isLoading" v-model="filters.garageOptions" @change="refreshSearch">
                <option value="any">
                  {{ $t("home.filter.criteria.garage_capacity.options.any") }}
                </option>
                <option value="1">1+</option>
                <option value="2">2+</option>
                <option value="3">3+</option>
                <option value="4">4+</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="form-row mb-2 mb-xl-3">
        <div class="col">
          <div class="form-group mb-0">
            <label>{{ $t("home.filter.criteria.stories.label") }}</label>
            <div class="select-wrapper mb-1">
              <select class="form-control mb-0" :disabled="isLoading" v-model="filters.storiesMin" @change="refreshSearch">
                <option value="any">
                  {{ $t("home.filter.criteria.stories.options.any_min") }}
                </option>
                <option
                  v-for="n in [1, 2, 3, 4]"
                  :value="n" :key="n"
                  :disabled="Boolean(filters.storiesMax) && n > filters.storiesMax"
                >
                  {{ n }}
                </option>
              </select>
            </div>

            <div class="select-wrapper mb-0">
              <select class="form-control mb-0" :disabled="isLoading" v-model="filters.storiesMax" @change="refreshSearch">
                <option value="any">
                  {{ $t("home.filter.criteria.stories.options.any_max") }}
                </option>
                <option
                  v-for="n in [1, 2, 3, 4]" :value="n" :key="n"
                  :disabled="Boolean(filters.storiesMin) && n < filters.storiesMin"
                >
                  {{ n }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>
      <div class="form-row mb-2 mb-xl-3">
        <div class="col">
          <div class="form-group mb-0">
            <div class="form-check">
              <input type="checkbox" id="hasPool" class="form-check-input" v-model="filters.hasPool" @change="refreshSearch">
              <label for="hasPool" class="form-check-label" style="user-select: none;">
                {{ $t("home.filter.criteria.include_pool.label") }}
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="form-row mb-1 mb-xl-2">
        <div class="col">
          <div class="form-group mb-0">
            <div class="form-check">
              <input type="checkbox" id="hasElevator" class="form-check-input" v-model="filters.hasElevator" @change="refreshSearch">
              <label for="hasElevator" class="form-check-label" style="user-select: none;">
                {{ $t("home.filter.criteria.include_elevator.label") }}
              </label>
            </div>
          </div>
        </div>
      </div>
      <div class="form-row mt-4">
        <div class="col">
          <button type="button" class="btn-criteria-results" @click="showResults()">
            Results
          </button>
        </div>
      </div>

      <div class="form-row mt-4">
        <div class="col text-center">
          <a href="javascript:;" class="text-muted" @click="clearAll">
            <span class="text-capitalize">
              {{ $t("home.map.btn_clear_selected").toLowerCase() }}
            </span>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import _ from 'lodash';
import { EventBus } from "../../helpers";
import { ASYNC_SEARCH } from "@riophae/vue-treeselect";
import Treeselect from "@riophae/vue-treeselect";
import { mapActions, mapState } from 'vuex';

const savableFields = [
  'minimumPrice',
  'maximumPrice',
  'listingStatus',
  'bedroomsMin',
  'bathrooms',
  'bathroomsHalf',
  'squareFeetMin',
  'squareFeetMax',
  'garageOptions',
  'storiesMin',
  'storiesMax',
  'hasPool',
  'hasElevator',
  // 'lotType'
];

export default {
  name: 'PropertyFilters',
  props: ['isLoading'],
  components: {
    Treeselect,
  },
  data() {
    return {
      filters: {},
      propertyTypeOptions: [],
      sqrFeetOptions: [
        // 1, 5, 10, 20, 40, 80, 160, 320,
        500, 750, 1000, 1250, 1500, 1750, 2000, 2250, 2500, 2750, 3000, 3500,
        4000, 5000, 7500,
      ],
    };
  },
  computed: {
    ...mapState('home', {
      homeMaster: state => state.master,
      homeData: state => state.data
    }),
  },
  watch: {
    filters: {
      handler() {
        this.updateFilters();
      },
      deep: true
    }
  },
  methods: {
    ...mapActions('home', ['saveHistory', 'clearFilters']),

    updateFilters: _.debounce(function () {
      this.saveHistory({ filters: this.filters });
    }, 500),
    saveSearch() {
      for (let field of savableFields) {
        localStorage.setItem(field, this[field]);
      }
      localStorage.setItem('selectedPropertyTypes', JSON.stringify(this.selectedPropertyTypes));
    },
    initFilters() {
      Object.keys(this.homeMaster.filters).map(filter => {
        this.filters[filter] = this.homeData.filters[filter] || this.homeMaster.filters[filter];
      });
    },
    clearAll() {
      EventBus.$emit("clearAllFilters");
    },
    refreshSearch() {
      EventBus.$emit("propertiesFilter", this.filters);
    },
    loadNeighborhoods({ action, searchQuery, callback }) {
      if (action === ASYNC_SEARCH) {
        axios
          .get(`/api/neighborhood/searchText/${searchQuery}/true`)
          .then(({ data }) => callback(null, this.mapData(data)))
          .catch(() => callback(new Error("Failed to load search results.")));
      }
    },
    mapData(data) {
      if (!data.length) return [];
      return data.map((obj) => ({ label: obj.title, ...obj }));
    },
    numberWithCommas(x) {
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },
    onSelectPolygon(node, instanceId) {
      EventBus.$emit("searchPolygon", node);
    },
    showResults() {
      EventBus.$emit("searchCriteria");
    },
  },
  created() {
    EventBus.$on('clearAllFilters', () => {
      this.filters = this.homeMaster.filters;
      this.clearFilters();
    });

    EventBus.$on('applySavedSearch', (criteria) => {
      this.filters = {
        ...this.filters,
        ...criteria.homeData.filters,
        polygons: criteria.polygons,
      }
    });

    this.propertyTypeOptions = [{
      id: "Country Homes/Acreage",
      label: this.$t(
        "home.filter.criteria.property_type.options.country_homes"
      ),
    }, {
      id: "Lots",
      label: this.$t(
        "home.filter.criteria.property_type.options.lots"
      )
    }, {
      id: "Mid/Hi-Rise Condo",
      label: this.$t(
        "home.filter.criteria.property_type.options.mid_hi_rise_condo"
      )
    }, {
      id: "Multi-Family",
      label: this.$t(
        "home.filter.criteria.property_type.options.multifamily"
      ),
    }, {
      id: "Neue Wave",
      label: this.$t(
        "home.filter.criteria.property_type.options.neue_wave"
      )
    }, {
      id: "Rental",
      label: this.$t(
        "home.filter.criteria.property_type.options.rental"
      )
    }, {
      id: "Single-Family",
      label: this.$t(
        "home.filter.criteria.property_type.options.single_family"
      ),
    }, {
      id: "Townhouse/Condo",
      label: this.$t(
        "home.filter.criteria.property_type.options.townhouse_condo"
      ),
    }];

    setTimeout(() => {
      this.refreshSearch();
    }, 500);
  },
  destroyed() {
    EventBus.$off('clearAllFilters');
    EventBus.$off('applySavedSearch');
  },
  mounted() {
    this.filters = this.homeData.filters || this.homeMaster.filters;
  },
};
</script>

<style>
.property-filters {
  height: calc(100% - 1.5rem);
  overflow: auto;
  margin-right: -10px;
}

.property-filters::-webkit-scrollbar-track {
  border: 1px solid #fff;
  padding: 2px 0;
  background-color: #fff;
}

.property-filters::-webkit-scrollbar {
  width: 10px;
}

.property-filters::-webkit-scrollbar-thumb {
  border-radius: 10px;
  box-shadow: inset 0 0 6px rgba(255, 255, 255, 0.3);
  background-color: #022E55;
  border: 1px solid #fff;
}

.property-type {
  border-radius: 0;
  border: 2px solid #eee;
}

.property-type .vue-treeselect__control {
  border: 0;
}

.property-filters-container {
  padding-right: 10px;
}

.property-filters option[disabled] {
  background-color: #eeeeee;
}

.polygon-search .vue-treeselect__control {
  background-color: #eeeeee;
  padding: 0.375rem 37px 0.375rem 0.575rem;
  border: 0;
  border-radius: 0;
  outline: none;
  position: relative;
}

.polygon-search .vue-treeselect__input {
  color: #012e55;
}

.polygon-search .vue-treeselect__placeholder {
  color: #012e55;
  opacity: 0.75;
  line-height: 1.5;
}

.polygon-search .vue-treeselect__single-value {
  color: #012e55;
  line-height: 1.5;
}

.polygon-search .vue-treeselect__control-arrow-container {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 32px;
  background-color: #012e55;
}

.polygon-search .vue-treeselect--searchable .vue-treeselect__input-container {
  padding: 0;
}

.polygon-search .vue-treeselect__control-arrow-container:hover,
.polygon-search .vue-treeselect__control-arrow-container .vue-treeselect__control-arrow,
.polygon-search .vue-treeselect__control-arrow-container:hover .vue-treeselect__control-arrow {
  color: #fff !important;
}

.polygon-search .vue-treeselect__menu {
  border-color: #eee;
  border-width: 2px;
}

.polygon-search .vue-treeselect--open-below .vue-treeselect__menu {
  border-radius: 0;
  border: none;
}

.polygon-search .vue-treeselect--open-above .vue-treeselect__menu {
  border-radius: 0;
}

.form-group label {
  font-size: 16px;
  color: #012e55;
}

.form-group .form-control {
  border-radius: 0;
  border: 2px solid #eee;
}

.form-group .form-control:focus {
  outline: 0;
  box-shadow: none;
}

.form-group select.form-control {
  padding-right: 30px;
}

.form-group .select-wrapper {
  position: relative;
}

.form-group .select-wrapper::after {
  content: "\f107";
  font-family: "FontAwesome";
  display: flex;
  justify-content: center;
  align-items: center;
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  width: 25px;
  height: 100%;
  background-color: #fff;
  color: #12253c;
  border: 2px solid #eee;
  border-left: 0;
  font-size: 18px;
  pointer-events: none;
  z-index: 1;
}

.btn-criteria-results {
  background: #012e55;
  color: #fff;
  width: 100%;
  height: 40px;
  border: 0px solid;
  cursor: pointer;
  pointer-events: all;
  transition: background-color 250ms;
}

.btn-criteria-results:hover {
  background: #01325d;
}

@media screen and (max-width: 991px) {
  .property-filters {
    padding: 1.5rem;
  }
}

@media screen and (min-width: 992px) {
  .form-group select.form-control {
    padding: 0.275rem 0.5rem;
  }
}

@media screen and (min-width: 1200px) {
  .property-filters {
    height: calc(100% - 2rem);
  }

  .form-group select.form-control {
    padding-right: 35px;
  }

  .form-group .select-wrapper::after {
    width: 30px;
    font-size: 20px;
  }

  .btn-criteria-results {
    height: 45px;
  }
}

@media screen and (min-width: 1400px) {
  .property-filters {
    height: calc(100% - 3rem);
  }

  .btn-criteria-results {
    height: 55px;
  }
}

@media screen and (max-width: 575px) {
  .property-filters-container {
    padding-right: 0;
  }
}
</style>
