<template>
  <div class="save-this-search">
    <div class="form-group">
      <label for="SearchName">
        {{ $t('home.searches.name_search') }}
      </label>
      <input type="text" id="SearchName" v-model.trim="searchName" />
    </div>
    <button class="save_btn" @click="onSaveSearch" :disabled="loading || searchName === null || searchName === ''">
      {{ $t("home.searches.btn_save") }}
    </button>
  </div>
</template>

<script>
import { EventBus } from "../../helpers";

export default {
  name: "SaveThisSearch",
  data() {
    return {
      searchName: null,
      loading: false
    };
  },
  methods: {
    onSaveSearch() {
      const filters = {
        polygons: [],
        homeData: this.$store.getters['home/getData']
      };

      const cachedSelections = localStorage.getItem('selectedPolygons');
      filters.polygons = cachedSelections ? JSON.parse(cachedSelections) : [];

      const searchData = {
        search_name: this.searchName,
        search_string_criteria: JSON.stringify(filters),
        user_id: this.$store.state.auth.user.id
      };
      this.loading = true;
      axios.post("/api/user-searches", searchData)
        .then(response => {
          if (response.status === 200) {
            EventBus.$emit("saveSearch", searchData);
          }
        }).catch(err => {
          console.err(err);
        }).finally(() => {
          this.searchName = null;
          this.loading = false;
        });
    }
  }
};
</script>

<style scoped>
.save-this-search {
  background-color: #fff;
  width: 100%;
  padding: 1.5rem 0;
}

.save-this-search input,
.save-this-search button {
  display: block;
}

.save-this-search label,
.save-this-search input {
  width: 100%;
}

.save-this-search input {
  border: 2px solid rgb(216, 216, 216);
  padding: 0 0.75rem;
  height: 40px;
  outline: none !important;
}

.save-this-search .save_btn {
  background: #012e55;
  cursor: pointer;
  user-select: none;
  outline: none;
  box-shadow: none;
  border: 0px solid;
  color: #fff;
  padding: 12px 15px;
  min-width: 110px;
  transition: 250ms;
}

.save-this-search .save_btn:not(:disabled):hover {
  background: #013969;
}

.save-this-search .save_btn:disabled {
  background: rgb(216, 216, 216);
  color: #fff;
}

@media screen and (max-width: 575px) {
  .save-this-search {
    padding: 1.5rem 3rem;
  }

  .save-this-search label {
    font-size: 18px;
  }
}

@media screen and (max-width: 991px) {
  .save-this-search {
    padding: 2.5rem 4rem;
  }

  .save-this-search label {
    font-size: 20px;
  }

  .save-this-search .form-group {
    margin-bottom: 1.5rem;
  }

  .save-this-search .save_btn {
    min-width: 130px;
    padding: 20px 15px;
    background: #012e55;
    background: linear-gradient(180deg, #013b6e, #013b6d, #013a6c, #013869, #013665, #013462, #01325e, #01305a, #012f57, #012d54, #012c53, #012c52);
  }
}

</style>
