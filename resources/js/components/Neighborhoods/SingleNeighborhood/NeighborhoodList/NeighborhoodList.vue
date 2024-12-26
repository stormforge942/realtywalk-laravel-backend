<template>
  <div>
    <div class="loading-state" v-if="loading">
      <div class="lds-ripple">
        <div></div>
        <div></div>
      </div>
    </div>

    <h4 v-if="total > 0 && title" class="mb-4"> {{title}} Neighborhood Properties
      <span style="float:right" class="btn btn-sm" @click="closeModal" :disabled="isProcessing">
        Close
      </span>
    </h4>

    <div v-if="! loading && total === 0" class="d-block py-4 text-center">
      <h4 class="mb-0">
        There is no property found which is associated with <b>{{ title }}</b>.
        <a href="javascript:;" @click="closeModal">Click here</a> to close this modal.
      </h4>
    </div>

    <div class="position-relative">
      <div class="loading-overlay" v-if="fetchNextPage">
        <div class="loading-state">
          <div class="lds-ripple">
            <div></div>
            <div></div>
          </div>
        </div>
      </div>

      <div class="d-none d-lg-block">
        <HomePropertyListItem
          v-for="item in properties"
          :item="item"
          :key="item.id"
          :isLoggedIn="isLoggedIn"
        ></HomePropertyListItem>
      </div>

      <div class="list d-block d-lg-none">
        <HomePropertyListSm
        v-for="favorite in properties"
        :key="favorite.id"
        :property="favorite"
        :isLoggedIn="isLoggedIn"
        ></HomePropertyListSm>
      </div>
    </div>

    <pagination
      class="justify-content-center mt-4"
      v-if="resources"
      :data="resources"
      :limit="Number(2)"
      @pagination-change-page="goToPage"
    ></pagination>
  </div>
</template>

<script>
import { EventBus } from "../../../helpers";
import HomePropertyListItem from "../../../HomePage/HomePropertyList/HomePropertyListItem/HomePropertyListItem";
import HomePropertyListSm from "../../../HomePage/HomePropertyList/HomePropertyListSm/HomePropertyListSm";

  export default {
    name: "NeighborhoodList",
    components: {
      HomePropertyListItem,
      HomePropertyListSm
    },
    data() {
      return {
        title: '',
        payload: '',
        resources: null,
        properties: [],
        favorites: [],
        loading: true,
        polygonSlug: null,
        isProcessing: false,
        fetchNextPage: false,
        total: 0,
        page: 1,
        lastPage: null
      };
    },
    props: {
      neighborhood: {
        type: Object,
        default: null
      },
      closeModal: {
        type: Function
      },
      fetch: {
        type: Boolean,
        default: false
      }
    },
    computed: {
      isLoggedIn() {
        return this.$store.state.auth.status.loggedIn;
      }
    },
    watch: {
      fetch: {
        handler () {
          this.getPolygon()
        }
      }
    },
    methods: {
      goToPage (page) {
        this.getPayload(page)
      },
      getPolygon () {
        axios
          .get(`/api/polygon/${this.$route.params.path}`)
          .then(response => {
            this.title = response.data.title
            this.polygonSlug = response.data.slug
            this.getPayload(1, true)
          })
      },
      getPayload (page = 1, initial = false) {
        if (initial) {
          this.loading = true
        } else {
          this.fetchNextPage = true
        }

        axios
          .get(`/api/polygon/properties/${this.polygonSlug}?page=${page}`)
          .then(response => {
            let data = response.data
            this.resources = data
            this.properties = data.data
            this.page = page
            this.total = data.total
            this.lastPage = data.last_page
            this.loading = false
            this.fetchNextPage = false
            EventBus.$emit("properties", this.total)
          })
          .catch(() => {
            console.log(err);
            this.loading = false
            this.fetchNextPage = false
          });
      },
    },
  };

</script>

<style scoped>
  .loading-overlay {
    position: absolute;
    top: 0; left: 0; bottom: 0; right: 0;
    background: rgba(255,255,255,0.8);
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10;
  }

  .resize-none {
    resize: none;
  }

  .list {
    font-weight: 700;
  }

  .fix-width {
    width: auto;
    height: auto;
  }


</style>
