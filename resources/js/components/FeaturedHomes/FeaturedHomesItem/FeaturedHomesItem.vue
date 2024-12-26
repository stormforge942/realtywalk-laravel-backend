<template>
  <div class="details-item" > <!-- class="pb-4"-->
    <PropertyListDetails :item="item" />
    <div ><!--class="pb-3"-->
      <PropertyListCallToActions :item="item" />
    </div>
  </div>
</template>

<script>
import PropertyListDetails from "../../Property/PropertyList/PropertyListDetails/PropertyListDetails";
import PropertyListCallToActions from "../../Property/PropertyList/PropertyListCallToActions/PropertyListCallToActions";

export default {
  name: "FeaturedHomesItem",
  props: ["item"],
  components: {
    PropertyListDetails,
    PropertyListCallToActions
  }
};
</script>

<style scoped>
.details-item {
  margin: 40px 0px 50px 0px;
}
</style>
