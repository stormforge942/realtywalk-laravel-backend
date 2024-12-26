<template>
  <div>
    <h4 class="mb-4">
      {{ $t('schedule.listing.title') }}
    </h4>
    <form method="post" @submit="submitSchedule">
      <div class="form-group">
        <label>
          {{ $t('schedule.listing.form.labels.date') }}
          <span class="text-danger">*</span>
        </label>
        <date-picker
          v-model="schedule.scheduleDate"
          :config="dateOptions"
          :placeholder="$t('schedule.listing.form.placeholders.date')"
        >
        </date-picker>

        <p
          v-if="!$v.schedule.scheduleDate.required && submitted"
          class="text-xs text-red-500 mt-1 font-weight-bold"
        >
          {{ $t('validation.required', { attribute: 'date' }) }}
        </p>
      </div>

      <div class="form-group">
        <label>
          {{ $t('schedule.listing.form.labels.time') }}
          <span class="text-danger">*</span>
        </label>
        <date-picker
          v-model="schedule.scheduleTime"
          :config="timeOptions"
          :placeholder="$t('schedule.listing.form.placeholders.time')"
        >
        </date-picker>

        <p
          v-if="!$v.schedule.scheduleTime.required && submitted"
          class="text-xs text-red-500 mt-1 font-weight-bold"
        >
          {{ $t('validation.required', { attribute: 'time' }) }}
        </p>
      </div>

      <div class="form-group">
        <label>
          {{ $t('schedule.listing.form.labels.name') }}
          <span class="text-danger">*</span>
        </label>
        <input
          v-model="schedule.name"
          class="form-control resize-none"
          :placeholder="$t('schedule.listing.form.placeholders.name')"
        />
        <p
          v-if="!$v.schedule.name.required && submitted"
          class="text-xs text-red-500 mt-1 font-weight-bold"
        >
          {{ $t('validation.required', { attribute: 'name' }) }}
        </p>
      </div>

      <p class="text-muted">
        Fill either email or phone number below as contact information
      </p>

      <div class="form-group">
        <label>
          {{ $t('schedule.listing.form.labels.email') }}
          <span v-if="!schedule.phone_number" class="text-danger">*</span>
        </label>
        <input
          v-model="schedule.email"
          class="form-control resize-none"
          :placeholder="$t('schedule.listing.form.placeholders.email')"
          type="email"
        />
        <p
          v-if="!$v.schedule.email.required && submitted"
          class="text-xs text-red-500 mt-1 font-weight-bold"
        >
          {{ $t('validation.required', { attribute: 'email address' }) }}
        </p>
      </div>

      <div class="form-group">
        <label>
          {{ $t('schedule.listing.form.labels.phone') }}
          <span v-if="!schedule.email" class="text-danger">*</span>
        </label>
        <the-mask
          v-model="schedule.phone_number"
          name="phone_number"
          type="text"
          class="form-control"
          :mask="['### ###-####']"
          placeholder="*** ***-****"
          :class="{
            'input-error':
              submitted && $v.schedule.phone_number.$error
          }"
        />
        <p
          v-if="!$v.schedule.phone_number.required && submitted"
          class="text-xs text-red-500 mt-1 font-weight-bold"
        >
          {{ $t('validation.required', { attribute: 'phone number' }) }}
        </p>
      </div>

      <div class="form-group">
        <label>
          {{ $t('schedule.listing.form.labels.message') }}
        </label>
        <textarea
          v-model="schedule.message"
          class="form-control resize-none"
          :placeholder="$t('schedule.listing.form.placeholders.message')"
        ></textarea>
      </div>

      <div class="form-group">
        <p class="text-danger" v-if="recaptchaError">
          {{ $t('general.captcha_help') }}
        </p>
        <vue-recaptcha
          v-if="modalDisplayed"
          ref="recaptcha"
          @verify="onVerify"
          @expired="onExpired"
          @error="onError"
          :sitekey="RCSitekey">
        </vue-recaptcha>
      </div>

      <div class="mt-5">
        <p>(<span class="text-danger">*</span>) is required field</p>
      </div>

      <div class="form-group mt-5">
        <button
          type="submit"
          class="btn btn-lg btn-add fix-width"
          :disabled="isProcessing"
        >
          {{ isProcessing ? $t('schedule.listing.btn_submit_processing') : $t('schedule.listing.btn_submit') }}
        </button>
        <button
          type="button"
          class="btn btn-lg btn-remove fix-width"
          @click="closeModal"
          :disabled="isProcessing"
        >
          {{ $t('schedule.listing.btn_close') }}
        </button>
      </div>
    </form>
  </div>
</template>

<script>
import { TheMask } from "vue-the-mask";
import Datepicker from "vuejs-datetimepicker";
//import "bootstrap/dist/css/bootstrap.css";

// Import this component
import datePicker from "vue-bootstrap-datetimepicker";

// Import date picker css
import "pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css";

import { required, requiredIf } from "vuelidate/lib/validators";
//import Alert from "../../utils/Alert/Alert: requiredIf } from "vuelidate/lib/validator";
import { getErrorMessage } from "../../utils/helper";
import VueRecaptcha from 'vue-recaptcha'

export default {
  name: "ScheduleForm",
  components: {
    Datepicker,
    datePicker,
    TheMask,
    VueRecaptcha
  },
  data() {
    return {
      submitted: false,
      errors: {},
      alert: {},
      isProcessing: false,
      schedule: {
        scheduleDateTime: new Date(),
        scheduleDate: new Date(),
        scheduleTime: new Date(),
        message: "",
        name: "",
        email: "",
        phone_number: ""
      },
      dateOptions: {
        format: "YYYY-MM-DD",
        minDate: this.getDateTime(),
        useCurrent: false,
        showClear: true,
        showClose: true
      },
      timeOptions: {
        format: "h:mm a",
        minDate: this.getDateTime(),
        useCurrent: false,
        showClear: true,
        showClose: true,
        stepping: 15,
      },
      RCSitekey: process.env.MIX_RECAPTCHA_SITE_KEY,
      verified: false,
      recaptchaError: false
    };
  },
  mounted () {
    this.schedule = {
      scheduleDate: this.getDefaultDate(),
      scheduleTime: this.getDefaultTime(),
      message: ""
    };
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
  props: {
    closeModal: {
      type: Function
    },
    itemId: {
      default: ""
    },
    modalDisplayed: {
      type: Boolean,
      default: true
    }
  },
  validations: {
    schedule: {
      scheduleDate: { required },
      scheduleTime: { required },
      message: { },
      name: { required },
      email: { required: requiredIf(obj => obj.phone_number === null || obj.phone_number === '') },
      phone_number: { required: requiredIf(obj => obj.email === null || obj.email === '') }
    }
  },
  methods: {
    getDefaultDate() {
      const date = new Date;
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');

      return `${year}-${month}-${day}`;
    },
    getDefaultTime() {
      const options = {
        hour: 'numeric',
        minute: 'numeric',
        hour12: true
      };
      const formatter = new Intl.DateTimeFormat('en-US', options);
      return formatter.format(new Date);
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
    submitSchedule(e) {
      e.preventDefault();
      this.submitted = true;
      this.$v.$touch();
      if (this.$v.$invalid) {
        return;
      }
      if (! this.verified) {
        this.recaptchaError = true
        return false
      } else {
        this.recaptchaError = false
      }
      this.isProcessing = true;

      this.schedule = {
        ...this.schedule,
        property_id: this.itemId,
        scheduleDateTime: `${this.schedule.scheduleDate} ${this.schedule.scheduleTime}`
      };

      axios
        .post(`/api/property/schedule-listing`, this.schedule)
        .then(response => {
          this.isProcessing = false;
          this.submitted = false;

          this.$swal({
            icon: "success",
            title: this.$t('schedule.listing.notifications.success.title'),
            text:  this.$t('schedule.listing.notifications.success.message'),
          });

          this.schedule = {
            scheduleDate: this.getDefaultDate(),
            scheduleTime: this.getDefaultTime(),
            message: ""
          };
          setTimeout(this.closeModal(), 6000);
        })
        .catch(error => {
          this.$refs.recaptcha.reset();
          const errMessage = getErrorMessage(error);
          this.isProcessing = false;
          this.submitted = false;
          this.$swal({
            icon: "error",
            title: "There was an error",
            text: getErrorMessage(error)
          });
        });
    },
    getDateTime() {
      var now = new Date();
      var year = now.getFullYear();
      var month = now.getMonth() + 1;
      var day = now.getDate();
      var hour = now.getHours();
      var minute = now.getMinutes();
      var second = now.getSeconds();
      if (month.toString().length == 1) {
        month = "0" + month;
      }
      if (day.toString().length == 1) {
        day = "0" + day;
      }
      if (hour.toString().length == 1) {
        hour = "0" + hour;
      }
      if (minute.toString().length == 1) {
        minute = "0" + minute;
      }
      if (second.toString().length == 1) {
        second = "0" + second;
      }
      var dateTime =
        year +
        "-" +
        month +
        "-" +
        day +
        " " +
        hour +
        ":" +
        minute +
        ":" +
        second;
      return dateTime;

    }
  }
};
</script>

<style scoped>
.resize-none {
  resize: none;
}

button {
  border: 0px solid;
  color: #fff;
  padding: 2px 10px;
  box-shadow: none;
  outline: 0;
}

.fix-width{
  width: auto;
  height: auto;
}

.btn-add {
  background: #012e55;
}

.btn-remove {
  background: #dc3545;
}

button:hover {
  color: #fff;
}

button:active,
button:focus {
  box-shadow: none;
  outline: 0;
}

button:disabled {
  opacity: 0.6;
}

.datetime-picker input,
input {
  height: 45px !important;
}

.text-red-500 {
  color: #f56565;
}
</style>
