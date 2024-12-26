<!DOCTYPE html>
<html lang="en">
<head>
  @include('layouts.head')
  {!! htmlScriptTagJsApi() !!}
  @yield('css')
</head>

<body class="c-app flex-row align-items-center">

  @yield('content')

  <script src="{{ mix('js/app.js') }}"></script>

  @yield('script')
</body>

</html>
