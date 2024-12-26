<template>
  <div>
    <div class="property-list">
      <div class="property-picture-sm">
        <router-link v-if="property.pu || property.alt_path_url" class="property-pic-link" :to="property.pu || property.alt_path_url">
          <img :src="listImageUrl" :alt="`${property.fa || property.full_address} Thumbnail`" />
        </router-link>

        <a v-else  href="javascript:void(0)" class="property-pic-link">
          <img :src="listImageUrl" :alt="`${property.fa || property.full_address} Thumbnail`" />
        </a>
      </div>

      <div class="property-details">
        <div class="d-flex justify-content-between align-items-stretch">
          <div class="">
            <div class="property-desc">
              <h3>{{ formattedPrice }}</h3>

              <h6 class="text-capitalize">
                {{ $t("home.result.item.address") }}
                <router-link v-if="property.pu || property.alt_path_url" :to="property.pu || property.alt_path_url">
                  {{ property.fa || property.full_address }}
                </router-link>

                <a v-else href="javascript:void(0)">
                  {{ property.fa || property.full_address }}
                </a>
              </h6>

              <h6 class="status">
                {{ $t('property.list_details.status') }}


                <span class="badge property-badge" :class="propertyStatusColor(property.s || property.status)">
                  {{ property.s || property.status || 'N/A' }}
                </span>
              </h6>

              <h6
                v-if="property.pp"
                class="text-capitalize"
              >
                <!-- change to neighborhood--->
                {{ $t("home.result.item.subdivision") }}
                <router-link v-if="property.pp" :to="property.pp">
                  {{ property.pn }}
                </router-link>

                <a v-else href="javascript:void(0)">
                  {{ property.pn }}
                </a>
              </h6>
              <h6 v-else-if="property.polygon &&
                property.polygon.ancestors &&
                property.polygon.ancestors.length > 0
                " class="text-capitalize">
                <!-- change to neighborhood--->
                {{ $t("home.result.item.subdivision") }}
                <router-link
                  v-if="property.polygon.ancestors[property.polygon.ancestors.length - 1].path_url"
                  :to="property.polygon.ancestors[property.polygon.ancestors.length - 1].path_url"
                >
                  {{ property.polygon.ancestors[property.polygon.ancestors.length - 1].title }}
                </router-link>

                <a v-else href="javascript:void(0)">
                  {{ property.polygon.ancestors[
                    property.polygon.ancestors.length - 1
                  ].title }}
                </a>
              </h6>
              <h6 v-else-if="property.polygon" class="text-capitalize">
                {{ $t("home.result.item.subdivision") }}
                <router-link v-if="property.polygon.path_url" :to="property.polygon.path_url">
                  {{ property.polygon.title }}
                </router-link>

                <a v-else href="javascript:void(0)">
                  {{ property.polygon.title }}
                </a>
              </h6>

              <h6 v-if="property.builder_id">
                {{ $t("home.result.item.builder") }}
                <router-link v-if="(property.builder && property.builder.path_url) || property.bp || property.builder_path" :to="(property.builder && property.builder.path_url) || property.bp || property.builder_path">
                  {{ (property.builder && property.builder.name) || property.bn || property.builder_name || '' }}
                </router-link>

                <a v-else href="javascript:void(0)">
                  {{ (property.builder && property.builder.name) || property.bn || property.builder_name || '' }}
                </a>
              </h6>

              <h6 v-else-if="property.bn || property.builder_name">
                {{ property.bn || property.builder_name }}
              </h6>
            </div>
          </div>

          <div class="">
            <div class="right-buttons">
              <div class="">
                <Favorite
                  :isLoggedIn="isLoggedIn"
                  :favorited="isFavorite"
                  :itemId="property.pri || property.id"
                  @favoriteItem="onItemFavorite"
                ></Favorite>

                <button class="calendar-button" @click="showModal">
                  <img class="calendar" src="/images/calendar.png" />
                </button>

                <Modal ref="modal" :showCloseBtn="false" @closeModal="closeModal">
                  <template v-slot:body>
                    <div class="modal-body">
                      <ScheduleForm
                        :modal-displayed="modalDisplayed"
                        :itemId="property.pri || property.id"
                        :closeModal="closeModal"
                      />
                    </div>
                  </template>
                </Modal>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Favorite from "../../../Favorite/Favorite";
import $ from "jquery";
import Modal from "../../../utils/Modal/Modal";
import ScheduleForm from "../../../ScheduleListing/ScheduleForm/ScheduleForm";
import { propertyStatusColor } from '@/components/utils/helper';

export default {
  name: "HomePropertyListSm",
  props: ["property", "isLoggedIn"],
  components: {
    Favorite,
    Modal,
    ScheduleForm,
  },
  data() {
    return {
      isFavorited: false,
      listImageUrl: `/images/property_no_img_thumb.png`,
      modalDisplayed: false,
    };
  },
  computed: {
    isFavorite() {
      const id = this.property?.pri || this.property?.id;
      return this.favorites.some(f => f.id === id && f.conn === 'realty');
    },
    favorites() {
      return this.$store.getters['auth/favorites'];
    },

    formattedPrice: function () {
      if (
        this.property &&
        this.property.pfi &&
        this.property.pf !== null
      ) {
        if (
          this.property.pfi === 2 &&
          this.property.pt !== null
        ) {
          return `$${this.property.pf.toLocaleString()} - $${this.property.pt.toLocaleString()}`;
        } else if (this.property.pfi === 1) {
          return `$${this.property.pf.toLocaleString()}`;
        }
      } else if (
        this.property &&
        this.property.price_format_id &&
        this.property.price_from !== null
      ) {
        if (
          this.property.price_format_id === 2 &&
          this.property.price_to !== null
        ) {
          return `$${this.property.price_from.toLocaleString()} - $${this.property.price_to.toLocaleString()}`;
        } else if (this.property.price_format_id === 1) {
          return `$${this.property.price_from.toLocaleString()}`;
        }
      }
      return "TBD"; //price_format_id === 3 or bad data
    },
  },
  methods: {
    propertyStatusColor(status) {
      return propertyStatusColor(status);
    },
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
        this.listImageUrl = `${mainMedia.fullUrl}`;
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
    showModal() {
      let element = this.$refs.modal.$el;
      this.modalDisplayed = true;
      $(element).modal("show");
    },
    closeModal() {
      let element = this.$refs.modal.$el;
      this.modalDisplayed = false;
      $(element).modal("hide");
    },
  },
  created() {
    const id = this.property?.pri || this.property?.id;
    this.isFavorited = this.favorites.some(f => f.id === id && f.conn === 'realty');
    this.normalizeMedia(this.property);
  },
};
</script>

<style lang="scss" scoped>
.property-picture-sm {
  height: 100%;
}

.property-pic-link {
  display: block;
  width: 100%;
  height: 100%;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
  }
}

@media screen and (max-width: 768px) {
  .property-picture-sm {
    height: auto;
  }

  .property-pic-link {
    width: 100%;
    padding-top: 100%;
    position: relative;

    img {
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      right: 0;
    }
  }
}
</style>
