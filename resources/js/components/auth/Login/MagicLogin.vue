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
                <div v-if="loggingIn" class="d-flex align-items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24">
                    <g stroke="currentColor">
                      <circle cx="12" cy="12" r="9.5" fill="none" stroke-linecap="round" stroke-width="3">
                        <animate attributeName="stroke-dasharray" calcMode="spline" dur="1.5s" keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" keyTimes="0;0.475;0.95;1" repeatCount="indefinite" values="0 150;42 150;42 150;42 150"/>
                        <animate attributeName="stroke-dashoffset" calcMode="spline" dur="1.5s" keySplines="0.42,0,0.58,1;0.42,0,0.58,1;0.42,0,0.58,1" keyTimes="0;0.475;0.95;1" repeatCount="indefinite" values="0;-16;-59;-59"/>
                      </circle>
                      <animateTransform attributeName="transform" dur="2s" repeatCount="indefinite" type="rotate" values="0 12 12;360 12 12"/>
                    </g>
                  </svg>

                  <span class="ml-2">Signing in using magic login. Please wait!</span>
                </div>

                <MagicLoginForm
                  v-else
                  ref="magicLoginForm"
                  :loading="loading"
                  :onFormSubmit="onFormSubmit"
                ></MagicLoginForm>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </PrimaryLayout>
</template>

<script>
import { getErrorMessage } from "../../utils/helper";
import PrimaryLayout from "../../layout/PrimaryLayout/PrimaryLayout";
import LeftSidebar from "../../layout/LeftSidebar";
import MagicLoginForm from "./LoginForm/MagicLoginForm";
import Alert from "../../utils/Alert/Alert";
import TopNavigation from '../../layout/TopNavigation';

export default {
  name: "Login",
  components: {
    PrimaryLayout,
    LeftSidebar,
    MagicLoginForm,
    Alert,
    TopNavigation
  },
  data() {
    return {
      loading: false,
      loggingIn: false
    }
  },
  computed: {
    isLoggedIn() {
      return this.$store.state.auth.status?.loggedIn || false;
    },
  },
  methods: {
    async onFormSubmit (payload) {
      this.loading = true;
      axios
        .post("/api/user/send-magic-login", payload)
        .then(response => {
          this.loading = false;
          this.$swal({
            icon: "success",
            title: "Magic Link Sent!",
            text: 'Your magic link to sign in to RealtyWalk site has been sent to your email!',
          }).then(result => {
            if (result.isConfirmed) {
              this.$refs.magicLoginForm.resetForm();
            }
          });
        })
        .catch(error => {
          this.$refs.magicLoginForm.resetRecaptcha();
          const errMessage = getErrorMessage(error);
          this.loading = false;
          this.$swal({
            icon: "error",
            title: "There was an error",
            text: errMessage,
          });
        });
    },
    async attemptMagicLogin() {
      const { token } = this.$route.params;
      const { dispatch } = this.$store;

      axios
        .post(`/api/user/magic-login/${token}`)
        .then(response => dispatch("auth/userLogin", { response }))
        .catch(error => {
          dispatch("auth/loginFailure", error)
          this.$swal({
            icon: "error",
            title: "There was an error!",
            text: getErrorMessage(error),
            confirmButtonText: 'Continue'
          }).then(result => {
            if (result.isConfirmed) {
              window.location.href = '/users/signin';
            }
          });
        });
    }
  },
  async mounted() {
    if (this.isLoggedIn) {
      this.$router.push('/');
    } else if (this.$route.params.token !== undefined) {
      this.loggingIn = true;
      await this.attemptMagicLogin();
    } else {
      this.loggingIn = false;
    }
  },
  destroyed() {
    this.loggingIn = false;
  }
}
</script>

<style scoped>
#right {
  border-left: 0px solid;
}
</style>
