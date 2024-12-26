<template>
  <div>
    <h3>Featured Homes</h3><!-- class="mb-4" -->
    <FeaturedHomesItem
      v-for="item in properties"
      :key="item.id"
      :item="item"
    ></FeaturedHomesItem>
  </div>
</template>

<script>
import FeaturedHomesItem from "./FeaturedHomesItem/FeaturedHomesItem";

export default {
  name: "FeaturedHomes",
  props: ["properties"],
  components: {
    FeaturedHomesItem
  }
};
</script>

<style scoped></style>
