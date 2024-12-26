<template>
  <PrimaryLayout>
    <template v-slot:contentlayout>
      <div id="content" class="container">
        <LeftSidebar></LeftSidebar>
        <div id="right" class="pl-4">
          <div class="loading-state" v-if="isLoading">
            <div class="lds-ripple">
              <div></div>
              <div></div>
            </div>
          </div>

          <div v-else>
            <div v-if="!resetSuccess">
              <div class="card border-0" v-if="tokenConfirm">
                <div class="card-header">
                  <h2 class="card-title m-0 py-2">
                    {{ $t('auth.reset_password.title') }}
                  </h2>
                </div>
                <div class="card-body">
                  <div class="col-md-7 mx-md-auto">
                    <Alert
                      v-if="alert.message"
                      :showAlert="
                        `${
                          alert.message ? true : false
                        }`
                      "
                      :alertClass="alert.type"
                      :alertText="alert.message"
                    ></Alert>

                    <h6 class="text-center mb-3">
                      {{ $t('auth.reset_password.caption') }}
                    </h6>

                    <form method="post" @submit="resetPassword">
                      <div class="form-group" :class="[errors['email'] ? 'form-error' : '']">
                        <label>
                          {{ $t('auth.reset_password.form.labels.email') }}
                        </label>
                        <input
                          type="email"
                          name="email"
                          class="form-control"
                          v-model="email"
                          autocomplete="off"
                          :disabled="true"
                        />
                      </div>

                      <div class="form-group" :class="[errors['password'] ? 'form-error' : '']">
                        <label>
                          {{ $t('auth.reset_password.form.labels.password') }}
                        </label>
                        <input
                          type="password"
                          name="password"
                          class="form-control"
                          autocomplete="off"
                          v-model="password"
                          @input="OnInput"
                        />
                      </div>

                      <div class="form-group" :class="[errors['cpassword'] ? 'form-error' : '']">
                        <label>
                          {{ $t('auth.reset_password.form.labels.password_confirmation') }}
                        </label>
                        <input
                          type="password"
                          name="cpassword"
                          class="form-control"
                          autocomplete="off"
                          v-model="cpassword"
                          @input="OnInput"
                        />
                      </div>

                      <div class="form-row mt-4 justify-content-center">
                        <div class="col-3">
                          <div class="form-group">
                            <button
                              type="submit"
                              class="btn btn-block btn-primary"
                              :disabled="isProcessing"
                            >
                              {{
                                `${
                                  isProcessing
                                    ? $t('auth.reset_password.btn_submit_processing')
                                    : $t('auth.reset_password.btn_submit')
                                }`
                              }}
                            </button>
                          </div>
                        </div>
                      </div>

                      <div class="form-group text-center">
                        <span>
                          {{ $t('auth.reset_password.text_remember') }}
                          <router-link :to="`/builders/users/signin`">
                            {{ $t('auth.reset_password.link_remember') }}
                          </router-link>
                        </span>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <div v-else>
                <h4 class="text-center py-5">
                  {{ $t('auth.reset_password.text_forgot_password', {'error': tokenErrorMsg}) }}
                  <router-link :to="`/builders/users/forgot-password`">
                    {{ $t('auth.reset_password.link_forgot_password') }}
                  </router-link>
                </h4>
              </div>
            </div>
            <div v-else>
              <h4 class="text-center py-5">
                {{ $t('auth.reset_password.text_signin') }}
                <router-link :to="`/users/signin`">
                  {{ $t('auth.reset_password.link_signin') }}
                </router-link>
              </h4>
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
import { validateAll } from "indicative/validator";
import { getValue } from "indicative-utils";
import Alert from "../../utils/Alert/Alert";
import { getErrorMessage } from "../../utils/helper";

export default {
  name: "ResetPassword",
  components: {
    PrimaryLayout,
    LeftSidebar,
    Alert
  },
  data() {
    return {
      isLoading: false,
      isProcessing: false,
      tokenConfirm: false,
      errors: {},
      tokenErrorMsg: "Invalid Reset Token.",
      resetSuccess: false,
      alert: {},
      resetToken: "",
      email: "",
      password: "",
      cpassword: ""
    };
  },
  computed: {
    formData() {
      return {
        resetToken: this.resetToken,
        email: this.email,
        password: this.password,
        cpassword: this.cpassword
      };
    }
  },
  methods: {
    confirmToken() {
      this.isLoading = true;
      axios
        .get(
          `/api/user/password/confirm-token/${this.$route.params.token}`
        )
        .then(response => {
          this.isLoading = false;
          this.resetToken = response.data.token;
          this.email = response.data.email;
          this.tokenConfirm = true;
        })
        .catch(error => {
          this.isLoading = false;
          this.tokenConfirm = false;
          this.resetToken = "";
          this.email = "";
          this.tokenErrorMsg = getErrorMessage(error);
        });
    },
    OnInput(e) {
      delete this.errors[e.target.name];
    },
    resetPassword(e) {
      e.preventDefault();
      // Validate form input
      const rules = {
        password: "required",
        cpassword: "required"
      };

      validateAll(this.formData, rules)
        .then(() => {
          this.errors = {};
          this.processReset();
        })
        .catch(errors => {
          const formattedErrors = {};
          errors.forEach(
            error => (formattedErrors[error.field] = error.message)
          );
          this.errors = formattedErrors;
        });
    },
    processReset() {
      this.isProcessing = true;
      axios
        .post(`/api/user/password-reset`, this.formData)
        .then(response => {
          this.isProcessing = false;
          this.resetSuccess = true;
          this.email = "";
        })
        .catch(error => {
          const errMessage = getErrorMessage(error);
          this.isProcessing = false;
          this.resetSuccess = false;
          this.alert = {
            type: "alert-danger text-center",
            message: errMessage
          };
        });
    }
  },
  mounted() {
    this.$Progress.start();
    this.confirmToken();
    this.$Progress.finish();
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
