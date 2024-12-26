<template>
  <div class="media mb-5">
    <div class="picture">
      <router-link v-if="item.pu || item.alt_path_url" :to="item.pu || item.alt_path_url">
        <img :src="listImageUrl" :alt="`${item.fa || item.full_address} Thumbnail`" />
      </router-link>

      <template v-else>
        <img :src="listImageUrl" :alt="`${item.fa || item.full_address} Thumbnail`" />
      </template>
    </div>

    <div class="media-body">
      <PropertyListDetails :item="item" :bgheader="true" />
      <PropertyListCallToActions :item="item" :isFavorited="isFavorited" :isLoggedIn="isLoggedIn"
        @favoriteItem="onItemFavorite" />
    </div>
  </div>
</template>

<script>
import PropertyListDetails from "../../../Property/PropertyList/PropertyListDetails/PropertyListDetails";
import PropertyListCallToActions from "../../../Property/PropertyList/PropertyListCallToActions/PropertyListCallToActions";

export default {
  name: "HomePropertyListItem",
  props: ["item", "isLoggedIn"],
  components: {
    PropertyListDetails,
    PropertyListCallToActions,
  },
  data() {
    return {
      isFavorited: false,
      listImageUrl: '/images/property_no_img_thumb.png',
    };
  },
  computed: {
    isFavorite() {
      const id = this.item?.pri || this.item?.id
      return this.favorites.some(f => f.id === id && f.conn === 'realty');
    },
    favorites() {
      return this.$store.getters['auth/favorites'];
    }
  },
  methods: {
    onItemFavorite(bool) {
      this.isFavorited = bool;
    },
    normalizeMedia(property) {
      if (property.pi) {
        this.listImageUrl = property.pi;
      } else if (property.type == 1 && property.media && property.media.length > 0) {
        let mainMedia = property.media.sort((a, b) => {
          return a.order_column - b.order_column;
        })[0];

        this.listImageUrl = mainMedia.fullUrl;
      } else if (property.type == 0 && property.image_urls) {
        this.listImageUrl = property.image_urls[0];
      } else if (property?.builderPrimaryLogo) {
        this.listImageUrl = property.builderPrimaryLogo;
      } else if (property?.builder?.media?.length) {
        this.listImageUrl = property.builder.media.find(
          (m) => m.collection_name == 'builder_logo'
        )?.fullUrl;
      }
    },
  },
  created() {
    const id = this.item?.pri || this.item?.id;
    this.isFavorited = this.favorites.some(f => f.id === id && f.conn === 'realty');
    this.normalizeMedia(this.item);
  },
};
</script>

<style lang="scss" scoped>
.picture {
  width: 220px;
  height: 220px;
  margin-right: 1rem;

  a {
    display: block;
    width: 100%;
    height: 100%;
  }

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
  }

  @media screen and (max-width: 1024px) {
    width: 80px;
    height: 80px;
  }
}
</style>
