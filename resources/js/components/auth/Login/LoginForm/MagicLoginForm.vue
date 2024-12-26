<template>
  <form method="post" @submit="sendMagicLink" class="auth-form">
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
      <div class="col-xl-4">
        <div class="form-group mb-0">
          <button type="submit" class="btn btn-block btn-primary" :disabled="loading">
            {{ loading ? $t('auth.login.btn_submit_processing') : $t('auth.login.btn_submit_magic_link') }}
          </button>
        </div>
      </div>

      <div class="col-xl-8">
        <div class="mt-5 mt-xl-0 mb-1 mb-xl-0">
          <span>
            {{ $t('auth.login.text_login_with_password') }}
            <router-link :to="`/users/signin-with-password`">
              {{ $t('auth.login.link_login_with_password') }}
            </router-link>.
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
      RCSitekey: process.env.MIX_RECAPTCHA_SITE_KEY,
      recaptchaResponse: null,
      verified: false,
      recaptchaError: false,
    };
  },
  mounted() {
    const URL = 'https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit';
    let scripts = document.getElementsByTagName('script');
    let added = false;
    for (let i = 0; i < scripts.length; i++) {
      if (scripts[i].src == URL) {
        added = true;
        break;
      }
    }
    if (added) return;
    let recaptchaScript = document.createElement('script');
    recaptchaScript.setAttribute('src', URL);
    document.head.appendChild(recaptchaScript);
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
    sendMagicLink(e) {
      e.preventDefault();
      // Validate form input
      const rules = {
        email: "required",
      };

      this.recaptchaError = !this.verified ? true : false;

      validateAll(this.formData, rules)
        .then(() => {
          this.errors = {};
          this.submitRequestMagicLink();
        })
        .catch(errors => {
          const formattedErrors = {};
          errors.forEach(
            error => (formattedErrors[error.field] = error.message)
          );
          this.errors = formattedErrors;

        });
    },
    resetForm() {
      this.email = "";
      this.resetRecaptcha();
    },
    resetRecaptcha() {
      if (!this.$refs.recaptcha) {
        console.error('reCaptcha is not ready!')
      } else {
        this.$refs.recaptcha.reset();
      }
    },
    submitRequestMagicLink() {
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
