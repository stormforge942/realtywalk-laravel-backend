<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <TopNavigation />

      <div id="content" class="container">
        <div id="right">
          <div class="card border-0">
            <div class="card-header">
              <h2 class="card-title m-0 py-2">
                {{ $t('auth.forgot_password.title') }}
              </h2>
            </div>
            <div class="card-body">
              <div class="col-md-7 mx-md-auto">
                <h6 class="text-center mb-3">
                  {{ $t('auth.forgot_password.caption') }}
                </h6>

                <form method="post" @submit="forgotPassword">
                  <div class="form-group" :class="[ errors['email'] ? 'form-error' : '' ]">
                    <label>{{ $t('auth.forgot_password.form.labels.email') }}</label>
                    <input
                      type="email"
                      name="email"
                      class="form-control"
                      v-model="email"
                      autocomplete="off"
                    />
                  </div>

                  <div class="form-group">
                    <p class="text-danger" v-if="recaptchaError">
                      {{ $t('general.captcha_help') }}
                    </p>
                    <vue-recaptcha
                      ref="recaptcha"
                      @verify="onVerify"
                      @expired="onExpired"
                      @error="onError"
                      :sitekey="RCSitekey"
                    ></vue-recaptcha>
                  </div>

                  <div class="form-row mt-4 justify-content-center">
                    <div class="col-12 col-sm-5 col-md-4 col-lg-3">
                      <div class="form-group">
                        <button
                          type="submit"
                          class="btn btn-block btn-primary"
                          :disabled="isProcessing"
                        >
                          {{
                            `${
                              isProcessing
                                ? $t('auth.forgot_password.btn_submit_processing')
                                : $t('auth.forgot_password.btn_submit')
                            }`
                          }}
                        </button>
                      </div>
                    </div>
                  </div>

                  <div class="form-group text-center">
                    <span>
                      {{ $t('auth.forgot_password.text_login') }}
                      <router-link :to="`/users/signin`">
                        {{ $t('auth.forgot_password.link_login') }}
                      </router-link>
                    </span>
                  </div>
                </form>
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
import { validateAll } from "indicative/validator";
import { getErrorMessage } from "../../utils/helper";
import LeftSidebar from "../../layout/LeftSidebar";
import VueRecaptcha from "vue-recaptcha";
import TopNavigation from '../../layout/TopNavigation.vue';

export default {
  name: "ForgotPassword",
  components: {
    PrimaryLayout,
    LeftSidebar,
    VueRecaptcha,
    TopNavigation,
     // Alert
  },
  data() {
    return {
      errors: {},
      alert: {},
      email: "",
      isProcessing: false,
      RCSitekey: process.env.MIX_RECAPTCHA_SITE_KEY,
      recaptchaResponse: null,
      verified: false,
      recaptchaError: false,
    };
  },
  computed: {
    formData() {
      return {
        email: this.email,
        'g-recaptcha-response': this.recaptchaResponse,
      };
    }
  },
  methods: {
    onVerify(response) {
      if (response) {
        this.recaptchaResponse = response;
        this.verified = true;
        this.recaptchaError = false;
      }
    },
    onError() {
      this.recaptchaResponse = null;
      this.verified = false;
    },
    onExpired() {
      this.recaptchaResponse = null;
      this.verified = false;
    },
    onInput(e) {
      delete this.errors[e.target.name];
    },
    forgotPassword(e) {
      e.preventDefault();
      // Validate form input
      const rules = {
        email: "required"
      };

      this.recaptchaError = !this.verified ? true : false;

      validateAll(this.formData, rules)
        .then(() => {
          this.errors = {};
          this.sendResetLink();
        })
        .catch(errors => {
          const formattedErrors = {};
          errors.forEach(
            error => (formattedErrors[error.field] = error.message)
          );
          this.errors = formattedErrors;
        });
    },
    async sendResetLink() {
      this.isProcessing = true;
      axios
        .post(`/api/user/password/send-reset-link`, this.formData)
        .then(response => {
          this.isProcessing = false;
            this.$swal({
            icon: "success",
            title: this.$t('auth.forgot_password.notifications.sent.title'),
            text:  this.$t('auth.forgot_password.notifications.sent.message'),
          })
          this.email = "";
        })
        .catch(error => {
          const errMessage = getErrorMessage(error);
          this.isProcessing = false;
          this.$swal({
            icon: "error",
            title: this.$t('auth.forgot_password.notifications.error.title'),
            text:  errMessage,
          })
        });
    }
  },
  mounted(){
    this.$Progress.start();
    this.$Progress.finish();
    let recaptchaScript = document.createElement("script");
    recaptchaScript.setAttribute(
      "src",
      "https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit"
    );
    document.head.appendChild(recaptchaScript);
  }
};
</script>

<style scoped>
.form-control:focus {
  outline: 0px;
  box-shadow: none;
}

.btn-primary {
  background: #012e55;
  border: 0px solid;
  box-shadow: none;
}

.form-error input {
  border: 2px solid red;
}

#right {
  border-left: 0px solid;
}
</style>
