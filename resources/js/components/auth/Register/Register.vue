<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <TopNavigation />
      <div id="content" class="container">
        <LeftSidebar></LeftSidebar>
        <div id="right" class="pl-4">
          <div class="card border-0">
            <div class="card-header">
              <h2 class="card-title m-0 py-2">
                {{ $t('auth.register.title') }}
              </h2>
            </div>
            <div class="card-body">
              <div class="col-md-7 mx-md-auto">
                <RegisterForm
                  :onFormSubmit="onFormSubmit"
                  :loading="loading"
                  ref="registerForm"
                ></RegisterForm>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </PrimaryLayout>
</template>

<script>
import PrimaryLayout from "../../layout/PrimaryLayout/PrimaryLayout";
import LeftSidebar from "../../layout/LeftSidebar";
import RegisterForm from "./RegisterForm/RegisterForm";
import { getErrorMessage, scrollTop } from "../../utils/helper";
import TopNavigation from '../../layout/TopNavigation';

export default {
  name: "Register",
  components: {
    PrimaryLayout,
    LeftSidebar,
    RegisterForm,
    TopNavigation
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
        this.loading = true
        this.showAlert = false
        this.alertMessage = ""
        const res = await axios.post("/api/user/create", payload)

        if (res.status === 200) {
          this.$refs.registerForm.clearFormFields(true)
          this.loading = false
          scrollTop()

          this.$swal({
            icon: "success",
            title: this.$t('auth.register.notifications.success.title'),
            text:  this.$t('auth.register.notifications.success.message'),
          })
        } else {
          this.$refs.registerForm.resetRecaptcha();
        }
      } catch (err) {
        this.$swal({
            icon: "error",
            title: this.$t('auth.register.notifications.error.title'),
            text:  getErrorMessage(err),
        })
        this.$refs.registerForm.resetRecaptcha();
        this.loading = false
        scrollTop()
      }
    }
  },
  mounted() {
    this.$Progress.start()
    this.$Progress.finish()
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
