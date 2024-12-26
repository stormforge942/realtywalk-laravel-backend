<template>
  <div class="dropdown-panel" :class="panelClass" :style="{
    height: `${toggled ? maxHeight : 0}${maxHeightMeasurement ? maxHeightMeasurement : 'px'}`,
    opacity: toggled ? 1 : 0
    }">
    <slot/>
  </div>
</template>

<script>

export default {
  name: "DropdownPanel",
  props: ["panelClass", "toggled", "maxHeight", "maxHeightMeasurement"],
  data() {
    return {};
  },
};
</script>

<style scoped>
.dropdown-panel {
  position: relative;
  overflow: hidden;
  background-color: #fff;
  transition: height .5s, opacity .5s;
}

</style>
