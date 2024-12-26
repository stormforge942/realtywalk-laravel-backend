<template>
  <div>
    <LoggedInFavorite
      v-if="isLoggedIn"
      :favorited="favorited"
      :itemId="itemId"
      @favoriteItem="onItemFavorite"
    />
    <NotLoggedInFavorite v-else />
  </div>
</template>

<script>
import NotLoggedInFavorite from "./NotLoggedInFavorite/NotLoggedInFavorite";
import LoggedInFavorite from "./LoggedInFavorite/LoggedInFavorite";

export default {
  name: "AddToFavorite",
  components: {
    LoggedInFavorite,
    NotLoggedInFavorite
  },
  props: ["isLoggedIn", "favorited", "itemId"],
  methods: {
    onItemFavorite(bool) {
      this.$emit("favoriteItem", bool);
    }
  }
};
</script>

<style scoped></style>
