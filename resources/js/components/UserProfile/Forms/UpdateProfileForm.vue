<template>
  <form method="POST" enctype="multipart/form-data" @submit="updateProfile">
    <div class="form-group">
      <label>
        {{ $t('profile.user.form.labels.name') }}
      </label>
      <div class="form-row align-items-center">
        <div class="col-md-12" :class="[errors['name'] ? 'form-error' : '']">
          <input
            type="text"
            name="name"
            v-model="data.name"
            class="form-control"
            autocomplete="off"
            @input="OnInput"
          />
          <span class="error-text">
            {{ errors["name"] && errors["name"] }}
          </span>
        </div>
      </div>
    </div>

    <div class="form-group">
      <label>
        {{ $t('profile.user.form.labels.picture') }}
      </label>
      <div class="card card-success">
        <div class="card-body">
          <image-uploader
            :preview="true"
            :className="['fileinput', { 'fileinput--loaded': hasImage }]"
            :capture="false"
            :debug="1"
            doNotResize="gif"
            :autoRotate="true"
            outputFormat="verbose"
            @input="setImage"
            :imgUrl="imgUrl"
          >
            <label for="fileInput" slot="upload-label">
              <figure>
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">
                  <path class="path1" d="M9.5 19c0 3.59 2.91 6.5 6.5 6.5s6.5-2.91 6.5-6.5-2.91-6.5-6.5-6.5-6.5 2.91-6.5 6.5zM30 8h-7c-0.5-2-1-4-3-4h-8c-2 0-2.5 2-3 4h-7c-1.1 0-2 0.9-2 2v18c0 1.1 0.9 2 2 2h28c1.1 0 2-0.9 2-2v-18c0-1.1-0.9-2-2-2zM16 27.875c-4.902 0-8.875-3.973-8.875-8.875s3.973-8.875 8.875-8.875c4.902 0 8.875 3.973 8.875 8.875s-3.973 8.875-8.875 8.875zM30 14h-4v-2h4v2z"
                  ></path>
                </svg>
              </figure>
              <span class="upload-caption">
                {{ hasImage ? $t('profile.user.btn_picture_replace') : $t('profile.user.btn_picture_upload') }}
              </span>
            </label>
          </image-uploader>
        </div>
      </div>
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
import ImageUploader from "../ImageUploader/ImageUploader.vue";

export default {
  name: "UserProfileForm",
  components: {
    ImageUploader
  },
  data() {
    return {
      showAlert: false,
      alertClass: "",
      alertMessage: "",
      errors: {},
      imgUrl: "",
      fileUploaded: false,
      showImgDelete: false,
      data: {
        name: "",
        picture: ""
      },
      loading: false,
      hasImage: false,
      image: null
    };
  },
  computed: {
    user() {
      return this.$store.state.auth.user;
    }
  },
  methods: {
    OnInput(e) {
      delete this.errors[e.target.name];
    },
    selectFile(event) {
      // `files` is always an array because the file input may be in multiple mode
      this.fileUploaded = true;
      this.data.picture = event.target.files[0];
      this.imgUrl = URL.createObjectURL(event.target.files[0]);
      this.showImgDelete = true;
      console.log(JSON.stringify(data.picture));
    },
    removeFile() {
      this.data.picture = "";
      this.imgUrl = "";
      const input = this.$refs.fileupload;
      input.type = "text";
      input.type = "file";
      this.fileUploaded = false;
    },
    updateProfile(e) {
      e.preventDefault();
      // validate data before posting to API
      const rules = {
        name: "required"
      };
      const messages = {
        "name.required": "Please enter your name"
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
        this.showImgDelete = false;
        const data = new FormData();
        data.append("picture", this.data.picture);
        data.append("name", this.data.name);

        const res = await axios.post('/api/user/update-profile', data);

        if (res.status === 200) {
          this.$swal({
            icon: "success",
            title: this.$t('profile.user.notifications.success.title'),
            text: this.$t('profile.user.notifications.success.message')
          });

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
    setImage: function(output) {
      this.hasImage = true;
      this.data.picture = output;
    }
  },
  beforeMount() {
    this.imgUrl = this.user.picture_path;
    if (this.imgUrl) {
      this.hasImage = true;
    }
  },
  mounted() {
    this.$Progress.start();
    this.data.name = this.user.name;
    this.$Progress.finish();
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

.error-text {
  color: red;
  margin: 8px 0px;
}

.cancel-button {
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

.img-upload {
  max-width: 12vw !important;
}

.pointer {
  cursor: pointer;
}

#fileInput {
  display: none;
}

/* .card {
  margin: 0 auto;
  float: none;
  margin-bottom: 10px;
} */
</style>
