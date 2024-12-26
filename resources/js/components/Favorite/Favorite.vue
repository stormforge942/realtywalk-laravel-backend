
<template>
  <div>
    <AddToFavorite
      :isLoggedIn="isLoggedIn"
      :favorited="favorited"
      :itemId="itemId"
      @favoriteItem="onItemFavorite"
    ></AddToFavorite>
  </div>
</template>

<script>
import AddToFavorite from "./AddToFavorite/AddToFavorite";

export default {
  name: "Favorite",
  components: {
    AddToFavorite
  },
  props: ["isLoggedIn", "favorited", "itemId"],
  methods: {
    onItemFavorite(bool) {
      this.$emit("favoriteItem", bool);
    }
  }
};
</script>

<style lang="scss" scoped></style>
