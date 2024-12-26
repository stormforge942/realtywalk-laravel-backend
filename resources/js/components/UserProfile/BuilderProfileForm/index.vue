<template>
  <form>
    <h3>
      {{ $t('profile.builder.sections.gallery') }}
    </h3>
    <gallery-pond
      ref="gallery"
      class-name="gallery-pond"
      :label-idle="$t('profile.builder.form.placeholders.gallery')"
      allow-multiple="true"
      allow-reorder="true"
      max-file-size="10MB"
      accepted-file-types="image/jpeg, image/png"
    />

    <h3>
      {{ $t('profile.builder.sections.logo') }}
    </h3>
    <logo-pond
      ref="logo"
      class-name="logo-pond"
      :label-idle="$t('profile.builder.form.placeholders.logo')"
      max-file-size="5MB"
      image-preview-height="170"
      image-crop-aspect-ratio="1:1"
      image-resize-target-width="200"
      image-resize-target-height="200"
      accepted-file-types="image/jpeg, image/png"
      style-panel-layout="compact circle"
      style-load-indicator-position="center bottom"
      style-button-remove-item-position="center bottom"
    />

    <h3>
      {{ $t('profile.builder.sections.general') }}
    </h3>
    <!-- Name -->
    <div class="form-group" :class="[isError('name') ? 'has-error' : '']">
      <label class="required">
        {{ $t('profile.builder.form.labels.title') }}
      </label>
      <input
        type="text"
        name="name"
        class="form-control"
        v-model="builder.name"
        maxlength="100"
        @input="onChange('name')"
      />
      <p v-if="isError('name')">{{ errors.name[0] }}</p>
    </div>
    <!-- Email Address -->
    <div class="form-group" :class="[isError('email') ? 'has-error' : '']">
      <label class="required">
        {{ $t('profile.builder.form.labels.email') }}
      </label>
      <input
        v-input-mask
        type="text"
        name="email"
        class="form-control"
        v-model="builder.email"
        maxlength="128"
        @input="onChange('email')"
        data-inputmask="
          'alias': 'email',
          'clearIncomplete': true
        "
      />
      <p v-if="isError('email')">{{ errors.email[0] }}</p>
    </div>
    <!-- Phone -->
    <div class="form-group" :class="[isError('phone') ? 'has-error' : '']">
      <label class="required">
        {{ $t('profile.builder.form.labels.phone') }}
      </label>
      <input
        v-input-mask
        type="tel"
        name="phone"
        class="form-control"
        v-model="builder.phone"
        @input="onChange('phone')"
        data-inputmask="
          'mask'              : '(999) 999-9999',
          'autoUnmask'        : true,
          'clearIncomplete'   : true,
          'removeMaskOnSubmit': true
        "
      />
      <p v-if="isError('phone')">{{ errors.phone[0] }}</p>
    </div>
    <!-- Website -->
    <div class="form-group" :class="[isError('website') ? 'has-error' : '']">
      <label>
        {{ $t('profile.builder.form.labels.website') }}
      </label>
      <input
        v-input-mask
        type="text"
        name="website"
        class="form-control"
        v-model="builder.website"
        maxlength="128"
        @input="onChange('website')"
        data-inputmask="
          'regex': 'http(s?)://.*',
          'greedy': true
        "
      />
      <p v-if="isError('website')">{{ errors.website[0] }}</p>
    </div>
    <!-- Polygon -->
    <div class="form-group" :class="[isError('builder_areas') ? 'has-error' : '']">
      <label class="required">
        {{ $t('profile.builder.form.labels.builder_areas') }}
      </label>
      <treeselect
        :load-options="loadNeighborhoods"
        :options="neighborhoods"
        :multiple="true"
        :flat="true"
        :placeholder="$t('profile.builder.form.placeholders.neighborhood')"
        :default-expand-level="1"
        value-consists-of="LEAF_PRIORITY"
        :no-children-text="$t('profile.builder.form.placeholders.neighborhood_no_child_data')"
        v-model="builder.builder_areas"
        @select="onChange('builder_areas')"
        @open="onBuilderAreaOpen"
        @close="onBuilderAreaClose"
      />
      <p v-if="isError('builder_areas')">{{ errors.builder_areas[0] }}</p>
    </div>
    <!-- City -->
    <div class="form-group" :class="[isError('city') ? 'has-error' : '']">
      <label class="required">
        {{ $t('profile.builder.form.labels.city') }}
      </label>
      <input
        type="text"
        name="city"
        class="form-control"
        v-model="builder.city"
        maxlength="128"
        @input="onChange('city')"
      />
      <p v-if="isError('city')">{{ errors.city[0] }}</p>
    </div>
    <!-- Address 1 -->
    <div class="form-group" :class="[isError('address1') ? 'has-error' : '']">
      <label>
        {{ $t('profile.builder.form.labels.address1') }}
      </label>
      <input
        type="text"
        name="address1"
        class="form-control"
        v-model="builder.address1"
        maxlength="128"
        @input="onChange('address1')"
      />
      <p v-if="isError('address1')">{{ errors.address1[0] }}</p>
    </div>
    <!-- Address 2 -->
    <div class="form-group" :class="[isError('address2') ? 'has-error' : '']">
      <label>
        {{ $t('profile.builder.form.labels.address2') }}
      </label>
      <input
        type="text"
        name="address2"
        class="form-control"
        v-model="builder.address2"
        maxlength="128"
        @input="onChange('address2')"
      />
      <p v-if="isError('address2')">{{ errors.address2[0] }}</p>
    </div>
    <!-- Address 3 -->
    <div class="form-group" :class="[isError('address3') ? 'has-error' : '']">
      <label>
        {{ $t('profile.builder.form.labels.address3') }}
      </label>
      <input
        type="text"
        name="address3"
        class="form-control"
        v-model="builder.address3"
        maxlength="128"
        @input="onChange('address3')"
      />
      <p v-if="isError('address3')">{{ errors.address3[0] }}</p>
    </div>
    <!-- Description -->
    <div class="form-group" :class="[isError('descr') ? 'has-error' : '']">
      <label>
        {{ $t('profile.builder.form.labels.descr') }}
      </label>
      <quill-editor
        :options="editorOptions"
        v-model="builder.descr"
        style="height:200px;background:#fff;"
        @text-change="onChange('descr')"
      ></quill-editor>
      <p v-if="isError('descr')" class="mt-5">{{ errors.descr[0] }}</p>
    </div>

    <div class="form-row align-items-center mt-5 pt-4">
      <div class="col-4 col-sm-3">
        <div class="form-group mb-0">
          <button
            type="button"
            class="btn btn-block btn-primary"
            :disabled="loading"
            @click="submit()"
          >
            {{ loading ? $t('profile.builder.btn_submit_processing') : $t('profile.builder.btn_submit') }}
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'
import { scrollToId } from '@/components/utils/helper'

// Treeselect
import Treeselect from '@riophae/vue-treeselect'
import { LOAD_ROOT_OPTIONS, LOAD_CHILDREN_OPTIONS } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

// Filepond
import vueFilePond from 'vue-filepond'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation'
import FilePondPluginImageCrop from 'filepond-plugin-image-crop'
import FilePondPluginImageResize from 'filepond-plugin-image-resize'
import FilePondPluginImageTransform from 'filepond-plugin-image-transform'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'

const GalleryPond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginFileValidateSize,
  FilePondPluginImagePreview,
  FilePondPluginImageExifOrientation,
)

const LogoPond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginFileValidateSize,
  FilePondPluginImagePreview,
  FilePondPluginImageExifOrientation,
  FilePondPluginImageCrop,
  FilePondPluginImageResize,
  FilePondPluginImageTransform
)

// Quill Editor
import { quillEditor } from 'vue-quill-editor'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

export default {
  name: 'BuilderProfileForm',
  components: {
    Treeselect,
    GalleryPond,
    LogoPond,
    quillEditor,
  },
  data () {
    return {
      submitted: false,
      neighborhoods: null,
      tmpBuilderAreas: [],
    }
  },
  created () {
    this.getData().then(data => {
      this.tmpBuilderAreas = this.builder.builder_areas

      if (this.builder.gallery) {
        this.$refs.gallery.addFiles(this.builder.gallery)
      }

      if (this.builder.logo) {
        this.$refs.logo.addFile(this.builder.logo)
      }
    })
  },
  destroyed () {
    this.RESET_FORM()
    this.getData()
  },
  computed: {
    ...mapState('builder', [
      'loading',
      'builder',
      'errors',
    ]),
    ...mapGetters('builder', [
      'getFormData',
    ]),
    editorOptions () {
      return {
        modules: {
          toolbar: [
            ['bold', 'italic', 'underline', 'strike'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ script: 'sub' }, { script: 'super' }],
            ['clean'],
            ['link'],
          ],
        },
      }
    },
  },
  methods: {
    ...mapActions('builder', [
      'getData',
      'store',
    ]),
    ...mapMutations('builder', [
      'RESET_FORM',
    ]),
    async submit () {
      const formData = this.getFormData
      const gallery = this.$refs.gallery.getFiles()
      if (gallery.length) {
        for (let i in gallery) {
          formData.append('updated_gallery[]', gallery[i].file)
        }
      }
      const logo = this.$refs.logo.getFile()
      if (logo) {
        formData.append('updated_logo', logo.file)
      }
      this.submitted = true
      this.store(formData)
        .then(data => {
          this.submitted = false
          this.$swal({
            icon: 'success',
            title: this.$t('general.notifications.success.title'),
            text: data.message
          })
          scrollToId('builder_profile')
        })
        .catch(err => {
          const status = err && err.hasOwnProperty('response') ? err.response.status : 500
          const title = status === 400
            ? this.$t('general.notifications.form_error.title')
            : this.$t('general.notifications.server_error.title')
          const text = status === 400
            ? this.$t('general.notifications.form_error.message')
            : this.$t('general.notifications.server_error.message')

          this.$swal({
            icon: 'error',
            title: title,
            text: text
          })
          scrollToId('builder_profile')
        })
    },
    loadNeighborhoods ({ action, parentNode, callback }) {
      const URI = '/api/property/get_neighborhoods?lazyload=true'

      if (action === LOAD_ROOT_OPTIONS && this.neighborhoods === null) {
        axios.get(URI)
          .then(res => {
            this.neighborhoods = res.data
            callback(null, res.data)
          })
          .catch(() => {
            callback(new Error('Failed to load data: internal server error.'))
          })
      }

      if (action === LOAD_CHILDREN_OPTIONS) {
        let zone_id = parentNode.zone_id
        let parent_id = parentNode.descendants_count > 0 && parentNode.isPolygon ? parentNode.id : null
        let childrenURI = `${URI}&zone_id=${zone_id}${parent_id ? `&parent_id=${parent_id}` : ``}`

        axios.get(childrenURI)
          .then(res => {
            parentNode.children = res.data
            callback()
          })
          .catch(() => {
            callback(new Error('Failed to load data: internal server error.'))
          })
      }
    },
    isError (prop) {
      if (! this.errors.hasOwnProperty(prop)) return false
      return this.errors[prop]
    },
    onChange (name) {
      if (this.submitted && this.isError(name)) {
        this.REMOVE_ERROR(name)
      }
    },
    onBuilderAreaOpen (instanceId) {
      if (this.builder.builder_areas.length && !this.isNumeric(this.builder.builder_areas[0])) {
        this.builder.builder_areas = []
      }
    },
    onBuilderAreaClose (value, instanceId) {
      if (! this.builder.builder_areas.length) {
        this.builder.builder_areas = this.tmpBuilderAreas
      }
    },
    isNumeric (num) {
      return !isNaN(num)
    }
  },
}
</script>

<style lang="scss" scoped>
@import './style.module.scss';
</style>
