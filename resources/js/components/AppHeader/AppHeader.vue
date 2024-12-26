<template>
  <header class="header-container">
    <nav class="navbar navbar-expand-lg navbar-light bg-newhomewalk">
      <div class="row">
        <div class="col-2 col-logo">
          <div class="py-2 py-xl-3 pl-xl-2">
            <router-link class="navbar-brand" :class="isOpen ? 'navbar-opened' : ''" :to="'/'">
              <img class="img-fluid d-none d-lg-block" src="/images/logo-rw.png" alt="RW Logo" />
              <img class="img-fluid d-lg-none" src="/images/logo-rw-horizontal.png" alt="RW Logo" />
            </router-link>
          </div>
        </div>
        <div class="col-10 col-right-content">
          <div class="py-2 py-xl-3 pr-xl-2 col-right-inner">
            <div class="col-instructions" v-if="showInstructions">
              <div class="col-instructions-inner d-none d-md-block">
                {{ showInstructions }}
              </div>
            </div>
            <div class="col-nav" :class="{'col-nav-full': !showInstructions}">
              <div v-if="isLoggedIn" class="saved-searches">
                <div id="top-nav-links" class="d-flex align-items-center">
                  <div class="go-home d-none d-lg-flex" v-if="$router.currentRoute.fullPath !== '/' || homeData.showHomeMap">
                    <a href="javascript:void(0);" @click="goHome">
                      <i class="fa fa-th-large"></i>
                      <span class="ml-1">{{ $t("menu.home") }}</span>
                    </a>
                  </div>

                  <div class="profile-nav-links d-none d-lg-flex">
                    <div class="btn-dropdown text-white d-flex align-items-center h-100">
                      <span class="text-settings">{{ $t("home.header.btn_settings") }}</span>
                      <span class="fa fa-caret-down"></span>
                    </div>

                    <div class="dropdown-content">
                      <router-link :to="`/users/profile`">{{ $t("menu.my_profile") }}</router-link>
                      <router-link :to="`/users/favorites`">{{ $t("menu.my_favorites") }}</router-link>
                      <a href="javascript:void(0)" @click.prevent="onLogout">{{ $t("menu.log_out") }}</a>
                    </div>
                  </div>

                  <button class="btn-toggle-saved-searches d-none d-lg-flex" :class="viewSavedSearches ? 'opened' : ''" @click="onToggleSavedSearches">
                    <span class="label">{{ $t("home.header.btn_saved_searches") }}</span>
                    <span :class="viewSavedSearches ? 'fa fa-caret-up' : 'fa fa-caret-down'"></span>
                  </button>

                  <DropdownPanel
                    panelClass="saved-searches-panel"
                    :toggled="viewSavedSearches"
                    :maxHeight="pixelHeight"
                  >
                    <SavedSearches :closePanel="onToggleSavedSearches" />
                  </DropdownPanel>
                </div>
              </div>

              <div v-else class="top-actions d-none d-lg-flex">
                <div class="link-wrapper go-home" v-if="$router.currentRoute.fullPath !== '/' || homeData.showHomeMap">
                  <a href="javascript:void(0);" @click="goHome"><i class="fa fa-th-large"></i>{{ $t("menu.home") }}</a>
                </div>
                <div class="link-wrapper">
                  <router-link :to="`/users/create`">{{ $t("menu.register") }}</router-link>
                </div>
                <div class="link-wrapper">
                  <router-link class="link" :to="`/users/signin`">{{ $t("menu.sign_in") }}</router-link>
                </div>
              </div>

              <div class="nav-items-inner">
                <button class="hamburger hamburger--elastic d-lg-none" :class="isOpen ? 'is-active' : ''" type="button" aria-label="Menu" aria-controls="navigation" @click="openNav">
                  <span class="hamburger-box"><span class="hamburger-inner"></span></span>
                </button>

                <div id="navbarToggler" class="navbar-collapse sidenav" :class="{ open: isOpen }">
                  <ul class="navbar-nav align-items-center ml-auto">
                    <li class="nav-item d-lg-none go-home" v-if="$router.currentRoute.fullPath !== '/' || homeData.showHomeMap">
                      <a href="javascript:void(0);" class="nav-link" @click="goHome">{{ $t("menu.home") }}</a>
                    </li>
                    <li class="nav-item" v-click-outside="closeAddressLookup">
                      <a class="nav-link" href @click.prevent="toggleAddressLookup">{{ $t("menu.address_lookup") }}</a>
                      <address-lookup v-if="showAddressLookup" @close="closeAddressLookup" />
                    </li>
                    <li class="nav-item">
                      <router-link class="nav-link" :to="`/about`">{{ $t("menu.about") }}</router-link>
                    </li>
                    <li class="nav-item">
                      <router-link class="nav-link" :to="`/neighborhoods`">{{ $t("menu.neighborhoods") }}</router-link>
                    </li>
                    <li class="nav-item d-lg-none" v-if="isLoggedIn">
                      <router-link class="nav-link" :to="`/users/favorites`">{{ $t("menu.my_favorites") }}</router-link>
                    </li>
                    <li class="nav-item d-lg-none" v-if="!isLoggedIn">
                      <router-link class="nav-link" :to="`/users/create`">{{ $t("menu.register") }}</router-link>
                    </li>
                    <li class="nav-item d-lg-none" v-if="!isLoggedIn">
                      <router-link class="nav-link" :to="`/users/signin`">{{ $t("menu.sign_in") }}</router-link>
                    </li>
                    <li class="nav-item d-lg-none" v-if="isLoggedIn">
                      <a class="nav-link" href="javascript:void(0)" @click="onToggleSavedSearches">{{ $t("home.header.btn_saved_searches") }}</a>
                    </li>
                    <li class="nav-item d-lg-none" v-if="isLoggedIn">
                      <a class="nav-link" href="javascript:void(0)" @click="onLogout">{{ $t("menu.log_out") }}</a>
                    </li>
                    <li class="nav-item">
                      <button type="button" class="nav-link last" @click="openReportProblemModal">{{ $t("menu.report_bug") }}</button>
                    </li>
                    <li>
                      <button type="button" class="watch-the-demo" @click="openModal()">
                        <span class="label">{{ $t("menu.watch_the_demo") }}</span>
                        <span class="arrow">
                          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 292.362 292.362" fill="currentColor"><path d="M286.935 69.377c-3.614-3.617-7.898-5.424-12.848-5.424H18.274c-4.952 0-9.233 1.807-12.85 5.424C1.807 72.998 0 77.279 0 82.228c0 4.948 1.807 9.229 5.424 12.847l127.907 127.907c3.621 3.617 7.902 5.428 12.85 5.428s9.233-1.811 12.847-5.428L286.935 95.074c3.613-3.617 5.427-7.898 5.427-12.847 0-4.948-1.814-9.229-5.427-12.85z"></path></svg>
                        </span>
                      </button>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <Modal ref="modal" @closeModal="closeModal">
      <template v-slot:body>
        <div class="modal-body">
          <div class="video-wrapper">
            <youtube video-id="z98-EokDUW4" ref="youtube" :fit-parent="true" />
          </div>
        </div>
      </template>
    </Modal>

    <Modal ref="reportProblemModal" content-class="report-bug-modal" :showCloseBtn="false" @closeModal="closeReportProblemModal">
      <template v-slot:body>
        <div class="modal-body">
          <ReportBugModal @submit="closeReportProblemModal" />
        </div>
      </template>
    </Modal>
  </header>
</template>

<script>
import DropdownPanel from "../DropdownPanel/DropdownPanel";
import SavedSearches from "../SavedSearches/SavedSearches";
import AddressLookup from "./AddressLookup.vue";
import Modal from "../../components/utils/Modal/Modal";
import ReportBugModal from '../ReportBug/modal';
import { mapActions, mapState } from 'vuex';

export default {
  name: "AppHeader",
  props: ["isLoggedIn", "logout", "showInstructions"],
  components: {
    DropdownPanel,
    SavedSearches,
    AddressLookup,
    Modal,
    ReportBugModal
  },
  data() {
    return {
      isOpen: false,
      viewSavedSearches: false,
      showAddressLookup: false,
      pixelHeight: 0,
      role: this.$store.state.auth.user !== null ? this.$store.state.auth.user.role : null,
    };
  },
  computed: {
    ...mapState('home', { homeData: state => state.data }),

    player() {
      return this.$refs.youtube.player;
    },
    user() {
      return this.$store.state.auth.user;
    }
  },
  mounted() {
    this.recalculatePixels();
    window.addEventListener("resize", this.recalculatePixels);
  },
  unmounted() {
    window.removeEventListener("resize", this.recalculatePixels);
  },
  methods: {
    ...mapActions('home', ['saveHistory']),
    goHome() {
      this.saveHistory({ showHomeMap: false });
      this.saveHistory({ property: { geometry: null }});

      this.closeNav();

      if (this.$router.currentRoute.fullPath !== "/") {
        this.$router.push("/");
      }
    },
    onLogout() {
      this.logout();
    },
    openNav() {
      this.isOpen = !this.isOpen;
    },
    closeNav() {
      this.isOpen = false;
    },
    toggleAddressLookup() {
      this.showAddressLookup = !this.showAddressLookup;
      this.isOpen = false;
    },
    closeAddressLookup() {
      this.showAddressLookup = false;
    },
    onToggleSavedSearches() {
      this.viewSavedSearches = !this.viewSavedSearches;
      if (this.isOpen) {
        this.closeNav();
      }
    },
    recalculatePixels() {
      this.pixelHeight = window.innerHeight;
    },
    openModal() {
      const element = this.$refs.modal.$el;
      $(element).modal("show");
      this.player.playVideo();
    },
    closeModal() {
      this.player.pauseVideo();
      const element = this.$refs.modal.$el;
      $(element).modal("hide");
    },
    openReportProblemModal() {
      const element = this.$refs.reportProblemModal.$el;
      $(element).modal("show");
    },
    closeReportProblemModal() {
      const element = this.$refs.reportProblemModal.$el;
      $(element).modal("hide");
    }
  },
};
</script>

<style lang="scss" scoped>
#top-nav-links {
  background-color: #001E38;
  height: 40px;

  .go-home {
    background: #012e55;
    height: 100%;

    a {
      height: 100%;
      display: inline-flex;
      justify-content: center;
      align-items: center;
      // font-size: 0.9rem;
      color: #fff;
      background-color: transparent;
      transition: 250ms;
      padding: 0 1rem;
      text-transform: uppercase;

      &:hover {
        background-color: #01223f;
        color: #ffc501;
        text-decoration: none;
      }

      > i, > span {
        position: relative;
        top: 2px;
      }
    }
  }

  .profile-nav-links {
    position: relative;
    height: 100%;
    list-style: none;
    margin: 0;
    padding: 0;

    .fa {
      color: #ffc501;
      margin-left: 15px;
      font-size: 22px;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      top: 100%;
      background-color: #f1f1f1;
      width: 100%;
      box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
      z-index: 9999;

      a {
        color: #012E55;
        text-decoration: none;
        display: block;
        padding: 5px 10px;

        &:hover {
          background-color: #ddd;
        }
      }
    }

    &:hover {
      .btn-dropdown {
        color: #FFC501;
      }

      .dropdown-content {
        display: block;
      }
    }
  }
}

button:focus {
  outline: 0;
}

.col-logo {
  flex: 0 0 300px;
  max-width: 300px;
}

.col-right-content {
  display: flex;
  flex: 0 0 calc(100% - 300px);
  max-width: calc(100% - 300px);
}

.col-right-inner {
  display: flex;
  justify-content: flex-end;
  width: 100%;
  position: relative;
}

.col-instructions {
  display: none;
  align-items: center;
  padding-right: 10px;
  color: white;
  font-size: 1rem;
  letter-spacing: 0.025rem;
  line-height: 1.275;
}

.col-instructions-inner {
  max-width: 100%;
}

.col-nav {
  padding-left: 0px;
}

.col-nav.col-nav-full {
  width: 100%;
  padding-left: 0;
}

.top-actions {
  position: absolute;
  display: flex;
  top: 0;
  right: 5px;
  background-color: #011f39;
  text-transform: uppercase;
  z-index: 900;
}

.top-actions .link-wrapper {
  position: relative;
}

.top-actions .link-wrapper.go-home {
  background-color: #012e55;
}

.top-actions .link-wrapper a {
  display: block;
  font-size: 0.9rem;
  color: #fff;
  background-color: transparent;
  transition: 250ms;
  padding: 0.5rem 1rem;
}

.top-actions .link-wrapper a:hover {
  background-color: #01223f;
  color: #ffc501;
  text-decoration: none;
}

.top-actions .link-wrapper:not(.go-home):not(:last-child)::after {
  content: "|";
  position: absolute;
  top: 50%;
  right: -1.5px;
  transform: translateY(-50%);
  color: #ffc501;
  opacity: 0.5;
  font-family: serif;
}

.saved-searches {
  position: absolute;
  top: 0;
  right: 0;
}

.btn-toggle-saved-searches {
  // position: absolute;
  top: 0;
  right: 10px;
  justify-content: center;
  align-items: center;
  background: #001e38;
  color: #fff;
  // font-size: 0.9rem;
  text-transform: uppercase;
  padding: 0.5rem 0;
  width: 170px;
  border: none;
  z-index: 1002;
  transition: 250ms;
}

.btn-toggle-saved-searches:hover {
  color: #ffc501;
  background-color: #01223f;
}

.btn-toggle-saved-searches.opened {
  background: #012e55;
}

.btn-toggle-saved-searches .label {
  position: relative;
  top: 2px;
}

.btn-toggle-saved-searches .fa {
  color: #ffc501;
  margin-left: 15px;
  font-size: 22px;
}

.saved-searches-panel {
  position: fixed;
  top: 0;
  right: 0;
  width: 450px;
  background: #001e38;
  z-index: 1001;
}

.navbar {
  padding: 0;
}

.navbar .navbar-flex-container {
  flex-wrap: nowrap;
  width: 100%;
  padding: 20px;
}

.nav-link {
  padding-top: 0rem;
  padding-bottom: 0rem;
}

.navbar.navbar-light .navbar-nav .nav-link {
  color: #fff;
  background-color: transparent;
  text-transform: uppercase;
  // padding: 0.25rem 1.5rem;
  transition: 250ms;
  text-align: center;
  border: none;
}

.navbar.navbar-light .navbar-nav .nav-link:hover,
.navbar.navbar-light .navbar-nav .nav-link:focus {
  color: #ffc501;
}

.col-nav {
  display: flex;
  align-items: flex-end;
}

.nav-items-inner {
  position: relative;
  width: 100%;
}

.nav-item:not(:first-of-type)::before {
  content: "|";
  display: block;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  margin-top: -1px;
  color: #ffc501;
  opacity: 0.5;
  font-family: serif;
}

.nav-item.go-home + .nav-item::before {
  display: none;
}

.watch-the-demo {
  background: #fff;
  width: 190px;
  height: 40px;
  display: flex;
  align-items: center;
  border: 0;
  padding: 0;
}

.watch-the-demo .label {
  display: flex;
  justify-content: center;
  align-items: center;
  color: #021d35;
  font-size: 16px;
  font-weight: 600;
  height: 100%;
  width: calc(100% - 40px);
}

.watch-the-demo .arrow {
  display: flex;
  justify-content: center;
  align-items: center;
  background: #ffc500;
  width: 40px;
  height: 100%;
}

.watch-the-demo .arrow svg {
  transform: rotate(-90deg);
  color: #021d35 !important;
  width: 13px;
}

.navbar-expand-lg .navbar-nav .nav-link.last {
  padding-right: 0;
}

.navbar-brand img.d-lg-block {
  height: 90px;
}

.hamburger.is-active {
  z-index: 20001;
}


/* Dropdown Button */
.btn-dropdown {
  color: #fff;
  text-transform: uppercase;
  padding: 0rem 1.5rem;
  transition: 250ms;
}

.text-settings {
  position: relative;
  top: 2px;
}

/* The container <div> - needed to position the dropdown content */
.dropdown {
  position: relative;
  display: inline-block;
}

@media screen and (max-width: 575px) {
  .sidenav.open {
    width: 100vw;
  }

  .saved-searches-panel {
    width: 100vw;
  }

  .navbar {
    padding: 0.75rem 0rem;
  }
}

@media screen and (max-width: 991px) {
  .header-container {
    position: fixed;
    top: 0px;
    width: 100%;
    z-index: 1040;
  }

  .col-logo,
  .col-right-content {
    flex: 0 0 50%;
    max-width: 50%;
  }

  .navbar-brand.navbar-opened {
    z-index: 20001;
    position: relative;
  }

  .navbar .navbar-flex-container {
    padding: 0;
    align-items: center;
  }

  .navbar-nav {
    /*padding: 3rem 0;*/
    align-items: center;
  }

  .col-nav {
    align-items: center;
  }

  .sidenav {
    height: 100%;
    max-width: 100%;
    width: 300px;
    position: fixed;
    top: 0px;
    right: 0px;
    transform: translateX(100%);
    background-color: #012e55;
    overflow-x: hidden;
    padding-top: 105px;
    transition: transform 250ms, box-shadow 250ms;
    box-shadow: none;
    z-index: 20000;
  }

  .sidenav.open {
    transform: translateX(0%);
    box-shadow: -11px 1px 13px -6px rgba(0, 0, 0, 0.24);
  }

  .sidenav a {
    padding: 10px;
    text-decoration: none;
    font-size: 20px;
    color: #eee;
    display: block;
    min-width: 100%;
    white-space: nowrap;
  }

  .nav-items-inner {
    display: flex;
    justify-content: flex-end;
  }

  .navbar-brand .d-lg-none {
    height: 45px;
  }

  .nav-item:not(:first-of-type)::before {
    display: none;
  }
}

@media screen and (min-width: 576px) and (max-width: 991px) {
  .navbar {
    padding: 1rem;
  }
}

@media screen and (min-width: 992px) {
  .col-logo {
    flex: 0 0 200px;
    max-width: 200px;
  }

  .col-right-content {
    flex: 0 0 calc(100% - 200px);
    max-width: calc(100% - 200px);
  }

  .col-instructions {
    display: flex;
    flex: 0 0 50%;
    max-width: 60%;
  }

  .col-nav:not(.col-nav-full) {
    flex: 0 0 50%;
    max-width: 100%;
  }

  .nav-items-inner {
    right: 2px;
  }

  .navbar.navbar-light .navbar-nav .nav-link {
    padding: 0.25rem 0.5rem;
  }

  .top-actions {
    right: 5px;
  }
}

@media screen and (min-width: 1200px) {
  .nav-items-inner {
    right: -7px;
  }

  .col-logo {
    flex: 0 0 275px;
    max-width: 275px;
  }

  .col-right-content {
    flex: 0 0 calc(100% - 275px);
    max-width: calc(100% - 275px);
  }

  .col-instructions {
    flex: 0 0 50%;
    max-width: 50%;
    font-size: 0.95rem;
    padding-right: 15px;
    letter-spacing: 0.0125rem;
    line-height: 1.375;
  }

  .col-nav:not(.col-nav-full) {
    flex: 0 0 50%;
    max-width: 100%;
  }

  .top-actions {
    right: 10px;
  }

  .navbar.navbar-light .navbar-nav .nav-link {
    padding: 0.25rem 0.875rem;
  }
}

@media screen and (min-width: 1400px) {
  .col-logo {
    flex: 0 0 300px;
    max-width: 300px;
  }

  .col-right-content {
    flex: 0 0 calc(100% - 300px);
    max-width: calc(100% - 300px);
  }
  .col-right-inner {
    flex-direction: row;
    justify-content: space-between;
  }

  .col-instructions {
    flex: 0 0 35%;
    max-width: 35%;
    font-size: 1.125rem;
    letter-spacing: 0.025rem;
    line-height: 1.5;
  }

  .col-instructions-inner {
    max-width: 500px;
  }

  .col-nav:not(.col-nav-full) {
    flex: 0 0 65%;
    max-width: 65%;
    justify-content: flex-end;
  }
}

@media screen and (min-width: 1800px) {
  .col-instructions {
    font-size: 1.25rem;
  }

  .col-instructions-inner {
    max-width: 550px;
  }
}

@media screen and (max-width: 575px) {
  .navbar.navbar-light .navbar-nav .nav-link,
  .navbar.navbar-light .navbar-nav .nav-item .dropdown .btn-dropdown {
    padding: 0.25rem 0;
  }
}
</style>

<style>
.modal-dialog.report-bug-modal {
  width: 50%;
  min-width: 250px;
}
</style>
