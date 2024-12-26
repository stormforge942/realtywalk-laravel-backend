<template>
  <form method="post" @submit="registerUser" class="auth-form">
    <div class="mb-3">
      <span class="text-danger">*</span>) {{ $t('auth.register.required_fields') }}
    </div>

    <div class="form-group" :class="[errors['name'] ? 'form-error' : '']">
      <label>
        {{ $t('auth.register.form.labels.name') }}
        <span class="text-danger">*</span>
      </label>
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
      <label>
        {{ $t('auth.register.form.labels.email') }}
        <span class="text-danger">*</span>
      </label>
      <input
        type="email"
        name="email"
        class="form-control"
        autocomplete="off"
        v-model="email"
        @input="onInput"
      />
    </div>

    <div v-if="setPassword">
      <div class="form-group" :class="[errors['password'] ? 'form-error' : '']">
        <label>
          {{ $t('auth.register.form.labels.password') }}
        </label>
        <input
          type="password"
          name="password"
          class="form-control"
          autocomplete="off"
          v-model="password"
          @input="onInput"
        />
      </div>

      <div class="form-group" :class="[errors['cpassword'] ? 'form-error' : '']">
        <label>
          {{ $t('auth.register.form.labels.password_confirmation') }}
        </label>
        <input
          type="password"
          name="cpassword"
          class="form-control"
          autocomplete="off"
          v-model="cpassword"
          @input="onInput"
        />
      </div>
    </div>

    <a href="javascript:;" @click="setPassword = !setPassword" class="mt-3">
      <template v-if="setPassword">
        {{ $t('auth.register.dont_set_password') }}
      </template>
      <template v-else>
        {{ $t('auth.register.set_password') }}
      </template>
    </a>

    <div class="my-3">
      <p class="text-danger" v-if="needAgreement">
        {{ $t('auth.register.form.labels.aggrement_help') }}
      </p>
      <div class="form-check">
        <input
          type="checkbox"
          class="form-check-input"
          v-model="agree"
          id="checkboxAgreement"
        />
        <label class="form-check-label" for="checkboxAgreement">
          {{ $t('auth.register.form.labels.aggrement') }}
          <a href="javascript:;" @click="showTosModal">
            {{ $t('auth.register.form.labels.link_tos') }}
          </a>
          and
          <a href="javascript:;" @click="showPrivacyPolicyModal">
            {{ $t('auth.register.form.labels.link_privacy_policy') }}
          </a>.
        </label>
      </div>

      <TosModal ref="tosModal" />
      <PrivacyPolicyModal ref="privacyPolicyModal" />
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

    <div class="form-row align-items-center mt-4">
      <div class="col-3">
        <div class="form-group mb-0">
          <button
            type="submit"
            class="btn btn-block btn-primary"
            :disabled="loading"
          >
            {{ loading ? $t('auth.register.btn_submit_processing') : $t('auth.register.btn_submit') }}
          </button>
        </div>
      </div>
      <div class="col-9 pl-3">
        <span>
          {{ $t('auth.register.text_registered') }}
          <router-link :to="`/users/signin`">
            {{ $t('auth.register.link_registered') }}
          </router-link>
        </span>
      </div>
    </div>
  </form>
</template>

<script>
import { validateAll } from "indicative/validator";
import VueRecaptcha from "vue-recaptcha";
import TosModal from "../TosModal";
import PrivacyPolicyModal from "../PrivacyPolicyModal";

export default {
  name: "RegisterForm",
  props: ["onFormSubmit", "loading"],
  data() {
    return {
      errors: {},
      name: "",
      email: "",
      password: "",
      cpassword: "",
      agree: "",
      RCSitekey: process.env.MIX_RECAPTCHA_SITE_KEY,
      recaptchaResponse: null,
      verified: false,
      recaptchaError: false,
      needAgreement: false,
      setPassword: false
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
        name: this.name,
        email: this.email,
        password: this.password,
        cpassword: this.cpassword,
        'g-recaptcha-response': this.recaptchaResponse,
      };
    }
  },
  methods: {
    resetRecaptcha() {
      if (!this.$refs.recaptcha) {
        console.error('reCaptcha is not ready!')
      } else {
        this.$refs.recaptcha.reset();
      }
    },
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
    clearFormFields(bool) {
      if (bool) {
        this.resetRecaptcha();
        this.name = "";
        this.email = "";
        this.password = "";
        this.cpassword = "";
        this.agree = "";
        this.verified = false;
        this.recaptchaError = false;
        this.recaptchaResponse = null;
        this.needAgreement = false;
      }
    },
    registerUser(e) {
      e.preventDefault();
      // validate data before posting to API
      const rules = {
        name: "required",
        email: "required"
      };

      this.needAgreement = !this.agree ? true : false;
      this.recaptchaError = !this.verified ? true : false;

      validateAll(this.formData, rules)
        .then(() => {
          this.errors = {};
          this.processUserCreate();
        })
        .catch(errors => {
          const formattedErrors = {};
          errors.forEach(
            error => (formattedErrors[error.field] = error.message)
          );
          this.errors = formattedErrors;
        });
    },
    async processUserCreate() {
      if (this.needAgreement) return;
      if (this.recaptchaError) return;

      this.onFormSubmit(this.formData);
    },
    showTosModal() {
      let element = this.$refs.tosModal.$el;
      $(element).modal("show");
    },
    showPrivacyPolicyModal() {
      let element = this.$refs.privacyPolicyModal.$el;
      $(element).modal("show");
    }
  },
  components: {
    VueRecaptcha,
    TosModal,
    PrivacyPolicyModal
  }
};
</script>

<style scoped>
.btn-primary {
  background: #012e55;
  border: 0px solid;
}

.form-error input {
  border: 2px solid red;
}
</style>
