<template>
  <div>
    <AppHeader :isLoggedIn="isLoggedIn" :logout="logout"></AppHeader>
    <slot name="contentlayout"></slot>
    <Footer :isLoggedIn="isLoggedIn" :logout="logout"></Footer>
  </div>
</template>

<script>
import AppHeader from "../../AppHeader/AppHeader";
import Footer from "../Footer";

export default {
  name: "PrimaryLayout",
  components: {
    AppHeader,
    Footer
  },
  computed: {
    isLoggedIn() {
      return this.$store.state.auth.status.loggedIn;
    }
  },
  methods: {
    logout() {
      const { dispatch } = this.$store;

      dispatch("auth/userLogout");
    }
  }
};
</script>

<style scoped></style>
