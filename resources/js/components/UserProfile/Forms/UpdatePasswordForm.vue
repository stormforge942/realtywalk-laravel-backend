<template>
  <form method="POST" @submit="updatePassword">
    <div class="form-group" :class="[errors['currentPassword'] ? 'form-error' : '']">
      <label>
        {{ $t('profile.user.form.labels.current_password') }}
      </label>
      <input
        type="password"
        class="form-control"
        name="currentPassword"
        autocomplete="off"
        placeholder="*******"
        v-model="data.currentPassword"
        @input="OnInput"
      />
      <span class="error-text">
        {{ errors["currentPassword"] && errors["currentPassword"] }}
      </span>
    </div>

    <div class="form-group" :class="[errors['newPassword'] ? 'form-error' : '']">
      <label>
        {{ $t('profile.user.form.labels.new_password') }}
      </label>
      <input
        type="password"
        class="form-control"
        name="newPassword"
        autocomplete="off"
        placeholder="*******"
        v-model="data.newPassword"
        @input="OnInput"
      />
      <span class="error-text">
        {{ errors["newPassword"] && errors["newPassword"] }}
      </span>
    </div>

    <div class="form-group" :class="[errors['confirmPassword'] ? 'form-error' : '']">
      <label>
        {{ $t('profile.user.form.labels.password_confirmation') }}
      </label>
      <input
        type="password"
        class="form-control"
        name="confirmPassword"
        autocomplete="off"
        placeholder="*******"
        v-model="data.confirmPassword"
        @input="OnInput"
      />
      <span class="error-text">
        {{ errors["confirmPassword"] && errors["confirmPassword"] }}
      </span>
    </div>

    <div class="form-group mt-4">
      <div class="col-4 col-sm-3 px-0">
        <button type="submit" class="btn btn-block btn-primary" :disabled="loading">
          {{ loading ? $t('profile.user.btn_submit_processing') : $t('profile.user.btn_submit') }}
        </button>
      </div>
    </div>
  </form>
</template>

<script>
import { validateAll } from "indicative/validator";
import {
  getErrorMessage,
  scrollTop
} from "../../utils/helper";

export default {
  data() {
    return {
      showAlert: false,
      alertClass: "",
      alertMessage: "",
      errors: {},
      data: {
        currentPassword: "",
        newPassword: "",
        confirmPassword: ""
      },
      loading: false
    };
  },
  methods: {
    OnInput(e) {
      delete this.errors[e.target.name];
    },
    updatePassword(e) {
      e.preventDefault();

      const rules = {
        currentPassword: 'required',
        newPassword: 'required|min:6',
        confirmPassword: 'required|same:newPassword'
      };

      const messages = {
        'currentPassword.required': 'Enter your current password',
        'newPassword.required': 'Enter your new password',
        'confirmPassword.required': 'Enter password confirmation',
        'confirmPassword.same': 'Password do not match'
      };

      validateAll(this.data, rules, messages)
        .then(() => {
          this.errors = {};
          this.processUpdate();
        })
        .catch(errors => {
          const formattedErrors = {};
          errors.forEach(
            error => (formattedErrors[error.field] = error.message)
          );
          // console.log(formattedErrors, "errors");
          this.errors = formattedErrors;
        });
    },
    async processUpdate() {
      try {
        this.loading = true;
        this.showAlert = false;
        this.alertMessage = "";

        const res = await axios.post('/api/user/update-password', this.data);

        if (res.status === 200) {
          this.data.currentPassword = "";
          this.data.newPassword = "";
          this.data.confirmPassword = "";

          this.$swal({
            icon: "success",
            title: this.$t('profile.user.notifications.password_updated.title'),
            text: this.$t('profile.user.notifications.password_updated.message')
          });

          const user = res.data.user;
          this.$store.dispatch('auth/setUserData', res.data.user);

          this.loading = false;
          scrollTop();
        }
      } catch (err) {
        // console.log(err, "Err caught");

        this.$swal({
          icon: "error",
          title: "There was an error",
          text: getErrorMessage(err)
        });
        this.loading = false;
        scrollTop();
      }
    },
  }
}
</script>

<style scoped>
.btn-primary {
  background: #012e55;
  border: 0px solid;
}
.form-error input {
  border: 2px solid red;
}

.error-text {
  color: red;
  margin: 8px 0px;
}

.cancel-button {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

.pointer {
  cursor: pointer;
}
</style>
