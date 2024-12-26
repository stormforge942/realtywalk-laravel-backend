<template>
  <vue-dropzone
    ref="vueDropzone"
    id="dropzone"
    class="dropzone"
    :useCustomSlot=true
    :options="dropzoneOptions"
    :duplicateCheck=true
    @vdropzone-success="onSuccess"
    @vdropzone-removed-file="removeImage"
  >
    <div class="dropzone-custom-content">
      <h3 class="dropzone-custom-title">Drag and drop to upload image!</h3>
      <div class="subtitle">...or click to select image from your computer</div>
    </div>
  </vue-dropzone>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

export default {
  name: 'Dropzone',
  components: {
    vueDropzone: vue2Dropzone
  },
  props: {
    url: { type: String, required: true },
  },
  data () {
    return {
      dropzoneOptions: {
        url: this.$props.url,
        thumbnailWidth: 150,
        thumbnailHeight: 150,
        thumbnailMethod: 'contain',
        headers: { "X-CSRF-TOKEN": window.CSRF },
        acceptedFiles: 'image/*',
        addRemoveLinks: true,
        dictRemoveFileConfirmation: 'Are you sure to delete this image?',
      }
    }
  },
  methods: {
    onSuccess(file, response) {
      const element = file.previewElement
      const data = response.data
    },
    removeImage(file, error, xhr) {
      let fileName = file.name
      console.log('File: '+ fileName +' has been deleted!')
    }
  }
}
</script>

<style>
#dropzone >>> .dz-message {
  font-weight: 700;
  color: #acacac;
}
#dropzone >>> .fa-cloud-upload {
  margin-right: 10px;
}
.dropzone {
  position: relative;
  border: 1px solid #d8dbe0;
}
.dropzone-custom-content {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
}
.dropzone-custom-title {
  margin-top: 0;
  color: #321fdb;
}
.subtitle {
  color: #314b5f;
}
.vue-dropzone>.dz-preview .dz-details {
  color: #314b5f;
  background: rgba(240, 240, 240, 0.8);
}
.dropzone > .dz-preview .dz-progress .dz-upload {
  background: #2eb85c;
}
.dropzone > .dz-preview .dz-action {
  display: flex;
  flex-grow: 1;
  bottom: 15px;
  position: absolute;
  z-index: 30;
  opacity: 0;
  font-size: .5rem;
}
.dropzone > .dz-preview:hover .dz-action {
  opacity: 1;
}
</style>
