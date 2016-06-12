@extends('layouts.main')

@section('title')
BVT > Price Trends
@stop

@section('styles')
<style type="text/css">
.bar-legend {
	list-style: none;
}
.bar-legend > div > span {
	border: 1px solid black;
}
</style>
@stop

@section('content')
<div class="jumbotron" style="background: white; position: relative;">	
	<img id="banner" src="{{ asset('images/fresh-vegetables.jpg') }}" class="img-responsive">	
	<div class="row">		
		<div class="col-md-12">
			<h1 class="text-center">Vegetable Price Trends <i class="fa fa-line-chart"></i></h1>
			<p class="text-center">La Trinidad Vegetable Trading Post Highland Vegetable Price Updates</p>			
		</div>

		<div class="col-md-offset-2 col-md-8 well well-lg" style="opacity: .9;">
			<div class="col-md-8 col-sm-10 col-xs-10">
				<label>Pick Vegetable(s):</label>
				<select id="searchKey"></select>
				<br>							
				<button id="plotBtn" class="btn-block">Plot Chart</button>
			</div>
			<div class="col-md-4">
				<label for="option1">
					<input id="option1" name="type" type="radio" class="project-type" data-bind="checked: option1" checked="" value="monthly"> Monthly
				</label>
				<label for="option2">
					<input id="option2" name="type" type="radio" class="project-type" data-bind="checked: option2" value="daily"> Daily
				</label>
				<div id="datePickerWrapper1">
					From: <input id="start"> To: <input id="end">
				</div>
				<div id="datePickerWrapper2">
					<input id="picker">
				</div>
			</div>			
		</div>			
	</div>
</div>

<div class="row">
	<div class="col-md-9">
		<canvas id="myChart" style="width: 100%;"></canvas>		
	</div>
	<div class="col-md-3">
		<div id="myChartLegend"></div>
	</div>
</div>
@stop

@section('plugins')
<!-- MultiSelect -->
<script src="{{ asset('kendoui.core/js/kendo.data.min.js') }}"></script>
<script src="{{ asset('kendoui.core/js/kendo.list.min.js') }}"></script>
<script src="{{ asset('kendoui.core/js/kendo.popup.min.js') }}"></script>

<script src="{{ asset('kendoui.core/js/kendo.fx.min.js') }}"></script>
<script src="{{ asset('kendoui.core/js/kendo.multiselect.min.js') }}"></script>

<!-- Date Picker -->
<script src="{{ asset('kendoui.core/js/kendo.calendar.min.js') }}"></script>
<script src="{{ asset('kendoui.core/js/kendo.popup.min.js') }}"></script>
<script src="{{ asset('kendoui.core/js/kendo.datepicker.min.js') }}"></script>

<!-- Chart -->
<script src="{{ asset('js/chart.min.js') }}"></script>
@stop

@section('scripts')
<script>
$(function() {

	// initialization
	$('#datePickerWrapper2').hide();
	$('.project-type').click(function() {
		var id = Number($(this).attr('id').replace('option', ''));		
		if(id==1) {
			$('#datePickerWrapper1').show();
			$('#datePickerWrapper2').hide();
		} else {
			$('#datePickerWrapper2').show();
			$('#datePickerWrapper1').hide();
		}
	});

	var dataSourceSearchKey = new kendo.data.DataSource({
		transport: {
			read: {
				url: "{{ action('ProductsController@index') }}",
				dataType: 'json'
			}
		}		
	});

	var searchKey = $('#searchKey').kendoMultiSelect({
		dataSource: dataSourceSearchKey,
		dataTextField: 'name',
		dataValueField: 'id'
	}).data('kendoMultiSelect');	

	/* Range Date Picker */
	var picker = $('#picker').kendoDatePicker({
		start: 'year',
		depth: 'year',
		format: 'yyyy MMM',
		footer: false,
		value: new Date()		
	}).data('kendoDatePicker');

	var start = $('#start').kendoDatePicker({
		start: 'year',
		depth: 'year',
		format: 'yyyy MMM',
		footer: false,
		value: new Date(),
		change: startChange
	}).data('kendoDatePicker');

	var end = $('#end').kendoDatePicker({
		start: 'year',
		depth: 'year',
		format: 'yyyy MMM',
		footer: false,
		value: new Date(),
		change: endChange
	}).data('kendoDatePicker');

	function startChange() {
		var startDate = start.value(),
		endDate = end.value;

		if(startDate) {
			startDate = new Date(startDate);
			startDate.setDate(startDate.getDate());
			end.min(startDate);
		} else if(endDate) {
			start.max(new Date(endDate));
		} else {
			endDate = new Date();
			start.max(endDate);
			end.min(endDate);
		}
	}

	function endChange() {
		var endDate = end.value(),
		startDate = start.value();

		if(endDate) {
			endDate = new Date(endDate);
			endDate.setDate(endDate.getDate());
			start.max(endDate);
		} else if(startDate) {
			end.min(new Date(startDate));
		} else {
			endDate = new Date();
			start.max(endDate);
			end.min(endDate);
		}
	}

	start.max(end.value());
	end.min(start.value());
	/* Range Date Picker */
	

	var myChart = null;
	$('#plotBtn').click(function() {
		var data = {};

		var keys = searchKey.value();
		var projectType = $('.project-type:checked').val();

		data.keys = keys;
		if(projectType == 'monthly') {
			data.start = start.value().toDateString();
			data.end = end.value().toDateString();
		} else if(projectType == 'daily') {
			data.date = picker.value(),toDateString();
		}

		var dataSourceChart = new kendo.data.DataSource({
			transport: {
				read: {
					url: "{{ action('PriceTrendsController@index') }}",
					dataType: 'json',								
					data: data
				}
			}			
		});

		dataSourceChart.fetch(function() {
			var data = dataSourceChart.data();

			var labels = [];			
			var datasets = [];
			for(var i=0; i<data.length; i++) {
				var record = data[i];
				labels.push(record.month);

				if(i==0) {
					for(var j=0; j<record.vegetables.length; j++) {
						var vegetable = record.vegetables[j];
						var red = randomNumberBetween(1, 255);
						var green = randomNumberBetween(1, 255);
						var blue = randomNumberBetween(1, 255);
						datasets.push({
							label: vegetable.name,
							fillColor: "rgba("+red+","+green+","+blue+",0.5)",
							strokeColor: "rgba("+red+","+green+","+blue+",1)",
							pointColor: "rgba("+red+","+green+","+blue+",1)",							
							data: [vegetable.unit_price_avg]
						});
					}
				} else {
					for(var j=0; j<record.vegetables.length; j++) {
						var vegetable = record.vegetables[j];
						datasets[j].data.push(vegetable.unit_price_avg);
					}
				}
			}				

			var dataLineChart = {
				labels : labels,
				datasets: datasets,
			}			

			if(myChart != null) {
				myChart.destroy();
			}

			var ctx = $('#myChart').get(0).getContext('2d');
			myChart = new Chart(ctx).Bar(dataLineChart, {
				responsive: true,
				legendTemplate : "<div class=\"<%=name.toLowerCase()%>-legend\">" + 
					"<p><strong>Legend: </strong></p>" + 
					"<% for (var i=0; i<datasets.length; i++){%>" + 
						"<div class=\"col-md-12\"><span style=\"background-color:<%=datasets[i].strokeColor%>\">&nbsp;&nbsp;&nbsp;</span> <%if(datasets[i].label){%><%=datasets[i].label%><%}%></div>" + 
					"<%}%></div>"
			});
						
			$('#myChartLegend').html(myChart.generateLegend());
		});

	});
});

function randomNumberBetween(start, end) {
	var number = 1 + Math.floor(Math.random() * end-1);
	return Math.round(number);
}
</script>
@stop

@section('styles')

@stop