<template>
  <div>
    <LoggedInFavoriteProperty
      v-if="isLoggedIn"
      :favorited="favorited"
      :itemId="itemId"
      :conn="conn"
      @favoriteItem="onItemFavorite"
    />
    <NotLoggedInFavoriteProperty v-else />
  </div>
</template>

<script>
import NotLoggedInFavoriteProperty from "./NotLoggedInFavoriteProperty/NotLoggedInFavoriteProperty";
import LoggedInFavoriteProperty from "./LoggedInFavoriteProperty/LoggedInFavoriteProperty";

export default {
  name: "AddToFavoriteProperty",
  components: {
     LoggedInFavoriteProperty,
     NotLoggedInFavoriteProperty
  },
  props: ["isLoggedIn", "favorited", "itemId", "conn"],
  methods: {
    onItemFavorite(bool) {
      this.$emit("favoriteItem", bool);
    }
  }
};
</script>

<style scoped></style>
