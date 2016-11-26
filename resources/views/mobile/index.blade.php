@extends('layouts.main-mobile')

@section('title')
BVT > Price Trends
@stop

@section('content')

<div id="home" data-role="view" data-title="Home" data-layout="default" data-show="showHome">
	<h1><i class="fa fa-lg fa-home"></i> Home</h1>
	<hr>
	<h2>Price Watch</h2>
	<p>Lates price updates.</p>
	<ul id="latestPriceList"></ul>
	<p><a href="#pricewatch" class="km-button">See More</a></p>
	<hr>
	<h2>Price Trends</h2>
	<p>Line chart presented are the average daily price updates for the month of <em>{{ date('M Y') }}</em>.</p>
	<p><a href="#pricetrends" class="km-button">See Trends</a></p>
	<div id="chartWrapper" style="width: 95%;">
		<canvas id="myChart" style="width: 100%;"></canvas>
		<div id="myChartLegend"></div>
	</div>
</div>

<div id="pricewatch" data-role="view" data-title="Price Watch" data-layout="default" data-init="initPriceWatch" data-show="showPriceWatch">
	<h1><i class="fa fa-lg fa-eye"></i> Price Watch</h1>	
	<ul id="vegetablesList"></ul>
</div>

<div id="pricetrends" data-role="view" data-title="Price Trends" data-layout="default" data-init="initPriceTrends" data-show="showPriceTrends">
	<h1><i class="fa fa-lg fa-bar-chart"></i> Price Trends</h1>
	<ul id="vegetablesList"></ul>
</div>

<div id="vegetablepricetrend" data-role="view" data-title="Vegetable Price Trend" data-layout="default" data-show="showVegetablePriceTrend">	
	<h1><i class="fa fa-lg fa-bar-chart"></i> <span class="vegetable-name"></span> Price Trend</h1>	
	<p><a href="#pricetrends" class="km-button">Back</a></p>
	<div id="chartWrapper" style="width: 95%;">
		<canvas id="myChart" style="width: 100%;"></canvas>
	</div>	
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
</div>

<div id="sign-in" data-role="view" data-title="Sign-in" data-layout="default" data-init="initSignIn">	
	<ul data-role="listview">
		<li>
			<label for="email">Email:</label>
			<input type="email" id="email" name="email">
		</li>
		<li>
			<label for="password">Password:</label>
			<input type="password" id="password" name="password">
		</li>
		<li>
			<button id="signInBtn" data-role="button" class="km-primary km-justified">Sign-in</button>	
		</li>
	</ul>	
</div>

@if(Auth::check())
<div id="updateprice" data-role="view" data-title="Update Price" data-layout="default" data-init="initUpdatePrice" data-show="showUpdatePrice">	
	<ul id="vegetablesList"></ul>
</div>
<div id="updatevegetableprice" data-role="view" data-title="Update Vegetable Price" data-layout="default" data-show="initUpdateVegetablePrice">	
	<ul data-role="listview">
		<li>
			<label for="vegetableName">Vegetable:</label>
			<input type="text" id="vegetableName" name="vegetableName" readonly>
			<input type="hidden" id="vegetableID" name="vegetableID">
		</li>
		<li>
			<label for="price">Price:</label>
			<input type="text" id="price" name="price">
		</li>
		<li>
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<button id="updatePriceBtn" data-role="button" class="km-primary">Update Price</button>	
			<a href="#updateprice" class="km-button">Cancel</a>
		</li>
	</ul>
</div>
@endif

<section data-role="layout" data-id="default">
	<header data-role="header">
		<div data-role="navbar">
			<a href="#menu" data-rel="drawer" data-role="button" data-align="left"><i class="fa fa-lg fa-bars"></i></a>
			BVT
			@if(Auth::check())
			<a href="#user" data-rel="drawer" data-role="button" data-align="right"><i class="fa fa-lg fa-user"></i></a>			
			@else 
			<a href="#sign-in" data-role="button" data-align="right"><i class="fa fa-lg fa-sign-in"></i></a>
			@endif
		</div>
	</header>

	<!-- View content will render here -->
</section>
@stop

@section('scripts')
<script type="text/javascript">
function showHome(e) {
	var list = e.view.element.find('#latestPriceList').kendoMobileListView({		
		template: kendo.template($('#latestPrice').html())
	}).data('kendoMobileListView');
	var myChart;

	e.view.element.find('#chartWrapper').find('#myChart').remove();
	e.view.element.find('#chartWrapper').find('#myChartLegend').remove();

	e.view.element.find('#chartWrapper').append('<canvas id="myChart" style="width: 100%;"></canvas>');
	e.view.element.find('#chartWrapper').append('<div id="myChartLegend"></div>');	

	loadUpdates();
	function loadUpdates() {
		$.getJSON("{{ action('HomeController@index') }}", function(r) {
			var latestPriceDataSource = r.priceWatch;
			var latestTrendsDataSource = r.priceTrends;
			list.setDataSource(latestPriceDataSource);

			var labels = [];
			var datasets = [];

			var now = new Date("{{ \Carbon\Carbon::now() }}");
			var startDate = new Date("{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}");
			var endDate = new Date("{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}");

			for(var day=startDate.getDate(); day<=endDate.getDate(); day++) {

				if(now.getDate()==day) break;

				var month = startDate.getMonth() + 1;
				month = ((month<10) ? '0' : '') + month;
				day = ((day<10) ? '0' : '') + day;
				var date = startDate.getFullYear() + '-' + month + '-' + day;
				labels.push(date);
			}
				
			for(i in latestTrendsDataSource) {
				var product = latestTrendsDataSource[i];

				var red = randomNumberBetween(1, 255);
				var green = randomNumberBetween(1, 255);
				var blue = randomNumberBetween(1, 255);

				var dataset = {
					id: product.id,
					label: product.name,
					fillColor: "rgba("+red+","+green+","+blue+",0.0)",
					strokeColor: "rgba("+red+","+green+","+blue+",1)",
					pointColor: "rgba("+red+","+green+","+blue+",1)",
					data: []
				}

				for(var day=startDate.getDate(); day<=endDate.getDate(); day++) {

					if(now.getDate()==day) break;

					var month = startDate.getMonth() + 1;
					month = ((month<10) ? '0' : '') + month;
					day = ((day<10) ? '0' : '') + day;
					var date = startDate.getFullYear() + '-' + month + '-' + day;
					dataset.data.push(product[date]);
				}

				datasets.push(dataset);
			}		

			var dataLineChart = {
				labels : labels,
				datasets: datasets,
			}		

			var ctx = e.view.element.find('#myChart').get(0).getContext('2d');
			myChart = new Chart(ctx).Line(dataLineChart, {
				responsive: true,
				legendTemplate : "<div class=\"<%=name.toLowerCase()%>-legend\">" + 
					"<p><strong>Legend: </strong></p>" + 
					"<% for (var i=0; i<datasets.length; i++){%>" + 
						"<div class=\"col-md-12\"><span style=\"background-color:<%=datasets[i].strokeColor%>\">&nbsp;&nbsp;&nbsp;</span> <%if(datasets[i].label){%><%=datasets[i].label%><%}%></div>" + 
					"<%}%></div>"
			});
						
			e.view.element.find('#myChartLegend').html(myChart.generateLegend());

			// setTimeout(function() {			
			// 	loadUpdates();
			// }, 10000);
		});			
	}
	function randomNumberBetween(start, end) {
		var number = 1 + Math.floor(Math.random() * end-1);
		return Math.round(number);
	}
}


// (start) Price Watch
function initPriceWatch(e) {
	e.view.element.find('#vegetablesList').kendoMobileListView({
		filterable: {
			field: 'name',
			placeholder: 'Vegetable Name'
		},
		template: kendo.template($('#vegetablePrice').html())
	});

}
function showPriceWatch(e) {
	var dataSource = new kendo.data.DataSource({
		transport: {
			read: {
				url: "{{ action('PriceWatchController@index') }}",
				dataType: 'json',
				data: {
					keys: []
				}
			}
		}		
	});
	e.view.element.find('#vegetablesList').data('kendoMobileListView').setDataSource(dataSource);
}
// (end) Price Watch

// (start) Price Trends
function initPriceTrends(e) {
	e.view.element.find('#vegetablesList').kendoMobileListView({
		filterable: {
			field: 'name',
			placeholder: 'Vegetable Name'
		},
		template: kendo.template($('#vegetablePriceTrend').html())
	});
}
function showPriceTrends(e) {
	var dataSource = new kendo.data.DataSource({
		transport: {
			read: {
				url: "{{ action('ProductsController@index') }}",
				dataType: 'json'
			}
		}
	});
	e.view.element.find('#vegetablesList').data('kendoMobileListView').setDataSource(dataSource);
}
function showVegetablePriceTrend(e) {
	var vegetableName = e.view.params.name;
	var vegetableID = e.view.params.id;	
	var myChart;

	e.view.element.find('.vegetable-name').text(vegetableName);
	e.view.element.find('#chartWrapper').find('#myChart').remove();
	e.view.element.find('#chartWrapper').append('<canvas id="myChart" style="width: 100%;"></canvas>');

	$.post("{{ url('vegetabledailypricetrend') }}", { vegetables: [vegetableID], _token: "{{ csrf_token() }}" }, function(products) {
		var labels = [];
		var datasets = [1,2,3];

		var now = new Date("{{ \Carbon\Carbon::now() }}");
		var startDate = new Date("{{ \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') }}");
		var endDate = new Date("{{ \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d') }}");

		for(var day=startDate.getDate(); day<=endDate.getDate(); day++) {

			if(now.getDate()==day) break;

			var month = startDate.getMonth() + 1;
			month = ((month<10) ? '0' : '') + month;
			day = ((day<10) ? '0' : '') + day;
			var date = startDate.getFullYear() + '-' + month + '-' + day;
			labels.push(date);
		}

		for(i in products) {
			var product = products[i];

			var red = randomNumberBetween(1, 255);
			var green = randomNumberBetween(1, 255);
			var blue = randomNumberBetween(1, 255);

			var dataset = {
				id: product.id,
				label: product.name,
				fillColor: "rgba("+red+","+green+","+blue+",0.0)",
				strokeColor: "rgba("+red+","+green+","+blue+",1)",
				pointColor: "rgba("+red+","+green+","+blue+",1)",
				data: []
			}

			for(var day=startDate.getDate(); day<=endDate.getDate(); day++) {

				if(now.getDate()==day) break;

				var month = startDate.getMonth() + 1;
				month = ((month<10) ? '0' : '') + month;
				day = ((day<10) ? '0' : '') + day;
				var date = startDate.getFullYear() + '-' + month + '-' + day;
				dataset.data.push(product[date]);
			}

			datasets.push(dataset);
		}

		var dataLineChart = {
			labels : labels,
			datasets: datasets,
		}

		var ctx = e.view.element.find('#myChart').get(0).getContext('2d');
		myChart = new Chart(ctx).Line(dataLineChart, {
			responsive: true,
			legendTemplate : "<div class=\"<%=name.toLowerCase()%>-legend\">" + 
				"<p><strong>Legend: </strong></p>" + 
				"<% for (var i=0; i<datasets.length; i++){%>" + 
					"<div class=\"col-md-12\"><span style=\"background-color:<%=datasets[i].strokeColor%>\">&nbsp;&nbsp;&nbsp;</span> <%if(datasets[i].label){%><%=datasets[i].label%><%}%></div>" + 
				"<%}%></div>"
		});		
	});

	function randomNumberBetween(start, end) {
		var number = 1 + Math.floor(Math.random() * end-1);
		return Math.round(number);
	}
}
// (end) Price Trends

function initSignIn(e) {
	var emailTxt = e.view.element.find('#email');
	var passwordTxt = e.view.element.find('#password');
	var signInBtn = e.view.element.find('#signInBtn');

	signInBtn.click(function() {
		$.post("{{ url('mobile-authenticate') }}", { email: emailTxt.val(), password: passwordTxt.val(), _token: "{{ csrf_token() }}" }, function(data) {
			window.location.replace("{{ url('mobile') }}");
		}, 'json');
	});
}

function initUpdatePrice(e) {
	e.view.element.find('#vegetablesList').kendoMobileListView({
		filterable: {
			field: 'name',
			placeholder: 'Vegetable Name'
		},
		template: kendo.template($('#updateVegetablePrice').html())
	});
}

function showUpdatePrice(e) {
	var dataSource = new kendo.data.DataSource({
		transport: {
			read: {
				url: "{{ action('ProductsController@index') }}",
				dataType: 'json'
			}
		}
	});
	e.view.element.find('#vegetablesList').data('kendoMobileListView').setDataSource(dataSource);
}

function initUpdateVegetablePrice(e) {
	var vegetableID = e.view.params.id;
	var vegetableName = e.view.params.name;
	var latestPrice = e.view.params.latestprice;
	$('#vegetableID').val(vegetableID);
	$('#vegetableName').val(vegetableName);
	$('#price').val(latestPrice);

	$('#updatePriceBtn').off().click(function() {
		var productID = vegetableID;
		var unit_price = $('#price').val();
		var url = "{{ url('dashboard/products') }}/"+productID+"/prices";
		$.post(url, { _token: $('input[name="_token"]').val(), unit_price: unit_price }, function() {
			location.href = '{{ url("mobile#updateprice") }}';
		});
	});
}
</script>

<script type="text/x-kendo-template" id="latestPrice">
	<h4>#: name #</h4>
	<p><strong>Php #: latest_price #</strong><br>
	as of #: latest_price_datetime_posted #</p>
</script>

<script type="text/x-kendo-template" id="vegetablePrice">
	<h3>#: name #</h3>
	<p><strong>Latest price: Php #: (unit_price_latest!==null) ? unit_price_latest.unit_price : 0.00 # / Kg</strong></p>
	<p>Min: Php #: unit_price_min # / Kg</p>
	<p>Max: Php #: unit_price_max # / Kg</p>
	<p>Ave: Php #: unit_price_avg # / Kg</p>
</script>

<script type="text/x-kendo-template" id="vegetablePriceTrend">	
	<a href="\#vegetablepricetrend?id=#: id #&name=#: name #">#: name #</a>	
</script>

<script type="text/x-kendo-template" id="updateVegetablePrice">	
	<a href="\#updatevegetableprice?id=#: id #&name=#: name #&latestprice=#: (latest_price!==null) ? latest_price : '0.00' #">#: name #</a>
	<p>Latest Posted Price: <em>Php #: (latest_price!==null) ? latest_price : '0.00' #</em> as of <em>#: (latest_price_datetime_posted!==null) ? latest_price_datetime_posted : '-' #</em></p>
</script>
@stop