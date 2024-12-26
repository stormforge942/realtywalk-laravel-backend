<!DOCTYPE html>
<html lang="en">

<head>

<base href="./">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>Property</title>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
<link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicon/apple-icon-57x57.png') }}">
<link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicon/apple-icon-60x60.png') }}">
<link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicon/apple-icon-72x72.png') }}">
<link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicon/apple-icon-76x76.png') }}">
<link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicon/apple-icon-114x114.png') }}">
<link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicon/apple-icon-120x120.png') }}">
<link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicon/apple-icon-144x144.png') }}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicon/apple-icon-152x152.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-icon-180x180.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon/android-icon-192x192.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon/favicon-96x96.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('favicon/manifest.json') }}">
<meta name="msapplication-TileColor" content="#ffffff">
<link rel="stylesheet" href="{{ mix('css/general.css') }}">
<link rel="stylesheet" href="{{ mix('css/property.css') }}"/>



<body class="c-app">
  <div class="c-wrapper">
    <div class="c-body">
      <main id="app" class="c-main">
	      <div id="nav">
		    <ul>
		        <li><a href="/">Home</a></li>
		        <li><a href="#">About</a></li>
		        <li><a href="/builders">Builders</a></li>
		        <li><a href="/neighborhoods">Neighborhoods</a></li>
		        <li><a href="/system/users/create">Register</a></li>
		        <li><a href="/system/login">Sign In</a></li>
		    </ul>
		  </div>
		  <div class="container-fluid">
		      <div class="row d-flex align-items-center">
			      	<div class="col-lg-10 col-md-6 col-sm-6">
		            	<div class="splash-logo"><img class="img-fluid" src="{{asset('images/footprints.png')}}" alt=""></div>
		        	</div>
					<div class="col-lg-2 col-md-6 col-sm-6 text-center">
			        	<div class="grey-btn">
			        		<a href='/system/users/create'>BECOME A MEMBER</a>
			        	</div>
		        	</div>
		      </div>
	     </div>
		<single-property :property="{{$property}}" :builder="{{$property->builder}}"></single-property>
      </main>
    </div>
  </div>
  <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
