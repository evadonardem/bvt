@extends('layouts.main')

@section('title')
BVT > Faker
@stop

@section('content')
<form method="post" action="{{ url('peke') }}">
	<h1>Peke</h1>
	<label for="searchKey">Pick vegetable(s):</label>
	<select id="searchKey" name="searchKey" style="width: 100%;"></select>
	<hr>
	From: <input id="start" name="start"> To: <input id="end" name="end">
	<hr>
	From: <input id="lowest" name="lowest"> To: <input id="highest" name="highest">
	<hr>
	<input type="hidden" name="_token" value="{{ csrf_token() }}">
	<button id="generateBtn" class="btn-block">Generate</button>
	<hr>
	@if(Session::get('message')!==null)
	<p class="message alert alert-success">{{ Session::get('message') }}</p>
	<script type="text/javascript">
	$('.message').fadeOut(3000);
	setTimeout(function() {
		$('.message').remove();
	}, 3000);
	</script>
	@endif

</form>

@stop

@section('scripts')
<script type="text/javascript">
$(function() {
	var dataSource = new kendo.data.DataSource({
		transport: {
			read: {
				url: "{{ action('ProductsController@index') }}",
				dataType: 'json'
			}
		}		
	});
	var searchKey = $('#searchKey').kendoDropDownList({
		dataSource: dataSource,
		dataTextField: 'name',
		dataValueField: 'id',
		filter: 'contains'
	}).data('kendoDropDownList');

	/* Range Date Picker */
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

	$('#lowest, #highest').kendoNumericTextBox();
});
</script>
@stop

