<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <TopNavigation />
      <div id="content" class="container">
        <LeftSidebar></LeftSidebar>

        <div id="right">
          <div class="loading-state" v-if="isLoading">
            <div class="lds-ripple">
              <div></div>
              <div></div>
            </div>
          </div>

          <div class="card h-100" v-else>
            <div class="card-header">
              <h2 class="card-title m-0 py-2">
                {{ $t('favorite.list.title') }}
              </h2>
            </div>

            <div class="card-body">
              <h4 v-if="favorites.length === 0">
                {{ $t('favorite.list.no_data') }}
              </h4>
              <div v-else>
                <div class="d-none d-lg-block">
                  <HomePropertyListItem
                    v-for="item in favorites"
                    :item="item"
                    :key="`fav-lg-${item.id}`"
                    :isLoggedIn="isLoggedIn"
                  ></HomePropertyListItem>
                </div>
                <div class="d-block d-lg-none">
                  <HomePropertyListSm
                    v-for="favorite in favorites"
                    :key="`fav-sm-${favorite.id}`"
                    :property="favorite"
                    :isLoggedIn="isLoggedIn"
                  ></HomePropertyListSm>
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
import PrimaryLayout from "../../layout/PrimaryLayout/PrimaryLayout";
import LeftSidebar from "../../layout/LeftSidebar";
import HomePropertyListItem from "../../HomePage/HomePropertyList/HomePropertyListItem/HomePropertyListItem";
import HomePropertyListSm from "../../HomePage/HomePropertyList/HomePropertyListSm/HomePropertyListSm";
import TopNavigation from '../../layout/TopNavigation';

//import ListFavoriteItem from "./ListFavoriteItem/ListFavoriteItem";

export default {
  name: "ListUserFavorites",
  components: {
    PrimaryLayout,
    LeftSidebar,
    HomePropertyListItem,
    HomePropertyListSm,
    TopNavigation
},
  data() {
    return {
      isLoading: false,
      favorites: [],
    };
  },
  computed: {
    isLoggedIn() {
      return this.$store.state.auth.status.loggedIn;
    },
  },
  methods: {
    async loadUserFavorites() {
      this.isLoading = true;
      this.$Progress.start();
      try {
        const { data } = await axios.get("/api/user/favorites");
        this.favorites = data;
      } catch (err) {
        console.error(err.response);
        if (err.response?.status === 401) {
          this.$store.dispatch('auth/userLogout');
          setTimeout(() => this.$router.push('/users/signin'));
        }
      } finally {
        this.isLoading = false;
      }
    },
    updateFavorites(propertyId) {
      const newList = this.favorites.filter(f => f.id !== propertyId && f.conn === 'realty');
      this.favorites = newList;
    },
  },
  mounted() {
    this.loadUserFavorites();
    this.$Progress.finish();
  },
};
</script>

<style scoped>
#right {
  min-height: 300px;
  border-left: 0px solid;
}
</style>
