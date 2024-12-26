<template>
  <div>
     <div class="d-none d-lg-block">
      <button
        v-if="!favorited"
        type="button"
        class="btn-add"
        @click="addToFavorite"
        :disabled="isProcessing"
      >
        {{ $t('favorite.item.btn_add') }}
      </button>
      <button
        v-else
        type="button"
        class="btn-remove"
        @click="removeFromFavorite"
        :disabled="isProcessing"
      >
        {{ $t('favorite.item.btn_remove') }}
      </button>
    </div>

    <div class="d-block d-lg-none mobile-favorite">
      <button
        v-if="!favorited"
        type="button"
        class="btn-add"
        @click="addToFavorite"
        :disabled="isProcessing"
      >
        <img class="favorite" src="/images/favorite.png" />
      </button>

      <button
        v-else
        type="button"
        class="btn-remove"
        @click="removeFromFavorite"
        :disabled="isProcessing"
      >
         <img class="favorite" src="/images/favorite-selected.png" />
      </button>
    </div>
  </div>
</template>

<script>
import { updateStorageValue } from '@/components/utils/helper';

export default {
  name: "LoggedInFavorite",
  props: ["favorited", "itemId"],
  data() {
    return {
      isProcessing: false
    };
  },
  computed: {
    favorites() {
      return this.$store.getters['auth/favorites'];
    },
  },
  methods: {
    addToFavorite() {
      this.isProcessing = true;

      const newTmpfavorites = [...this.favorites, ...[{ id: this.itemId, conn: 'realty' }]];
      this.$store.commit('auth/updateFavorites', newTmpfavorites);

      axios.post(`/api/user/favorite/${this.itemId}`, { conn: 'realty' }).then(response => {
        const { favorites } = response.data;
        updateStorageValue('property_favorites', favorites);
        this.$store.commit('auth/updateFavorites', favorites);
        this.isProcessing = false;
        this.$emit("favoriteItem", true);
      }).catch(error => (this.isProcessing = false));
    },
    removeFromFavorite() {
      this.isProcessing = true;

      const newTmpfavorites = this.favorites.filter(f => f.id !== this.itemId && f.conn === 'realty');
      this.$store.commit('auth/updateFavorites', newTmpfavorites);

      axios.post(`/api/user/unfavorite/${this.itemId}`, { conn: 'realty' }).then(response => {
        const { favorites } = response.data;
        updateStorageValue('property_favorites', favorites);
        this.$store.commit('auth/updateFavorites', favorites);
        this.isProcessing = false;
        this.$emit("favoriteItem", false);
      }).catch(error => (this.isProcessing = false));
    }
  }
};
</script>

<style scoped>
button {
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
