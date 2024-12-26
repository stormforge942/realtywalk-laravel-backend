<template>
  <div>
    <button type="button" @click="showModal">
      {{ $t('favorite.item.btn_add') }}
      <span class="star-icon"></span>
    </button>
  </div>
</template>

<script>
import Modal from "../../../utils/Modal/Modal";

export default {
  name: "NotLoggedInFavoriteProperty",
  components: {
    Modal,
  },
  methods: {
    showModal() {
      this.$swal({
        text:  this.$t('favorite.item.alert_login'),
      })
    },
  },
};
</script>

<style scoped>

button {
  border: 0px solid;
  color: #012E55;
  padding: 2px 10px;
  box-shadow: none;
  background: none;
  outline: 0;
  display: flex;
}

.btn-add {
  color: #012E55;
}

.star-icon {
  background-image: url(/images/favorite.png);
  height: 20px;
  width: 20px;
  vertical-align: middle;
  margin-left: 6px;
  background-size: contain;
}
</style>
