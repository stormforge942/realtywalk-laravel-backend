<template>
  <form method="post" @submit="submit" class="auth-form">
    <div class="form-group" :class="[errors['name'] ? 'form-error' : '']">
      <label>Name</label>
      <input
        type="text"
        name="name"
        class="form-control"
        autocomplete="off"
        v-model="name"
        @input="onInput"
      />
    </div>

    <div class="form-group" :class="[errors['email'] ? 'form-error' : '']">
      <label>Email</label>
      <input
        type="email"
        name="email"
        class="form-control"
        autocomplete="off"
        v-model="email"
        @input="onInput"
      />
    </div>

    <div class="form-group" :class="[errors['url'] ? 'form-error' : '']">
      <label>URL</label>
      <input
        type="text"
        name="url"
        class="form-control"
        autocomplete="off"
        v-model="url"
        @input="onInput"
      />
    </div>

    <div class="form-group" :class="[errors['body'] ? 'form-error' : '']">
      <label>Bug Information</label>
      <textarea
        type="text"
        name="body"
        class="form-control"
        autocomplete="off"
        v-model="body"
        @input="onInput"
      ></textarea>
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
        :sitekey="RCSitekey">
      </vue-recaptcha>
    </div>

    <div class="form-row align-items-center mt-4">
      <div class="col-sm-3">
        <div class="form-group mb-0">
          <button
            type="submit"
            class="btn btn-block btn-primary"
            :disabled="loading"
          >
            {{ loading ? "Please wait..." : "Submit" }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import { validateAll } from "indicative/validator";
import VueRecaptcha from 'vue-recaptcha'

export default {
  name: "ReportBugForm",
  props: ["onFormSubmit", "loading"],
  data() {
    return {
      errors: {},
      name: "",
      email: "",
      url: window.location.href,
      body: "",
      RCSitekey: process.env.MIX_RECAPTCHA_SITE_KEY,
      verified: false,
      recaptchaError: false
    };
  },
  mounted () {
    let recaptchaScript = document.createElement('script')
    recaptchaScript.setAttribute('src', 'https://www.google.com/recaptcha/api.js?onload=vueRecaptchaApiLoaded&render=explicit')
    document.head.appendChild(recaptchaScript)
  },
  computed: {
    formData() {
      return {
        name: this.name,
        email: this.email,
        url: this.url,
        body: this.body
      };
    }
  },
  methods: {
    onInput(e) {
      delete this.errors[e.target.name];
    },
    onVerify (response) {
      if (response) {
        this.verified = true
        this.recaptchaError = false
      }
    },
    onError() {
      this.verified = false
    },
    onExpired () {
      this.verified = false
    },
    clearFormFields(bool) {
      if (bool) {
        this.resetRecaptcha();

        this.name = "";
        this.email = "";
        this.url = "";
        this.body = "";
      }
    },
    submit(e) {
      e.preventDefault();
      // validate data before posting to API
      const rules = {
        name: "required",
        email: "required|email",
        url: "required|url",
        body: "required"
      };

      validateAll(this.formData, rules)
        .then(() => {
          this.errors = {};
          this.processUserCreate();
        })
        .catch(errors => {
          const formattedErrors = {};
          this.$refs.recaptcha.reset();
          errors.forEach(
            error => (formattedErrors[error.field] = error.message)
          );
          this.errors = formattedErrors;
        });
    },
    resetRecaptcha() {
      this.$refs.recaptcha.reset();
    },
    async processUserCreate() {
      if (! this.verified) {
        this.recaptchaError = true
        return false
      } else {
        this.recaptchaError = false
      }

      this.onFormSubmit(this.formData);
    }
  },
  components: {
    VueRecaptcha
  }
};
</script>

<style scoped>
.btn-primary {
  background: #012e55;
  border: 0px solid;
}

.form-error input,
.form-error textarea {
  border: 2px solid red;
}
</style>
