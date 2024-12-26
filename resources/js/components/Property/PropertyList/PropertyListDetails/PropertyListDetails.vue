<template>
  <div class="relative" v-if="item">
    <div class="listing-header" :class="[bgheader === true ? 'has-bg' : '']">
      <div class="row">
        <div class="detail col-md-2 mb-2 mb-md-0">
          <h6 class="">
            {{ $t('property.list_details.price') }}
            {{ formatPrice(item) }}
          </h6>
        </div>
        <div class="detail col-md-3 col-3">
          <div class="d-flex">
            <h6 class="text-capitalize" v-if="item.pu || item.alt_path_url">
              {{ $t('property.list_details.address') }}
              <router-link :to="item.pu || item.alt_path_url">
                {{ item.fa || item.full_address }}
              </router-link>
            </h6>

            <h6 class="status">
              {{ $t('property.list_details.status') }}

              <span class="badge property-badge" :class="propertyStatusColor(item.s || item.status)">
                {{ item.s || item.status || 'N/A' }}
              </span>
            </h6>
          </div>
        </div>
        <div class="detail col-md-3 col-3">
          <div class="d-flex">
            <h6 v-if="item.pp" class="text-capitalize">
                <!-- change to neighborhood--->
                {{ $t('property.list_details.sub') }}
                <router-link :to="item.pp">
                    {{item.pn}}
                </router-link>
            </h6>
            <h6 v-else-if="item.polygon && item.polygon.ancestors && item.polygon.ancestors.length > 0"
              class="text-capitalize">
              {{ $t('property.list_details.sub') }}
              <router-link
                :to="item.polygon.ancestors[item.polygon.ancestors.length - 1].path_url">
                {{ item.polygon.ancestors[item.polygon.ancestors.length - 1].title }}
              </router-link>
            </h6>
            <h6 v-else-if="item.polygon" class="text-capitalize">
              {{ $t('property.list_details.sub') }}
              <router-link v-if="item.polygon.path_url" :to="item.polygon.path_url">
                {{ item.polygon.title }}
              </router-link>

              <a v-else href="javascript:void(0)">
                {{ item.polygon.title }}
              </a>
            </h6>
          </div>
        </div>
      </div>
    </div>

    <div class="listing-desc" :class="[bgheader === true ? 'has-bg-paragraph-text' : '']">
      <p>
        {{ getDescription(item) }}
      </p>
    </div>
  </div>
</template>

<script>
import { propertyStatusColor } from '@/components/utils/helper';

export default {
  name: "PropertyListDetails",
  props: ["item", "bgheader"],
  methods: {
    propertyStatusColor(status) {
      return propertyStatusColor(status);
    },
    formatPrice: function (property) {
      if (property && property.pfi && property.pf !== null) {
        return (property.pfi === 2 && property.pt !== null)
          ? `$${property.pf.toLocaleString()} - $${property.pt.toLocaleString()}`
          : `$${property.pf.toLocaleString()}`;
      } else if (property && property.price_format_id && property.price_from !== null) {
        return (property.price_format_id === 2 && property.price_to !== null)
          ? `$${property.price_from.toLocaleString()} - $${property.price_to.toLocaleString()}`
          : `$${property.price_from.toLocaleString()}`;
      }

      return this.$t('property.list_details.to_be_determined_abbr'); //price_format_id === 3 or bad data
    },
    getDescription(item) {
      const html = item.descr || item.dsc;
      if (html === null) return '';
      let tmp = document.createElement('DIV');
      tmp.innerHTML = html;
      let text = tmp.textContent || tmp.innerText || '';
      return text.length > 194 ? `${text.substring(0, 194)}...` : text;
    },
  },
};
</script>

<style scoped>
.relative {
  position: relative;
}

.listing-header {
  border-bottom: 1px solid #012e55;
}

.listing-desc {
  margin-bottom: 40px;
}

.inline-logo {
  width: 60px;
  position: absolute;
  right: 2%;
  top: 3rem;
}

.text-logo {
  width: 70px;
  position: absolute;
  bottom: -50px;
  right: 2%;
  font-weight: bold;
}

.row,
.detail {
  padding-left: 0px;
}

.detail .status {
    font-weight: bold;
    color: #000;
    padding-left: 15px;
}

.has-bg {
  border-bottom: 0px solid !important;
  background: #d7e4ea !important;
  margin-bottom: 10px;
}

.has-bg .row {
  justify-content: flex-start;
  padding: 8px 8px 0 8px;
  margin-left: auto !important;
}

.has-bg h6 {
  color: #315e84;
}

.has-bg-paragraph-text p {
  color: #315e84 !important;
}
</style>
