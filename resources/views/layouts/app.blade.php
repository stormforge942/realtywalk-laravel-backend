<!DOCTYPE html>
<html lang="en">

<head>
  @include('layouts.head')

  @yield('css')

  @stack('extra-css')
</head>

<body class="c-app">
  <div class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show" id="sidebar">
    @include('layouts.shared.nav-builder')
  </div>

  <div class="c-wrapper">
    @include('layouts.shared.header')

    <div class="c-body">
      <main id="backend" class="c-main">
        @yield('content')
      </main>
    </div>

    @include('layouts.shared.footer')
  </div>

  <script src="//code.jquery.com/jquery-2.2.2.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.1.2/js/plugins/sortable.min.js" type="text/javascript"></script>
  <script src="{{ mix('js/backend.js') }}"></script>
  <script
  src="https://code.jquery.com/jquery-3.5.1.min.js"
  integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
  crossorigin="anonymous"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.4.0/tinymce.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
  <script>
   $(document).ready(function(){

    tinymce.init({
        selector:'.desc-editor',
        width: "100%",
        height: 400,
         setup: function (editor) {
        editor.on('change', function () {
            tinymce.triggerSave();
        });
    }
    });

    if ($("#add")) {
      let linkNum = 1;
      //add more inputs for links
      $('#add').click(function(e) {
        e.preventDefault();
        linkNum++;
        $('#links-container').append(`
          <div class="links">
            <input class="form-control" type="text" name="links[label][]" placeholder="Label" />
            <input class="form-control ml-2" type="text" name="links[url][]" placeholder="URL (https://)" />
            <button id="${linkNum}" type="button" class="btn btn-default remove">Remove</button>
          </div>
        `)
      });

      $(document).on("click", ".remove", function(){
        let $this = $(this);
        //remove the parent of the current link
        $this.parent().remove();
      });
    }



   })
  </script>
  @yield('script')
  @stack('script')
</body>

</html>
