<template>
  <div class="list-call d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
      <template v-if="typeof item.conn === 'undefined' || item.conn === 'realty'">
        <router-link v-if="item.pu || item.alt_path_url" :to="item.pu || item.alt_path_url">
          {{ $t('property.cta.btn_view_listing') }}
        </router-link>

        <a v-else href="javascript:void(0)">
          {{ $t('property.cta.btn_view_listing') }}
        </a>
      </template>

      <a v-else :href="item.pu" target="_blank">
        {{ $t('property.cta.btn_view_listing') }}
      </a>

      <template v-if="!notForSale">
        <AddToFavoriteProperty
          :isLoggedIn="isLoggedIn"
          :favorited="isFavorited"
          :itemId="item.pri || item.id"
          :conn="item.conn || 'realty'"
          @favoriteItem="onItemFavorite"
        />

        <ScheduleListing
          class="schedule-listing"
          :isLoggedIn="isLoggedIn"
          :itemId="item.pri || item.id"
          :classBtn="'mr-3'"
        />
      </template>

      <span v-else class="badge property-badge ml-2" :class="propertyStatusColor(item.s || item.status)">
        {{ item.s || item.status }}
      </span>
    </div>
    <div class="ml-auto">
      <div v-if="this.isBuildersPath">
        <img v-if="item.pi || item.builderPrimaryLogo" class="inline-logo" :src="item.pi || item.builderPrimaryLogo" />
        <p class="text-logo" v-else>
          {{ item.builder ? item.builder.name : (item.bn || item.builder_name || '') }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import AddToFavoriteProperty from "../../PropertyFavorite/AddToFavoriteProperty";
import ScheduleListing from "../../../ScheduleListing/ScheduleListing";
import { propertyStatusColor } from '@/components/utils/helper';

export default {
  name: "PropertyListCallToActions",
  data() {
    return {
      isBuildersPath: false,
    }
  },
  props: ["item", "isFavorited", "isLoggedIn"],
  mounted() {
    let _path = this.$route.path;
    let i = _path.includes("builders");
    this.isBuildersPath = i;
  },
  computed: {
    notForSale() {
      return ['Expired', 'Withdrawn', 'Terminated', 'Sold'].includes(this.item.s || this.item.status);
    }
  },
  methods: {
    onItemFavorite(bool) {
      this.$emit("favoriteItem", bool);
    },
    propertyStatusColor(status) {
      return propertyStatusColor(status);
    },
  },
  components: {
    AddToFavoriteProperty,
    ScheduleListing
  },
};
</script>

<style scoped>
a {
  display: block;
  padding: 0px 10px;
  color: #012e55;
  text-decoration: none;
  font-weight: bold;
}

a:first-child {
  padding-left: 0px;
}
</style>
