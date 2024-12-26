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
                {{ $t('auth.login.title') }}
              </h2>
            </div>
            <div class="card-body">
              <div class="col-md-7 mx-md-auto">
                <LoginForm
                  ref="loginForm"
                  :loading="loggingIn"
                  :onFormSubmit="onFormSubmit"
                ></LoginForm>
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
import LoginForm from "./LoginForm/LoginForm";
import Alert from "../../utils/Alert/Alert";
import { getErrorMessage } from "../../utils/helper";
import TopNavigation from '../../layout/TopNavigation';

export default {
  name: "Login",
  components: {
    PrimaryLayout,
    LeftSidebar,
    LoginForm,
    Alert,
    TopNavigation
},
  computed: {
    loggingIn () {
      return this.$store.state.auth.status.loggingIn
    },
  },
  methods: {
    async onFormSubmit (payload) {
      const { dispatch } = this.$store

      axios
        .post("/api/user/signin", payload)
        .then(response => {
          dispatch("auth/userLogin", {response})
        })
        .catch(error => {
          this.$refs.loginForm.resetRecaptcha();
          const errMessage = getErrorMessage(error)
          dispatch("auth/loginFailure", error)
          this.$swal({
            icon: "error",
            title: "There was an error",
            text: errMessage,
          })
        })
    }
  },
  mounted  (){
    this.$Progress.start()
    this.$Progress.finish()
  }
}
</script>

<style scoped>
#right {
  border-left: 0px solid;
}
</style>
