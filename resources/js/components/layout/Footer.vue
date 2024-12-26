<template>
  <footer :class="mini ? '' : 'mt-0 pt-4 pt-md-5 border-top'">
    <div class="container" :class="mini ? 'mini-footer' : ''">
      <div class="row">
        <div class="col-12 col-md-4">
          <div v-if="!mini" class="footer-logo">
            <img
              class="img-fluid mx-auto mx-lg-0"
              src="/images/logo-rw-horizontal.png"
              alt=""
            />
          </div>
          <small class="d-block mb-3 mt-3 text-center text-lg-left">
            <div v-html="$t('footer.copyright', { year })"></div>
          </small>
        </div>
        <div class="col-12 col-md-8 centerfix">
          <ul class="list-unstyled list-inline text-small">
            <li>
              <router-link class="nav-link" :to="`/`">
                {{ $t('menu.home') }}
              </router-link>
            </li>
            <li>
              <router-link class="nav-link" :to="`/about`">
                {{ $t('menu.about') }}
              </router-link>
            </li>
            <li>
              <router-link class="nav-link" :to="`/neighborhoods`">
                {{ $t('menu.neighborhoods') }}
              </router-link>
            </li>
            <li v-if="!isLoggedIn">
              <router-link class="nav-link" :to="`/users/create`">
                {{ $t('menu.register') }}
              </router-link>
            </li>
            <li v-if="!isLoggedIn">
              <router-link class="nav-link last" :to="`/users/signin`">
                {{ $t('menu.sign_in') }}
              </router-link>
            </li>
            <li v-if="isLoggedIn">
              <router-link class="nav-link" :to="`/users/favorites`">
                {{ $t('menu.my_favorites') }}
              </router-link>
            </li>
            <li v-if="isLoggedIn">
              <router-link class="nav-link" :to="`/users/profile`">
                {{ $t('menu.my_profile') }}
              </router-link>
            </li>
            <li v-if="isLoggedIn">
              <a class="nav-link" href="javascript:void(0)" @click="onLogout">
                {{ $t('menu.log_out') }}
              </a>
            </li>
            <li>
              <router-link class="nav-link last" :to="`/report-bug`">
                {{ $t('menu.report_bug') }}
              </router-link>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: "Footer",
  props: ["isLoggedIn", "logout", "mini"],
  data() {
    return {
      role:
        this.$store.state.auth.user !== null
          ? this.$store.state.auth.user.role
          : null,
    };
  },
  computed: {
    year() {
      return (new Date).getFullYear();
    }
  },
  methods: {
    onLogout() {
      this.logout();
    },
  },
};
</script>

<style scoped>
.centerfix {
  width: 90%;
  height: 40px;
  vertical-align: center;
  margin-top: 20px;
}

.mini-footer .centerfix {
  margin-top: 0;
}

.mini-footer > .row {
  align-items: center;
}

footer {
  background: #012e55;
  color: #fff;
  clear: both;
}

.list-inline li {
  display: inline-block;
  margin-right: 10px;
}

.list-inline li:last-child {
  margin-right: 0px;
}

.list-inline li a {
  display: block;
  padding: 8px;
  font-size: 16px;
  color: #fff;
}
@media screen and (max-width: 768px) {
  .footer-logo {
    width: 194.5px;
    margin: 0 auto;
  }
  .footer-logo img {
    height: 50px;
  }
  .centerfix {
    margin-left: auto;
    margin-right: auto;
    text-align: center;
    height: auto;
    padding-bottom: 20px;
  }
}
</style>
