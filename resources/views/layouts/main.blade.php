<!DOCTYPE html>
<html>
<head>
	<title>@yield('title')</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}">
	<script type="text/javascript" src="{{ asset('js/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/bootstrap.min.js') }}"></script>
	<style type="text/css">
	body {
		margin-top: 64px;
		margin-bottom: 64px;
	}
	footer {		
		background: white;
		border-top: 1px solid silver;
		position: fixed;
		bottom: 0;
		left: 0;
		width: 100%;
	}
	.jumbotron img#banner {
		position: absolute;
		left: 0;	
		top: 0;	
		width: 100%;
	}
	</style>
	@yield('styles')
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-top"> 
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bvt-navbar-collapse" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">BVT</a>
		</div>
		
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bvt-navbar-collapse">
			<ul class="nav navbar-nav">
				<li class="{{ Request::is('/') || Request::is('home') ? 'active' : null }}"><a href="{{ action('HomeController@index') }}"><i class="fa fa-home"></i> HOME</a></li>
				<li class="{{ Request::is('pricewatch*') ? 'active' : null }}"><a href="{{ action('PriceWatchController@index') }}"><i class="fa fa-eye"></i> PRICE WATCH</a></li>
				<li class="{{ Request::is('pricetrends*') ? 'active' : null }}"><a href="{{ action('PriceTrendsController@index') }}"><i class="fa fa-line-chart"></i> PRICE TRENDS</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				@if(Auth::guest())
				<li><a href="{{ url('login') }}"><span class="glyphicon glyphicon-log-in"></span> Log-in</a></li>
				@else
				<li class="{{ Request::is('dashboard*') ? 'active' : null }}"><a href="{{ url('dashboard') }}"><i class="fa fa-dashboard"></i> DASHBOARD</a></li>											
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<span class="glyphicon glyphicon-user"></span> {{ Auth::user()->name }}
					</a>
					<ul class="dropdown-menu">
						<li><a href="{{ url('logout') }}"><span class="glyphicon glyphicon-log-out"></span> Log-out</a></li>
					</ul>
				</li>
				@endif
			</ul>
		</div>
	</div>
</nav>

<div class="container">	
	@yield('content')
</div>

<footer>
	<p class="text-center"><a href="">HOME</a> | <a href="">Price Watch</a> | <a href="">Price Trends</a></p>
</footer>

<!-- Kendo UI Core -->
<link rel="stylesheet" href="{{ asset('kendoui.core/styles/kendo.common.min.css') }}">
<link rel="stylesheet" href="{{ asset('kendoui.core/styles/kendo.office365.min.css') }}">
<link rel="stylesheet" href="{{ asset('kendoui.core/styles/kendo.office365.mobile.min.css') }}">
<script src="{{ asset('kendoui.core/js/kendo.core.min.js') }}"></script>
<script src="{{ asset('kendoui.core/js/kendo.all.min.js') }}"></script>

<!-- BVT Library -->
<script src="{{ asset('js/bvt.min.js') }}"></script>

@yield('plugins')
@yield('scripts')

</body>
</html>