<!DOCTYPE html>
<html>
<head>
	<title>@yield('title')</title>
	<link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.min.css') }}">
	<link rel="stylesheet" type="text/css" href="{{ asset('kendoui.core/styles/kendo.mobile.all.min.css') }}">	
</head>
<body>

@yield('content')

<div data-role="drawer" data-position="left" id="menu">
	<ul data-role="listview">
		<li><a href="#home"><i class="fa fa-lg fa-home"></i> Home</a></li>
		<li><a href="#pricewatch"><i class="fa fa-lg fa-eye"></i> Price Watch</a></li>
		<li><a href="#pricetrends"><i class="fa fa-lg fa-bar-chart"></i> Price Trends</a></li>
	</ul>
</div>

@if(Auth::check())
<div data-role="drawer" data-position="right" id="user" data-init="initSignOut">
	<ul data-role="listview">
		<li>
			<label for="">Hi! {{ Auth::user()->name }}</label>			
		</li>		
	</ul>
	<p>
		<button id="signOutBtn" data-role="button" class="km-primary km-justified"><i class="fa fa-lg fa-sign-out"></i> Sign-out</button>
	</p>
</div>
<div data-role="drawer" data-position="right" id="settings">	
	<ul data-role="listview">
		<li>T</li>
		<li>X</li>
	</ul>
</div>
@else

@endif

<script type="text/javascript" src="{{ asset('kendoui.core/js/jquery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('kendoui.core/js/kendo.all.min.js') }}"></script>

<script type="text/javascript">
function initSignOut(e) {	
	var signOutBtn = e.sender.element.find('#signOutBtn');
	signOutBtn.click(function() {
		$.get("{{ url('mobile-sign-out') }}", function() {
			window.location.replace("{{ url('mobile') }}");
		});
	});
}
</script>

@yield('scripts')

<script type="text/javascript">
	var app = new kendo.mobile.Application(document.body, {
		//useNativeScrolling: true, 
		skin: "nova"
	});
</script>

</body>
</html>