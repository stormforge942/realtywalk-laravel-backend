<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <TopNavigation />
      <div id="pagebody">
        <div id="content" class="container">
          <LeftSidebar></LeftSidebar>
          <div id="right" class="pl-4">
            <div class="card border-0">
              <div class="card-header">
                <h2 class="card-title m-0 py-2">
                  {{ $t('pages.address_lookup.title') }}
                </h2>
              </div>

              <div class="card-body">
                <div class="loading-state" v-if="loading">
                  <div class="lds-ripple">
                    <div></div>
                    <div></div>
                  </div>
                </div>

                <div v-else>
                  <div v-if="filters.addressQuery" class="row">
                    <div class="col mt-3 mb-4" v-html="$t('pages.address_lookup.result_text', {q: filters.addressQuery})"></div>
                  </div>

                  <div v-if="properties.total > 0" class="row">
                    <div class="col">
                      <div class="d-none d-lg-block wide-results">
                        <HomeSortMenu
                          type="lg"
                          :toggles="sortToggles"
                          :setSort="setSort"
                        ></HomeSortMenu>
                        <HomePropertyListItem
                          v-for="item in properties.data"
                          :item="item"
                          :key="`lg-${item.id}`"
                          :isLoggedIn="isLoggedIn"
                        ></HomePropertyListItem>
                      </div>

                      <div class="d-block d-lg-none">
                        <HomeSortMenu
                          type="sm"
                          :toggles="sortToggles"
                          :setSort="setSort"
                        ></HomeSortMenu>
                        <HomePropertyListSm
                          v-for="property in properties.data"
                          :key="`sm-${property.id}`"
                          :property="property"
                          :isLoggedIn="isLoggedIn"
                        ></HomePropertyListSm>
                      </div>

                      <div class="my-3">
                        <pagination :data="properties" :limit="Number(2)" @pagination-change-page="getResults" />
                      </div>
                    </div>
                  </div>

                  <div v-else class="row">
                    <div class="col">
                      No results found
                    </div>
                  </div>
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
import { EventBus } from "@/components/helpers";
import PrimaryLayout from '@/components/layout/PrimaryLayout/PrimaryLayout'
import LeftSidebar from '@/components/layout/LeftSidebar'
import TopNavigation from '@/components/layout/TopNavigation'
import HomePropertyListItem from "@/components/HomePage/HomePropertyList/HomePropertyListItem/HomePropertyListItem";
import HomePropertyListSm from "@/components/HomePage/HomePropertyList/HomePropertyListSm/HomePropertyListSm";
import HomeSortMenu from "@/components/HomePage/HomeSortMenu/HomeSortMenu";

export default {
  components: {
    PrimaryLayout,
    LeftSidebar,
    TopNavigation,
    HomePropertyListItem,
    HomePropertyListSm,
    HomeSortMenu,
  },
  data () {
    return {
      loading: false,
      properties: [],
      filters: {
        sortBy: "",
        sortOrder: "",
        addressQuery: "",
        showAll: true,
      },
      sortToggles: {
        price_from: false,
        address: false,
        neighborhood: false,
      }
    }
  },
  computed: {
    isLoggedIn() {
      return this.$store.state.auth.status.loggedIn;
    },
  },
  created () {
    this.getResults(1);

    EventBus.$on("orderProperties", (column, order) => {
      this.filters.sortBy = column;
      this.filters.sortOrder = order;
      this.getResults(1)
    });
  },
  methods: {
    getResults(page = 1) {
      if (this.$route.query.q) {
        this.filters.addressQuery = this.$route.query.q;
      }

      try {
        this.loading = true;

        axios
          .post(
            `/api/properties/address-lookup?page=${page}&sortBy=${this.filters.sortBy}&orderBy=${this.filters.sortOrder}`,
            this.filters
          )
          .then(({ data }) => {
            this.properties = data;
            this.loading = false;
          });
      } catch (err) {
        this.loading = false;
      }
    },
    setSort(column) {
      Object.keys(this.sortToggles).forEach((key) => {
        if (key !== column) {
          this.sortToggles[key] = false;
        } else {
          this.sortToggles[key] = !this.sortToggles[key];
        }
      });

      return this.sortToggles[column] ? "asc" : "desc";
    },
  }
}
</script>
