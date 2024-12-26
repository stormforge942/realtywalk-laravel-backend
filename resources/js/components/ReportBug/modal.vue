<template>
  <div class="relative">
    <h3 class="mb-4">
      Report Problem
    </h3>

    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>

    <ReportBugForm
      :onFormSubmit="onFormSubmit"
      :loading="loading"
      ref="reportBugForm"
    ></ReportBugForm>
  </div>
</template>

<script>
import ReportBugForm from "./form";
import { getErrorMessage } from "../utils/helper";

export default {
  components: {
    ReportBugForm
  },
  data() {
    return {
      loading: false,
    }
  },
  methods: {
    async onFormSubmit(payload) {
      try {
        this.loading = true;
        const res = await axios.post("/api/report-bug", payload);

        if (res.status === 200) {
          this.$refs.reportBugForm.clearFormFields(true);

          this.loading = false;

          this.$swal({
            icon: "success",
            title: "Success!",
            text:  "Your bug report has been submitted.",
          });

          this.$emit('submit');
        }
      } catch (err) {
        this.$swal({
            icon: "error",
            title: "There was an error",
            text:  getErrorMessage(err),
        });
        this.loading = false;
      }
    }
  }
}
</script>

<style scoped>
.close {
  position: absolute;
  top: 20px;
  right: 20px;
}
</style>
