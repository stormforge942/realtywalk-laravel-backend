<template>
  <div class="saved-searches">
    <button class="mobile-close d-lg-none" @click="onClose">X</button>;
    <div v-for="search in savedSearches" :key="search.id">
      <button class="apply-search" @click="applySearch(search.id)">{{search.search_name}}</button>
      <button class="btn-remove-saved-search" @click="removeSearch(search.id)">x</button>
    </div>
  </div>
</template>

<script>

import { EventBus } from "../helpers";
import VueRouter from "vue-router";

export default {
  name: "SavedSearches",
  props: ["closePanel"],
  data() {
    return {
      savedSearches: []
    };
  },
  created() {
    EventBus.$on("saveSearch", () => {
      this.loadSearches();
    });
  },
  mounted() {
    this.loadSearches();
  },
  methods: {
    loadSearches() {
      if (this.$store.state.auth.user) {
        axios.get(`/api/user-searches/list/${this.$store.state.auth.user.id}`).then(response => {
          if (response.data) {
            this.savedSearches = response.data;
          }
        })
      }
    },
    applySearch(searchId) {
      const search = this.savedSearches.find(item => item.id === searchId);
      if (search.search_string_criteria) {
        const criteria = JSON.parse(search.search_string_criteria);
        const params = {
          polygons: criteria.polygons,
          ...criteria.homeData.filters,
        };

        if (this.$router.currentRoute && this.$router.currentRoute.path !== "/") {
          this.$router.push("/").then(() => {
            this.$store.commit('home/SET_HISTORY', criteria.homeData);
            EventBus.$emit('applySavedSearch', criteria);
            EventBus.$emit("propertiesFilter", params);
          });
        } else {
          this.$store.commit('home/SET_HISTORY', criteria.homeData);
          EventBus.$emit('applySavedSearch', criteria);
          EventBus.$emit("propertiesFilter", params);
        }
      }
      this.closePanel();
    },
    removeSearch(searchId) {
      axios.delete(`/api/user-searches/${searchId}`).then(() => {
        const index = this.savedSearches.findIndex(item => item.id === searchId);
        if (index !== -1) {
          this.savedSearches.splice(index, 1);
        }
      });
    },
    onClose() {
      this.closePanel();
    }
  }
};
</script>

<style scoped>
.saved-searches {
  height: 100%;
  width: 100%;
  padding: 50px 35px 20px 35px;
  overflow: auto;
}

.apply-search{
  color: #FFF;
  background: none;
  border: none;
  width: 94%;
  text-align: left;
}

.btn-remove-saved-search {
  color: #FFC501;
  float: right;
  background: none;
  border: none;
}

.mobile-close {
  position: absolute;
  right: 15px;
  top: 15px;
  padding: 10px;
  background: none;
  border: none;
  color: #FFC501;
  font-size: 1.5rem;
}
</style>
