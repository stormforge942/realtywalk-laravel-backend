<template>
  <div class="media" v-if="typeof item !== 'undefined'">
    <img :src="`${listImageUrl}`" class="img-fluid mr-3" alt="" />
    <div class="media-body">
      <PropertyListDetails :item="item" />
      <PropertyListCallToActions
        :item="item"
        :isFavorited="isFavorite"
        :isLoggedIn="isLoggedIn"
        @favoriteItem="onItemFavorite"
      />
    </div>
  </div>
</template>

<script>
import PropertyListDetails from "../../../Property/PropertyList/PropertyListDetails/PropertyListDetails";
import PropertyListCallToActions from "../../../Property/PropertyList/PropertyListCallToActions/PropertyListCallToActions";

export default {
  name: "ListFavoriteItem",
  props: ["item", "isLoggedIn", "updateFavorites"],
  components: {
    PropertyListDetails,
    PropertyListCallToActions
  },
  data() {
    return {
      isFavorited: false,
      listImageUrl: `/images/property_no_img_thumb.png`
    };
  },
  computed: {
    isFavorite() {
      return this.favorites.some(f => f.id === this.item?.id && f.conn === 'realty');
    },
    favorites() {
      return this.$store.getters['auth/favorites'];
    }
  },
  methods: {
    onItemFavorite(bool) {
      this.isFavorited = bool;
      this.updateFavorites(this.item.id);
    },
    normalizeMedia(data) {
      if (data.media.length > 0) {
        let mainMedia = data.media.sort((a, b) => {
          return a.order_column - b.order_column;
        })[0];
        this.listImageUrl = `/storage/properties/${mainMedia.id}/${mainMedia.file_name}`;
      }
    }
  },
  created() {
    this.isFavorited = this.favorites.some(f => f.id === this.item?.id && f.conn === 'realty');
    this.normalizeMedia(this.item);
  }
};
</script>

<style scoped>
.media {
  padding-bottom: 25px;
}
img {
  width: 100px;
  height: 100px;
}
</style>
