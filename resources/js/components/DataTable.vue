<template>
  <div>
    <CDataTable
      :items="items"
      :fields="fields"
      :column-filter="{ external: true, lazy: true }"
      :sorter="{ external: true }"
      :items-per-page-select="{ external: true, values: [10, 25, 50, 100] }"
      :items-per-page="itemsPerPage"
      :loading="loading"
      :small="true"
      :fixed="true"
      class="cdatatable"
      @update:column-filter-value="filterChanged"
      @update:sorter-value="sorterChanged"
      @pagination-change="itemsPerPageChanged"
      hover
    >
      <template #actions="{ item }">
        <td class="table-actions text-center" style="white-space:nowrap;padding:0.35rem;">
          <a v-if="item.show_view_btn !== undefined ? item.show_view_btn : true"
            :href="item.page_url"
            class="btn btn-ghost-dark">
            <span v-html="$t('component.datatable.actions.btn_show')" />
          </a>
          <a v-if="item.show_edit_btn !== undefined ? item.show_edit_btn : true"
            :href="uriAction + '/' + item.id + '/edit'" class="btn btn-ghost-dark"
            :class="{ 'disabled': item.disable_edit_btn }">
            <span v-html="$t('component.datatable.actions.btn_edit')" />
          </a>
          <a v-if="item.show_delete_btn !== undefined ? item.show_delete_btn : true" href="javascript:void(0);"
            @click="deleteItem(uriAction + '/' + item.id, item)" class="btn btn-ghost-dark"
            :class="{ 'disabled': item.disable_delete_btn }">
            <span v-html="$t('component.datatable.actions.btn_delete')" />
          </a>
        </td>
      </template>

      <template #created_at="{ item }">
        <td>{{ item.created_at | dateTimeFilter }}</td>
      </template>

      <template #updated_at="{ item }">
        <td>{{ item.updated_at | dateTimeFilter }}</td>
      </template>
    </CDataTable>

    <CPagination :pages="Math.ceil(itemTotal / itemsPerPage)" :activePage="page" @update:activePage="pageChanged" />
  </div>
</template>

<script>
import { CDataTable, CPagination } from "@coreui/vue";
import dayjs from "dayjs";
import Swal from "sweetalert2";
import Noty from "noty";
import { EventBus } from './helpers';

export default {
  name: "DataTable",
  components: {
    CDataTable,
    CPagination,
  },
  props: {
    uriAction: { type: String, required: true },
    viewUri: { type: String },
    fetchUrl: { type: String, required: true },
    fields: { type: Array, required: true },
    chunkLoad: { type: Boolean, default: false },
  },
  data() {
    return {
      items: [],
      itemTotal: 0,
      itemsPerPage: 10,
      page: 1,
      loading: true,
      sorterState: null,
      columnFilter: null
    };
  },
  mounted() {
    return this.fetchData(this.fetchUrl);
  },
  filters: {
    dateTimeFilter: (date) => {
      if (!date) return null;
      return dayjs(date).format("ddd, MMM D, YYYY h:mm A");
    },
  },
  methods: {
    fetchData(url) {
      this.loading = true;
      axios.get(url + this.constructQuery()).then((response) => {
        if ("data" in response.data) {
          this.items = response.data.data;
          this.itemTotal = response.data.total;
        } else {
          this.items = response.data;
        }

        this.loading = false;
      }).catch(err => {
        if (err.response && err.response.status === 401) {
          const { dispatch } = this.$store;
          dispatch("auth/userLogout");
        }
      });
    },
    pageChanged(newValue) {
      this.page = newValue;
      this.fetchData(this.$props.fetchUrl);
    },
    filterChanged(newValue) {
      this.columnFilter = newValue;
      this.page = 1;
      this.fetchData(this.$props.fetchUrl)
    },
    sorterChanged(newValue) {
      this.sorterState = newValue;
      this.fetchData(this.$props.fetchUrl)
    },
    itemsPerPageChanged(newValue) {
      this.itemsPerPage = newValue;
      this.page = 1;
      this.fetchData(this.$props.fetchUrl)
    },
    constructQuery() {
      const queryItems = [];
      queryItems.push(`start=${(this.page - 1) * this.itemsPerPage}`);
      queryItems.push(`limit=${this.itemsPerPage}`);

      if (this.columnFilter) {
        const columns = Object.keys(this.columnFilter);
        columns.forEach((column, ix) => {
          queryItems.push(`column[${ix}][name]=${column}`);
          queryItems.push(`column[${ix}][search]=${encodeURIComponent(this.columnFilter[column])}`);
        })
      }

      if (this.sorterState) {
        queryItems.push(`order[column]=${this.sorterState.column}`);
        queryItems.push(`order[asc]=${this.sorterState.asc}`);
      }

      if (this.fetchUrl.includes('?')) {
        return "&" + queryItems.join("&")
      }

      return "?" + queryItems.join("&");
    },
    deleteItem(url, item) {
      let is_polygon = item.hasOwnProperty('is_polygon') && item.is_polygon

      Swal.fire({
        title: this.$t('component.datatable.alert.confirm_delete.title'),
        icon: "warning",
        html: is_polygon ? this.$t('component.datatable.alert.confirm_delete.message_neighborhood') : this.$t('component.datatable.alert.confirm_delete.message'),
        showCancelButton: true,
        confirmButtonText: this.$t('component.datatable.alert.confirm_delete.btn_confirm'),
        cancelButtonText: this.$t('component.datatable.alert.confirm_delete.btn_cancel'),
        confirmButtonColor: "#DD4B39",
        cancelButtonColor: "#D2D6DE",
        showLoaderOnConfirm: true,
        preConfirm: () => {
          return axios
            .delete(url, {
              headers: {
                "X-CSRF-TOKEN": window.CSRF,
              },
            })
            .then((response) => {
              return response.data;
            })
            .catch((error) => {
              let response = error.response;
              let message =
                response.status === 400 ? response.data.error.message : error;

              Swal.fire({
                title: this.$t('component.datatable.alert.error.title'),
                icon: "warning",
                html: this.$t('component.datatable.alert.error.message'),
                showCloseButton: true,
                focusConfirm: false,
                confirmButtonText: "Okay",
                confirmButtonAriaLabel: "Okay",
                cancelButtonText: "Cancel",
                confirmButtonColor: "#DD4B39",
                showLoaderOnConfirm: true,
              });
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
      }).then(
        (result) => {
          if (result.value) {
            let response = result.value;

            new Noty({
              type: "success",
              theme: "relax",
              layout: "topRight",
              text: response.message,
              timeout: 3000,
              animation: {
                open: "animated bounceInRight",
                close: "animated bounceOutRight",
              },
            }).show();

            this.fetchData(this.$props.fetchUrl);
          }
        },
        (dismiss) => {
          if (dismiss === "cancel") {
            // cancel
          }
        }
      );
    },
  }
};
</script>
