<template>
  <div class="address-lookup form-group d-flex p-3">
    <input
      type="search"
      v-model="query"
      :placeholder="$t('menu.enter_address')"
      @keyup.enter="search"
    />
    <div
      class="
        input-group-append
        d-flex
        align-items-center
        justify-content-center
      "
      @click="search"
    >
      <i class="fa fa-search px-3"></i>
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    query: "",
  }),
  methods: {
    search() {
      window.location.href = `/address_lookup?q=${this.query}`;
    },
  },
};
</script>

<style scoped>
.address-lookup {
  position: absolute;
  margin-top: 10px;
  margin-left: -15rem;
  z-index: 901;
  background-color: white;
}
.address-lookup input {
  background-color: #eeeeee;
  border: 2px solid rgb(216, 216, 216);
  padding: 0 0.75rem;
  height: 40px;
  outline: none !important;
  width: 15rem;
}

.input-group-append {
  color: white;
  background: #012e55;
  cursor: pointer;
}
</style>
