require("./bootstrap");

window.CSRF = document
  .querySelector('meta[name="csrf-token"]')
  .getAttribute("content");

import Vue from "vue";
import store from "./components/store";
import App from "./components/App.vue";
import router from "./components/helpers/router";

window.Vue = Vue;
window.nhw = {};

Vue.component("pagination", require("laravel-vue-pagination"));
import "bootstrap-fileinput";
import swal from "sweetalert";
import "chartjs-plugin-colorschemes/src/plugins/plugin.colorschemes";
import "@coreui/coreui";
import "./components";
import im from "inputmask";
import "leaflet/dist/leaflet.css";
import "../../node_modules/bootstrap-fileinput/css/fileinput.min.css";
import "../../node_modules/leaflet-draw/dist/leaflet.draw.css";

// Languages
import VueI18n from 'vue-i18n'
import messages from './i18n'

Vue.use(VueI18n)

import SliderCombo from './components/Backend/SliderCombo';
import SliderPicker from './components/Backend/SliderPicker';
import PropertyListingFields from './components/Backend/PropertyListingFields';
import UserRoleAndBuilderFields from './components/Backend/UserRoleAndBuilderFields';
import GeoPicker from './components/GeoPicker';

// Filepond
import vueFilePond from 'vue-filepond'
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'
import FilePondPluginImageExifOrientation from 'filepond-plugin-image-exif-orientation'
import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'

const FilePond = vueFilePond(
  FilePondPluginFileValidateType,
  FilePondPluginFileValidateSize,
  FilePondPluginImagePreview,
  FilePondPluginImageExifOrientation
)

Vue.directive("input-mask", {
  bind: el => {
    im().mask(el);
  },
  unbind: el => {
    im.remove(el);
  }
});

const i18n = new VueI18n({
  locale: 'en',
  fallbackLocale: 'en',
  messages,
})

const vm = new Vue({
  el: "#backend",
  i18n,
  store,
  components: {
      App,
      SliderCombo,
      SliderPicker,
      FilePond,
      GeoPicker,
      PropertyListingFields,
      UserRoleAndBuilderFields,
  },
  router,
  data: {
    deletedFiles: [],
    deletedFeaturedImages: [],
    deletedLogos: []
  },
  methods: {
    addDeletedFilesToList: function (event, file) {
      if (file.file instanceof Blob) {
        this.deletedFiles.push(file.filename);
      }
    },
    addDeletedFeaturedImagesToList: function (event, file) {
      if (file.file instanceof Blob) {
        this.deletedFeaturedImages.push(file.filename);
      }
    },
    addDeletedLogosToList: function (event, file) {
      if (file.file instanceof Blob) {
        this.deletedLogos.push(file.filename);
      }
    },
    appendFilesToFormData: function(fileData, formData, propName) {
      const sortOrder = [];

      if (fileData.length) {
        for (let i in fileData) {
            if (fileData[i].file instanceof File) {
                formData.append(propName, fileData[i].file);
            }
            if (propName === "gallery[]") {
              formData.append("sortOrder[]", fileData[i].filename);
            }
        }
      }
    }
  }
});

if (vm.$refs.uploadAlert) {
  const alert = vm.$refs.uploadAlert;
  const URI_CHECK = alert.getAttribute('data-check-uri');
  setTimeout(() => regularCheck(URI_CHECK), 10000);

  function regularCheck(URI) {
    $.ajax({
      type: 'GET',
      url: URI,
      success: function (data) {
        const { status } = data;
        if (status === 'done') {
          alert.style.display = 'none';

          let timer, closeInSeconds = 10;
          const div = document.createElement('div');
          div.innerHTML = `There is new uploaded files for this record. The page will be automatically reloaded within <span id="swal-timer" class="font-weight-bold">${closeInSeconds}</span> seconds or click the button below to reload the page now.`;

          swal({
            title: 'File uploaded!',
            content: div,
            icon: 'info',
            button: 'Continue',
            timer: closeInSeconds * 1000,
            closeOnClickOutside: false,
          }).then(function() {
              window.location.reload();
          });

          timer = setInterval(() => {
            closeInSeconds--;
            if (closeInSeconds < 0) {
              clearInterval(timer);
            } else {
              document.getElementById('swal-timer').innerHTML = closeInSeconds;
            }
          }, 1000)
        } else {
          setTimeout(() => regularCheck(URI), 5000);
        }
      }
    });
  }
}

$('.form-ajax').on('submit', function (e) {
    e.preventDefault();

    $(".errorlist").css({ display: "none" });
    $(".errorlist").html("");
    var msg = $(this).attr("data-msg");
    var redir = $(this).attr("data-to");
    var formData = new FormData(this);

    if (vm.deletedFiles.length) {
        $.each(vm.deletedFiles, function(i,v) {
            formData.append('deletedFiles[]', v);
        })
    }
    if (vm.deletedFeaturedImages.length) {
        $.each(vm.deletedFeaturedImages, function(i, v) {
            formData.append("deletedFeaturedImages[]", v);
        });
    }
    if (vm.deletedLogos.length) {
      $.each(vm.deletedLogos, function(i,v) {
          formData.append('deletedLogos[]', v);
      })
    }
    if (vm.$refs && vm.$refs.gallery) {
      vm.appendFilesToFormData(vm.$refs.gallery.getFiles(), formData, 'gallery[]');
    }
    if (vm.$refs && vm.$refs.logo) {
      vm.appendFilesToFormData(vm.$refs.logo.getFiles(), formData, 'logo[]');
    }
    if (vm.$refs && vm.$refs.featured_image) {
      vm.appendFilesToFormData(vm.$refs.featured_image.getFiles(), formData, 'featured_image[]');
    }

    formData.delete("filepond");
    formData.delete("filepond[]");

    $.ajax({
      type: $(this).attr("method"),
      url: $(this).attr("action"),
      data: formData,
      processData: false,
      contentType: false,
      beforeSend: function () {
        $('.form-ajax input[type="submit"]').prop("disabled", true);
        $('.form-ajax input[type="submit"]').attr('value', 'Please wait...');
      },
      success: function (data) {
        if (data.status === 'success') {
          resetButtonReload(msg, redir);
        }
      },
      error: function (error) {
        let responseErrors = error.responseJSON.errors || undefined;
        let responseMessage = error.responseJSON.message || undefined;

        if (responseErrors) {
            let errorValues = Object.values(responseErrors);
            errorValues.forEach((err) => {
                $(".errorlist").append("<li>" + err[0] + "</li>");
                $(".errorlist").css({ display: "block" });
                $('.form-ajax input[type="submit"]').prop("disabled", false);
                $('.form-ajax input[type="submit"]').attr("value", "Save");
            });
        } else if (responseMessage) {
            $(".errorlist").append("<li>" + responseMessage + "</li>");
            $(".errorlist").css({ display: "block" });
            $('.form-ajax input[type="submit"]').prop("disabled", false);
            $('.form-ajax input[type="submit"]').attr("value", "Save");
        }

        document.querySelector("body").scrollIntoView();

        document.querySelector('body').scrollIntoView();
      }
  });
});

function resetButtonReload(msg, redir) {
  $('.form-ajax input[type="submit"]').prop("disabled", false);
  $('.form-ajax input[type="submit"]').attr('value', 'Save');
  swal(msg, "", "success").then(function () {
    window.location.href = redir;
  });
}

$(function () {
  const datalist = $("datalist#alias_list");
  if (datalist.length) {
    var similars = JSON.parse(datalist.attr('data-similars'));

    for (const builder of similars) {
      datalist.append(`<option>${builder.name}</option>`);
    }

    const spinner = $('.alias-spinner').addClass('d-none');

    $('[name="alias_of"]').on('input', function () {
      spinner.removeClass('d-none');
      const aliasName = $('[name="alias_of"]').val();
      const aliasMatch = similars.filter(s => s.name == aliasName);
      if (aliasMatch.length) {
        $('[name="alias_of_id"]').val(aliasMatch[0].id);
      } else {
        $('[name="alias_of_id"]').val('');
      }

      $.ajax({
        type: "POST",
        url: '/system/builders/similar-to',
        data: {
          _token: $('[name="_token"]').val(),
          search: $(this).val(),
          exclude_builder_id: datalist.attr('data-builder-id')
        },
        success: function (response) {
          for (const builder of response.similars) {
            if (similars.some(s => s.name == builder.name)) {
              continue;
            }
            similars.push(builder);
            datalist.append(`<option>${builder.name}</option>`);
          }
          spinner.addClass('d-none');
        },
        error: function () {
          spinner.addClass('d-none');
        }
      });
    });
  }

  checkColorFieldAvailability()
  $('#zoom').on('change', function () {
    checkColorFieldAvailability()
  })
})

function checkColorFieldAvailability () {
  if ($('#zoom').length) {
    let zoom = $('#zoom').val()

    if (zoom && zoom == 2) {
      $('#polygonColor input').prop('disabled', true)
      $('#polygonColor .input-group-append').addClass('disabled')
      $('.color-polygon-info').show()
    } else {
      $('#polygonColor input').prop('disabled', false)
      $('#polygonColor .input-group-append').removeClass('disabled')
      $('.color-polygon-info').hide()
    }
  }
}

$('#toggle_aliased').on('change', function (e) {
    let query = [];

    if ($(this).prop('checked') === true) {
        query.push('aliased=true');
    }

    if ($('#toggle_hide_blacklist').prop('checked') === true) {
        query.push('hide_blacklist=true');
    }

    query = query.length ? `?${query.join('&')}` : '';

    window.location = `/system/builders${query}`;
});

$("#toggle_hide_blacklist").on("change", function(e) {
  let query = [];

  if ($(this).prop("checked") === true) {
    query.push('hide_blacklist=true');
  }

  if ($('#toggle_aliased').prop('checked') === true) {
    query.push('aliased=true');
  }

  query = query.length ? `?${query.join('&')}` : '';

  window.location = `/system/builders${query}`;
});
