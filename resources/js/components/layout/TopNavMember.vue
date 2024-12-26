<template>
  <div
    class="container-fluid d-none d-sm-none d-md-none d-lg-block d-xl-block"
  >
    <div class="row d-flex align-items-center">
      <div class="col-lg-10 col-md-6 col-sm-6">
        <div class="splash-logo">
          <img class="" src="/images/footprints.png" alt="" />
        </div>
      </div>
      <div class="col-lg-2 col-md-6 col-sm-6 text-center">
        <div class="grey-btn">
          <router-link v-if="!isLoggedIn" class="text-uppercase" :to="`/users/create`">
            {{ $t('general.navigation.register') }}
          </router-link>
          <router-link v-else class="text-uppercase" :to="`/users/favorites`">
            {{ $t('general.navigation.my_favorites') }}
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "TopNavMember",
  computed: {
    isLoggedIn() {
      return this.$store.state.auth.status.loggedIn;
    }
  }
};
</script>

<style scoped>

.row {
  margin: 0px;
}
@media screen and (max-width: 1034px) {
  .grey-btn a {
    font-size: 0.75em;
  }
}
</style>
