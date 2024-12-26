<template>
  <div>
     <div>
      <button
        v-if="!favorited"
        type="button"
        class="btn-add"
        @click="addToFavorite"
        :disabled="isProcessing"
      >
        {{ $t('favorite.item.btn_add') }}
        <span class="star-icon"></span>
      </button>
      <button
        v-else
        type="button"
        class="btn-remove"
        @click="removeFromFavorite"
        :disabled="isProcessing"
      >
        {{ $t('favorite.item.btn_remove') }}
        <span class="star-icon"></span>
      </button>
    </div>
  </div>
</template>

<script>
import { updateStorageValue } from '@/components/utils/helper';

export default {
  name: "LoggedInFavoriteProperty",
  props: ["favorited", "itemId", "conn"],
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

      const newTmpfavorites = [...this.favorites, ...[{ id: this.itemId, conn: this.conn }]];
      this.$store.commit('auth/updateFavorites', newTmpfavorites);

      axios
        axios.post(`/api/user/favorite/${this.itemId}`, { conn: this.conn }).then(response => {
          const { favorites } = response.data;
          updateStorageValue('property_favorites', favorites);
          this.$store.commit('auth/updateFavorites', favorites);
          this.isProcessing = false;
          this.$emit("favoriteItem", true);
        })
        .catch(error => (this.isProcessing = false));
    },
    removeFromFavorite() {
      this.isProcessing = true;

      const newTmpfavorites = this.favorites.filter(f => f.id !== this.itemId && f.conn === this.conn);
      this.$store.commit('auth/updateFavorites', newTmpfavorites);

      axios
        axios.post(`/api/user/unfavorite/${this.itemId}`, { conn: this.conn }).then(response => {
          const { favorites } = response.data;
          updateStorageValue('property_favorites', favorites);
          this.$store.commit('auth/updateFavorites', favorites);
          this.isProcessing = false;
          this.$emit("favoriteItem", false);
        })
        .catch(error => (this.isProcessing = false));
    }
  }
};
</script>

<style scoped>
button {
  border: 0px solid;
  padding: 2px 10px;
  box-shadow: none;
  background: none;
  outline: 0;
  display: flex;
  align-items: center;
}

.btn-add {
  color: #012E55;
}

.btn-remove {
  color: #dc3545;
}

.star-icon {
  background-image: url(/images/favorite.png);
  height: 20px;
  width: 20px;
  vertical-align: middle;
  margin-left: 6px;
  background-size: contain;
  background-repeat: no-repeat;
}

.btn-remove .star-icon {
  background-image: url(/images/favorite-selected.png);
}

</style>
