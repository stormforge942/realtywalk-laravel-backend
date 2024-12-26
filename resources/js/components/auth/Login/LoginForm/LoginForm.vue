<template>
  <form method="post" @submit="loginUser" class="auth-form">
    <div class="form-group" :class="[errors['email'] ? 'form-error' : '']">
      <label>
        {{ $t('auth.login.form.labels.email') }}
      </label>
      <input
        type="email"
        name="email"
        class="form-control"
        v-model="email"
        autocomplete="off"
      />
    </div>

    <div class="form-group" :class="[errors['password'] ? 'form-error' : '']">
      <label>
        {{ $t('auth.login.form.labels.password') }}
      </label>
      <input
        type="password"
        name="password"
        class="form-control"
        v-model="password"
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

    <div class="form-row align-items-center mt-5">
      <div class="col-xl-3">
        <div class="form-group mb-0">
          <button type="submit" class="btn btn-block btn-primary" :disabled="loading">
            {{ loading ? $t('auth.login.btn_submit_processing') : $t('auth.login.btn_submit') }}
          </button>
        </div>
      </div>

      <div class="col-xl-9">
        <div class="mt-5 mt-xl-0 mb-1 mb-xl-0">
          <span>
            <router-link :to="`/users/signin`">
              {{ $t('auth.login.link_login_without_password') }}
            </router-link>
          </span>
        </div>

        <div>
          <span>
            {{ $t('auth.login.text_remember') }}
            <router-link :to="`/users/forgot-password`">
              {{ $t('auth.login.link_remember') }}
            </router-link>
          </span>
        </div>

        <div>
          <span>
            {{ $t('auth.login.text_register') }}
            <router-link :to="`/users/create`">
              {{ $t('auth.login.link_register') }}
            </router-link>
          </span>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import { validateAll } from 'indicative/validator';
import VueRecaptcha from 'vue-recaptcha';

export default {
  name: 'LoginForm',
  props: ['onFormSubmit', 'loading'],
  data() {
    return {
      errors: {},
      email: "",
      password: "",
      RCSitekey: process.env.MIX_RECAPTCHA_SITE_KEY,
      recaptchaResponse: null,
      verified: false,
      recaptchaError: false,
    };
  },
  mounted() {
    let recaptchaScript = document.createElement("script");
    recaptchaScript.setAttribute(
      "src",
      "https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit"
    );
    document.head.appendChild(recaptchaScript);
  },
  computed: {
    formData() {
      return {
        email: this.email,
        password: this.password,
        'g-recaptcha-response': this.recaptchaResponse,
      };
    }
  },
  methods: {
    onVerify(response) {
      if (response) {
        this.verified = true;
        this.recaptchaError = false;
        this.recaptchaResponse = response;
      }
    },
    onError() {
      this.verified = false;
      this.recaptchaResponse = null;
    },
    onExpired() {
      this.verified = false;
      this.recaptchaResponse = null;
    },
    OnInput(e) {
      delete this.errors[e.target.name];
    },
    loginUser(e) {
      e.preventDefault();
      // Validate form input
      const rules = {
        email: "required",
        password: "required"
      };

      this.recaptchaError = !this.verified ? true : false;

      validateAll(this.formData, rules)
        .then(() => {
          this.errors = {};
          this.submitLogin();
        })
        .catch(errors => {
          const formattedErrors = {};
          errors.forEach(
            error => (formattedErrors[error.field] = error.message)
          );
          this.errors = formattedErrors;

        });
    },
    resetRecaptcha() {
      if (!this.$refs.recaptcha) {
        console.error('reCaptcha is not ready!')
      } else {
        this.$refs.recaptcha.reset();
      }
    },
    submitLogin() {
      if (this.recaptchaError) return;
      this.onFormSubmit(this.formData);
    }
  },
  components: {
    VueRecaptcha,
  },
};
</script>

<style scoped>
.btn-primary {
  background: #012e55;
  border: 0px solid;
}

p a {
  color: #007bff;
}

.form-error input {
  border: 2px solid red;
}
</style>
