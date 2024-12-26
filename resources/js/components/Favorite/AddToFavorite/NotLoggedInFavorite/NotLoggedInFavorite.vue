<template>
  <div>
    <div class="d-none d-lg-block">
      <button type="button" @click="showModal">
        {{ $t('favorite.item.btn_add') }}
      </button>
    </div>

    <div class="d-block d-lg-none mobile-favorite">
      <button type="button" class="btn-add" @click="showModal">
        <img class="favorite" src="/images/favorite.png" />
      </button>
    </div>
  </div>
</template>

<script>
import Modal from "../../../utils/Modal/Modal";

export default {
  name: "NotLoggedInFavorite",
  components: {
    Modal
  },
  methods: {
    showModal() {
      this.$swal({
        text: this.$t('favorite.item.alert_login')
      });
    }
  }
};
</script>

<style scoped>
button {
  background: #012e55;
  border: 0px solid;
  color: #fff;
  padding: 2px 10px;
  box-shadow: none;
  outline: 0;
}

.btn-add {
  background: #012e55;
}

.btn-remove {
  background: #dc3545;
}

.mobile-favorite button {
  outline: 0;
  box-shadow: none;
  background: none;
  border: 0px solid;
  display: block;
  border-bottom: 2px solid #fff;
}
</style>
