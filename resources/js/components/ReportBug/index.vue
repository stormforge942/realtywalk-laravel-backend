<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <TopNavigation />
      <div id="content" class="container">
        <LeftSidebar></LeftSidebar>

        <div id="right" class="pl-4">
          <div class="card border-0">
            <div class="card-header">
              <h2 class="card-title m-0 py-2">Report Bug</h2>
            </div>
            <div class="card-body">
              <div class="col-md-7 mx-md-auto">
                <ReportBugForm
                  :onFormSubmit="onFormSubmit"
                  :loading="loading"
                  ref="reportBugForm"
                ></ReportBugForm>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </PrimaryLayout>
</template>

<script>
import { getErrorMessage, scrollTop } from "../utils/helper";
import PrimaryLayout from "../layout/PrimaryLayout/PrimaryLayout";
import LeftSidebar from "../layout/LeftSidebar";
import TopNavigation from "../layout/TopNavigation";
import ReportBugForm from "./form";

export default {
  name: "ReportBug",
  components: {
    PrimaryLayout,
    LeftSidebar,
    ReportBugForm,
    TopNavigation,
  },
  data() {
    return {
      loading: false,
      alertMessage: "",
      alertClass: "",
      showAlert: false
    };
  },
  methods: {
    async onFormSubmit(payload) {
      try {
        this.loading = true;
        this.showAlert = false;
        this.alertMessage = "";
        const res = await axios.post("/api/report-bug", payload);

        if (res.status === 200) {
          this.$refs.reportBugForm.clearFormFields(true);
          this.loading = false;
          scrollTop();

          this.$swal({
            icon: "success",
            title: "Success!",
            text:  "Your bug report has been submitted.",
          });
        }
      } catch (err) {
        this.$swal({
            icon: "error",
            title: "There was an error",
            text:  getErrorMessage(err),
        });
        this.loading = false;
        scrollTop();
      }
    }
  },
  mounted() {
    this.$Progress.start();
    this.$Progress.finish();
  }
};
</script>

<style scoped>
.btn-primary {
  background: #012e55;
  border: 0px solid;
}

.loading-state {
  min-height: 450px;
}

#right {
  border-left: 0px solid;
}
</style>
