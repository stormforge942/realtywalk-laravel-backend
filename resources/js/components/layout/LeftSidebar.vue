<template>
  <div id="left" class="d-none d-sm-none d-md-none d-lg-block d-xl-block">
    <div id="logo">
      <router-link :to="`/`">
        <img
          class="img-fluid"
          src="/images/logo-rw.png"
          alt="NHW Logo"
        />
      </router-link>
    </div>
    <div class="footprints">
      <img id="footprints-img" src="/images/footprints-vert.png" alt="" />
    </div>
  </div>
</template>

<script>
export default {
  name: "LeftSidebar"
};
</script>

<style scoped></style>
