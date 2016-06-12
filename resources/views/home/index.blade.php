@extends('layouts.main')

@section('title')
BVT > Home
@stop

@section('content')

<div class="row">
	<div class="col-md-3 col-sm-12">
		<div class="row">
			<h2><i class="fa fa-eye"></i> PriceWatch</h2>
			<p>Latest price updates.</p>
			<a href="{{ url('pricewatch') }}" class="k-button k-primary">More on PriceWatch <i class="fa fa-link"></i></a>
		</div>
		<br>
		<div id="priceWatchWrapper" class="row">
		</div>
	</div>
	<div class="col-md-9 col-sm-12">
		<div class="row">
			<h2><i class="fa fa-line-chart"></i> PriceTrends</h2>
			<p>Line chart presented are the average daily price updates for the month of <em>{{ date('M Y') }}</em>.</p>
			<a href="{{ url('pricetrends') }}" class="k-button k-primary">More on PriceTrends <i class="fa fa-link"></i></a>
		</div>
		<br>
		<div class="row">
			<div class="col-md-10">
				<canvas id="myChart" style="width: 100%;"></canvas>		
			</div>
			<div class="col-md-2">
				<div id="myChartLegend"></div>
			</div>
		</div>
	</div>
</div>		
@stop

@section('plugins')
<!-- Chart -->
<script src="{{ asset('js/chart.min.js') }}"></script>
@stop

@section('scripts')
<script type="text/javascript">
var myChart;
$(function() {
	loadUpdates();		
});
function loadUpdates() {
	req = $.getJSON("{{ action('HomeController@index') }}", function(r) {
		
		$('#priceWatchWrapper').html('');
		for(i in r.priceWatch) {
			var product = r.priceWatch[i];
			var element = $('<div class="col-md-12 col-sm-12"/>');
			element.append($('<h4/>').text(product.name));
			element.append($('<p/>').html('<strong>Php '+product.latest_price+'</strong><br>as of '+product.latest_price_datetime_posted));

			$('#priceWatchWrapper').append(element);
		}

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
			
		for(i in r.priceTrends) {
			var product = r.priceTrends[i];

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

		if(!$.isEmptyObject(myChart)) {
			myChart.destroy();
		}		

		var ctx = $('#myChart').get(0).getContext('2d');
		myChart = new Chart(ctx).Line(dataLineChart, {
			responsive: true,
			legendTemplate : "<div class=\"<%=name.toLowerCase()%>-legend\">" + 
				"<p><strong>Legend: </strong></p>" + 
				"<% for (var i=0; i<datasets.length; i++){%>" + 
					"<div class=\"col-md-12\"><span style=\"background-color:<%=datasets[i].strokeColor%>\">&nbsp;&nbsp;&nbsp;</span> <%if(datasets[i].label){%><%=datasets[i].label%><%}%></div>" + 
				"<%}%></div>"
		});
					
		$('#myChartLegend').html(myChart.generateLegend());

		setTimeout(function() {			
			loadUpdates();
		}, 10000);	
	});
}
function randomNumberBetween(start, end) {
	var number = 1 + Math.floor(Math.random() * end-1);
	return Math.round(number);
}
</script>
@stop

