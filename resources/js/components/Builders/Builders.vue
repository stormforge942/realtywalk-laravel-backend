<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <TopNavigation />
      <div id="pagebody">
        <div id="content" class="container">
          <LeftSidebar></LeftSidebar>
          <div id="right">
            <div id="pagehead"></div>

            <div class="container-fluid">
              <div class="selector-in-header">
                <h1>{{ $t('builder.list.searchBy.help') }}</h1>
              </div>

              <h4 v-if="isLoggedIn">
                {{ $t('general.welcome', {user: user.name})  }}
              </h4>

              <div class="search-options">
                <div class="">
                  <a
                    href="#"
                    class="keywords filter-active"
                    @click.prevent="onSearchAll($event)"
                    id="all"
                    >All</a
                  >
                  <a
                    href="#"
                    class="keywords"
                    v-for="(keyword, index) in keywords"
                    :key="index"
                    :id="index"
                    @click.prevent="
                      alphabeticalSearch($event, keyword)
                    "
                    >{{ keyword.toUpperCase() }}</a
                  >
                </div>
                <div>
                  <input
                    class="form-control"
                    type="text"
                    v-model.trim="searchWord"
                    :placeholder="$t('builder.list.searchBy.placeholder')"
                    aria-label="Search"
                    @input="search"
                  />
                </div>
              </div>
              <div class="loading-state" v-if="loading">
                <div class="lds-ripple">
                  <div></div>
                  <div></div>
                </div>
              </div>
              <div v-else>
                <div v-if="builders.data && builders.data.length > 0" class="search-results">
                  <pagination
                    :data="builders"
                    :limit="Number(2)"
                    @pagination-change-page="getResults"
                  ></pagination>
                  <div class="builder-list">
                    <div v-if="loading" class="loading-state">
                      <div class="lds-ripple">
                        <div></div>
                        <div></div>
                      </div>
                    </div>

                    <div v-else v-for="builder in builders.data" :key="builder.id">
                      <div class="accordion" id="builderAccordion">
                        <div class="card">
                          <div id="headingOne">
                            <h3 class="d-flex align-items-center" v-if="builder.name">
                              <button
                                class="btn btn-link btn-collapsable"
                                type="button"
                                data-toggle="collapse"
                                :data-target="'#accordion-target' + builder.id"
                                aria-expanded="true"
                                :aria-controls="'accordion-target' + builder.id"
                                @click="showCollapseIcon(builder.id)"
                                :disabled="builder.properties.length == 0"
                              >
                                <svg v-if="builder.id === rowid && collapsed" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-collapse">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-collapse">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                              </button>
                              <router-link :to="`/builder/${builder.slug}`" class="ml-1">
                                {{ builder.name }}
                              </router-link>
                              <span class="badge ml-3" v-if="builder.zoom" :class="{
                                'badge-warning': builder.zoom == 1,
                                'badge-info': builder.zoom == 2,
                                'badge-primary': builder.zoom == 3
                              }">
                                {{ $t('builder.list.level') }} {{ builder.zoom }}
                              </span>
                              <span class="badge badge-secondary ml-2">
                                {{ $tc('builder.list.n_properties', builder.properties.length, { n: builder.properties.length }) }}
                              </span>
                            </h3>
                            <h3 class="d-flex align-items-center" v-if="builder.title">
                              <button
                                class="btn btn-link btn-collapsable"
                                type="button"
                                data-toggle="collapse"
                                :data-target="'#accordion-target' + builder.id"
                                aria-expanded="true"
                                @click="showCollapseIcon(builder.id)"
                                :disabled="builder.properties.length == 0"
                              >
                                <svg v-if="builder.id === rowid" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-collapse">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="icon-collapse">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                              </button>

                              <router-link :to="`/neighborhood/${builder.slug}`" class="ml-1">
                                {{ builder.title }}
                              </router-link>

                              <span class="badge ml-3" v-if="builder.zoom" :class="{
                                'badge-warning': builder.zoom == 1,
                                'badge-info': builder.zoom == 2,
                                'badge-primary': builder.zoom == 3
                              }">
                                {{ $t('builder.list.level') }} {{ builder.zoom }}
                              </span>
                              <span class="badge badge-secondary ml-2">
                                {{ $tc('builder.list.n_properties', builder.properties.length, { n: builder.properties.length }) }}
                              </span>
                            </h3>
                          </div>
                          <div v-if="builder.properties.length > 0" v-show="rowid === builder.id">
                            <div class="card-body">
                              <div>
                                <p
                                  v-for="property in currentPropertyData(builder)"
                                  :key="property.id"
                                  class="property-link"
                                >
                                  <router-link v-if="property.alt_path_url" :to="property.alt_path_url">
                                    {{ property.full_address }}
                                  </router-link>

                                  <a v-else href="javascript:void(0)">
                                    {{ property.full_address }}
                                  </a>
                                </p>
                              </div>

                              <vs-pagination
                                :total-pages="currentPropertyPageSize(builder)"
                                :current-page="builder.properties_pagination.page"
                                @change="changePropertyPage($event, builder)"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <pagination
                    :data="builders"
                    :limit="Number(2)"
                    @pagination-change-page="getResults"
                  ></pagination>
                  <div class="p-3"></div>
                </div>
                <div v-else>
                  <h2 v-html="$t('builder.list.not_found')"></h2>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </PrimaryLayout>
</template>

<script>
import PrimaryLayout from "../layout/PrimaryLayout/PrimaryLayout";
import LeftSidebar from "../layout/LeftSidebar";
import TopNavigation from '../layout/TopNavigation';
import VsPagination from "@vuesimple/vs-pagination";
import _ from 'lodash';

export default {
  name: "Builders",
  components: {
    LeftSidebar,
    PrimaryLayout,
    TopNavigation,
    VsPagination,
  },
  data() {
    return {
      builders: {},
      searchWord: "",
      search: null,
      tempData: "",
      loading: false,
      searchError: false,
      searchBy: "",
      searchPlaceholder: "",
      builder: "",
      displayTemplate: [],
      keywords: [
        "#",
        "a",
        "b",
        "c",
        "d",
        "e",
        "f",
        "g",
        "h",
        "i",
        "j",
        "k",
        "l",
        "m",
        "n",
        "o",
        "p",
        "q",
        "r",
        "s",
        "t",
        "u",
        "v",
        "w",
        "x",
        "y",
        "z"
      ],
      collapsed: false,
      rowid: ""
    };
  },
  computed: {
    isLoggedIn() {
      return this.$store.state.auth.status.loggedIn;
    },
    user() {
      return this.$store.state.auth.user;
    }
  },
  methods: {
    onSearchAll(e) {
      this.loadNeighborhood();
      this.onSearchByChangeFilterToggle();
    },
    alphabeticalSearchFilterToggle(e) {
      let letters = document.querySelectorAll(".keywords");
      letters.forEach(l => {
        if (l.classList.contains("filter-active")) {
          l.classList.remove("filter-active");
        }
      });
      let correctChild = parseInt(e.target.id) + 1;
      letters[correctChild].classList.add("filter-active");
    },
    onSearchByChangeFilterToggle() {
      let letters = document.querySelectorAll(".keywords");
      letters.forEach(l => {
        if (l.classList.contains("filter-active")) {
          l.classList.remove("filter-active");
        }
      });

      document.querySelector("#all").classList.add("filter-active");
    },
    searchTextFilterToggle() {
      let letters = document.querySelectorAll(".keywords");
      letters.forEach(l => {
        if (l.classList.contains("filter-active")) {
          l.classList.remove("filter-active");
        }
      });
    },
    addAllFilter() {
      document.querySelector("#all").classList.add("filter-active");
    },
    getResults(page = 1) {
      this.searchError = false;
      this.loading = true;
      let url = new URL(this.builders.path);
      let path = url.pathname;
      axios.get(path + "?page=" + page).then(response => {
        this.builders = response.data;
        this.loading = false;
      });
    },
    loadNeighborhood() {
      this.$Progress.start();
      this.loading = true;
      axios.get("/api/neighbors/list").then(data => {
        this.builders = data.data;
        this.loading = false;
        this.$Progress.finish();
        this.pageHeader = "Properties by Neighborhood";

        this.addAllFilter();
      });
    },
    alphabeticalSearch(e, keyword) {
      this.alphabeticalSearchFilterToggle(e);
      this.searchNeighborhood(keyword);
    },
    searchNeighborhood(keyword) {
      this.searchError = false;
      this.searchWord = "";
      this.loading = true;
      if (keyword == "#") keyword = "numeric";
      axios.get("/api/neighborhood/search/" + keyword).then(data => {
        this.builders = data.data;
        this.loading = false;
        this.pageHeader = "Properties by Neighborhood";
      });
    },
    searchNeighborhoodText() {
      this.searchError = false;
      this.loading = true;

      axios
        .get("/api/neighborhood/searchText/" + this.searchWord)
        .then(data => {
          this.builders = data.data;
          this.loading = false;
          this.pageHeader = "Properties by Neighborhood";

          this.searchTextFilterToggle();
        })
        .catch(err => {
          this.loading = false;
        });

      if (this.builders.data.length == 0) {
        this.searchError = true;
      }
    },
    showCollapseIcon (id) {
        if (this.rowid === id) {
            return this.rowid = -1
        }
        this.rowid = id
    },
    currentPropertyData (builder) {
      let page = builder.properties_pagination.page;
      let size = 10;
      return builder.properties.slice((page - 1) * size, page * size);
    },
    currentPropertyPageSize (builder) {
      return Math.ceil(builder.properties_pagination.total / 10);
    },
    changePropertyPage (page, builder) {
      if (this.loading) return;
      if (this.currentPropertyPageSize(builder) == 1) return;
      let data = this.builders.data.map(function (item) {
        if (item.id === builder.id) {
          item.properties_pagination.page = page;
        }
        return item;
      });
      this.builders.data = data;
    },
  },
  created() {
    this.search = _.debounce(() => {
      if (this.searchWord != '') {
        this.searchNeighborhoodText();
      } else {
        this.loadNeighborhood();
      }
    }, 500)
  },
  mounted() {
    this.loadNeighborhood();
  }
};
</script>

<style scoped>
.selector-in-header,
.search-options {
  display: flex;
}

.search-options {
  align-items: center;
  justify-content: space-between;
}

.search-options > div {
  margin-bottom: 10px;
}

.search-options > div:first-child {
  margin-right: 10px;
}

.search-properties {
  display: block;
  width: 250px;
  margin-left: 10px;
  margin-top: 12px;
  font-size: 2.5rem;
}
a.keywords {
  padding: 10px 8px;
  color: grey;
  text-decoration: underline;
  display: inline-block;
}
.loading-state {
  padding: 50px 0;
}
.builder-list {
  padding: 2rem 0;
}
#right {
  border-left: none !important;
}

.filter-active {
  border: 1px solid #007bff;
  background: #007bff;
  color: #eee !important;
  border-radius: 2px !important ;
}

.icon-collapse {
  display: block;
  width: 1.5rem;
  height: 1.5rem;
}

.btn-collapsable {
  width: 50px;
}

.btn-collapsable:disabled {
  cursor: default;
  color: transparent;
}

@media screen and (max-width: 870px) {
  .search-options {
    display: block;
  }
}

@media screen and (max-width: 475px) {
  .selector-in-header {
    display: block;
  }

  .selector-in-header .search-properties {
    margin-left: 0px;
    width: 100%;
  }
}

@media screen and (max-width: 991px) {
  #content {
    margin-top: 0;
  }

  #content #right div#pagehead {
    padding: 0;
    background-color: transparent;
  }
  .search-options > div {
    margin-right: 0px;
  }
}
.property-link {
  margin-left: 30px;
}
.badge {
  font-size: 14px;
}
</style>
