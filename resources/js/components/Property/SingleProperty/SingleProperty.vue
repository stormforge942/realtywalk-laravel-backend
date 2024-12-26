<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <TopNavigation />
      <div class="page-content single-property">
        <div class="loading-state" v-if="loading">
          <div class="lds-ripple">
            <div></div>
            <div></div>
          </div>
        </div>

        <section v-else class="section-one">
          <div>
            <div v-if="propertyPhotos.length > 0" class="d-md-none">
              <div class="gallery-inner-cover-img" :style="{ backgroundImage: `url(${propertyPhotos[0].fullUrl})` }" />
              <div class="gallery-actions-mobile">
                <button @click="index = 0">
                  {{ $t("property.show.view_gallery") }}
                  <span class="gallery-icon"></span>
                </button>
              </div>
            </div>
            <div v-else class="gallery-inner-cover-img no-property-img" />
          </div>
          <LightGallery :images="propertyOnlyPics" :index="index" :disableScroll="true" class="light-gallery"
            @close="index = null" />
          <div v-if="propertyPhotos.length > 0" class="property-gallery d-none d-md-block overflow-hidden">
            <div class="property-gallery-inner" :style="{ transform: `translateX(calc(33vw * -${activePhoto}))` }">
              <div class="property-gallery-item" v-for="(photo, photoIndex) in propertyPhotos" :key="photo.id"
                :style="{ backgroundImage: `url(${photo.fullUrl})` }" @click="index = photoIndex"></div>
            </div>
            <button v-if="activePhoto !== 0" class="gallery-control-prev d-xs-none" @click="--activePhoto">
              <svg viewBox="0 0 25 40">
                <polyline data-v-2951e615="" points="19 5 5 20 19 35" stroke-width="3" stroke-linecap="butt" fill="none"
                  stroke-linejoin="round" stroke="#DEC417"></polyline>
              </svg>
              <span class="sr-only">Previous</span>
            </button>
            <button v-if="activePhoto + 3 !== propertyPhotos.length" class="gallery-control-next d-xs-none"
              @click="++activePhoto">
              <svg viewBox="0 0 25 40">
                <polyline data-v-2951e615="" points="6 5 20 20 6 35" stroke-width="3" stroke-linecap="butt" fill="none"
                  stroke-linejoin="round" stroke="#DEC417"></polyline>
              </svg>
              <span class="sr-only">Next</span>
            </button>
          </div>
        </section>

        <section v-if="!loading" id="section-two">
          <div class="features-box row">
            <div class="col-12">
              <div class="card-body">
                <div>
                  <div class="property-actions">
                    <PropertyFavorite :isLoggedIn="isLoggedIn" :favorited="isFavorite" :itemId="property.id"
                      @favoriteItem="onItemFavorite"></PropertyFavorite>
                    <ScheduleListing :isLoggedIn="isLoggedIn" :itemId="property.id" :classBtn="'ml-2'" />
                  </div>
                  <div class="card-title">
                    <h2>
                      {{ $t("property.show.features.title") }}
                    </h2>
                    <span class="main-detail">
                      {{ formattedPrice }}
                    </span>
                    <span class="main-detail">
                      {{`${property.full_address}`}}
                    </span>
                  </div>
                </div>
                <div class="row">
                  <ul class="inner-features col-10">
                    <li>
                      {{ property.bedrooms }}
                      {{ $t("property.show.features.bedrooms") }}
                    </li>
                    <li>
                      {{
                        $t("property.show.features.bathrooms", {
                          n: property.bathrooms_full,
                          h: property.bathrooms_half,
                        })
                      }}
                    </li>
                    <li>
                      {{ property.sqft }}
                      {{ $t("property.show.features.sqft") }}
                    </li>
                    <li v-if="property.garage_capacity">
                      {{ $t("property.show.features.garage_capacity") }}
                      {{ property.garage_capacity }}
                    </li>
                    <li v-if="property.stories">
                      {{ $t("property.show.features.stories") }}
                      {{ property.stories }}
                    </li>
                    <li>
                      {{ $t("property.show.features.year_built") }}
                      {{ property.year_built }}
                    </li>
                    <li>
                      {{ $t("property.show.features.neighborhood") }}
                      <a v-if="property.neighborhood != 'N/A'" :href="property.neighborhood_path
                          ? property.neighborhood_path
                          : '/neighborhood/' + property.neighborhood_slug
                        ">
                        {{ property.neighborhood }}
                      </a>
                      <span v-else>{{ property.neighborhood }}</span>
                    </li>
                    <li>
                      {{ $t("property.show.features.subdivision") }}
                      <a v-if="property.subdivision != 'N/A'" :href="property.subdivision_path
                          ? property.subdivision_path
                          : '/neighborhood/' + property.subdivision_slug
                        ">
                        {{ property.subdivision }}
                      </a>
                      <span v-else>{{ property.subdivision }}</span>
                    </li>
                    <li v-if="property.office_name">
                      {{ $t("property.show.features.office") }}
                      <a v-if="property.office_website" :href="property.office_website">{{ property.office_name }}</a>
                      <span v-else>{{ property.office_name }}</span>
                    </li>
                    <li>
                      {{ $t("property.show.features.zipcode") }}
                      {{ property.zipcode }}
                    </li>
                    <li>
                      {{ $t("property.show.features.mls_number") }}
                      {{ property.mls_number }}
                    </li>
                    <li>
                      {{ $t("property.show.features.fees_and_taxes") }} N/A
                    </li>
                    <li>
                      {{ $t("property.show.features.status") }}
                      <span class="badge property-badge" :class="propertyStatusColor(property.status)">
                        {{ property.status }}
                      </span>
                    </li>
                    <li>
                      {{ $t("property.show.features.broker") }}
                      {{ property.broker || '–' }}
                    </li>
                  </ul>
                  <div class="col-md-2 builder-logo">
                    <img v-if="builderLogo" class="inline-logo" style="max-width: 300px"
                      :src="builderLogo" />
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row property-desc">
            <div class="col-lg-6 col-md-12 py-4">
              <div class="property-desc-content">
                <div class="video-embed">
                  {{ property.video_embed }}
                </div>
                <div v-html="property.descr">
                  {{ property.descr }}
                </div>
              </div>
            </div>

            <div class="property-map col-lg-6 col-md-12">
              <iframe width="100%" height="450"
                :src="`https://maps.google.com/maps?width=100%&amp;height=450&amp;hl=en&amp;q=${property.full_address_with_zip}+(${property.full_address_with_zip})&amp;output=embed`"
                frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
            </div>
          </div>
        </section>
      </div>
    </template>
  </PrimaryLayout>
</template>

<script>
import PrimaryLayout from "../../layout/PrimaryLayout/PrimaryLayout";
import { LightGallery } from "vue-light-gallery";
import TopNavMember from "../../layout/TopNavMember";
import TopNavigation from "../../layout/TopNavigation";
import PropertyFavorite from "../PropertyFavorite/AddToFavoriteProperty";
import ScheduleListing from "../../ScheduleListing/ScheduleListing";
import { propertyStatusColor } from '@/components/utils/helper';

export default {
  name: "SingleProperty",
  components: {
    LightGallery,
    PrimaryLayout,
    TopNavMember,
    TopNavigation,
    PropertyFavorite,
    ScheduleListing,
  },
  computed: {
    isLoggedIn() {
      return this.$store.state.auth.status.loggedIn;
    },
    isFavorite() {
      return this.isFavorited;
    },
    favorites() {
      return this.$store.getters['auth/favorites'];
    },
    formattedPrice: function () {
      if (
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
      return this.$t("general.text_tbd");
    },
  },
  watch: {
    // call method again if route changes
    $route: "loadProperty",
  },
  data() {
    return {
      property: {},
      builder: {},
      loading: true,
      activePhoto: 0,
      thumbnails: [],
      builderLogo: null,
      propertyOnlyPics: [],
      propertyPhotos: [
        { fullUrl: "/images/property_no_img_thumb.png" }
      ],
      index: null,
      isFavorited: false,
      propertyPath: this.$route.params.path,
    };
  },

  methods: {
    propertyStatusColor(status) {
        return propertyStatusColor(status);
    },
    dataIndex(index) {
      this.index = index;
    },
    showMore(e) {
      e.preventDefault();

      //let gallery = document.querySelector("#gallery-desktop");
      //gallery.style.height= "auto";
    },
    showMoreMobile(e) {
      e.preventDefault();
      //let gallery = document.querySelector("#gallery-mobile");
      //gallery.style.height= "auto";
    },
    loadProperty() {
      this.$Progress.start();
      axios
        .get("/api/property/" + this.propertyPath)
        .then((data) => {
          this.property = data.data;

          if (this.property.type == 1 && data.data.media) {
            this.propertyPhotos = data.data.media;
            this.propertyPhotos.map((photo) => {
              this.propertyOnlyPics.push(photo.fullUrl);
            });
          } else if (this.property.type == 0 && data.data.image_urls) {
            this.propertyPhotos = data.data.image_urls.map(url => {
              this.propertyOnlyPics.push(url);
              return {
                id: Math.floor(Math.random() * 99999),
                fullUrl: url
              };
            });
          }

          if (this.property.builder) {
            this.builderLogo = data.data.builder.media.find(
              (m) => m.collection_name == 'builder_logo'
            )?.fullUrl;
          }

          document.title = `${this.property.full_address} - Realty WALK`;
          this.builder = data.data.builder;
          this.isFavorited = this.favorites.some(f => f.id === data.data.id && f.conn === 'realty');
          this.$Progress.finish();
          this.loading = false;
        })
        .catch((err) => {
          console.log(err);
          if (err.response && err.response.status === 404) {
            window.location.href = "/404";
            this.$Progress.finish();
          }
        });
    },
    onItemFavorite(bool) {
      this.isFavorited = bool;
    },
    sortMedia(media) {
      media.sort((a, b) => {
        return a.order_column - b.order_column;
      });
    },
  },
  async mounted() {
    this.loadProperty();
  },
};
</script>
