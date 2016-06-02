@extends('layouts.main')

@section('title')
BVT > Price Watch
@stop

@section('content')
<div class="jumbotron" style="background: white; position: relative;">
	<div class="row">
		<img id="banner" src="{{ asset('images/fresh-vegetables.jpg') }}" class="img-responsive">			
		<div class="col-md-12">
			<h1 class="text-center">Vegetable Price Watch <i class="fa fa-eye"></i></h1>
			<p class="text-center">La Trinidad Vegetable Trading Post Highland Vegetable Price Updates</p>			
		</div>

		<div class="col-md-offset-2 col-md-8 well well-lg" style="opacity: .9;">
			<div class="row">			
				<h3 class="text-center col-md-6">{{ date('(D) M d, Y') }}</h3>
				<div class="col-md-6 col-sm-12 col-xs-12">
					<label for="searchKey">Pick vegetable(s):</label>
					<select id="searchKey"></select>
					<button id="searchBtn" class="btn-block">Search</button>
				</div>
			</div>
		</div>			
	</div>	
</div>

<div id="searchResult" class="row" style="background: none; border: none;"></div>
<div id="pagerWrapper">
	<div id="pager"></div>
</div>
@stop

@section('plugins')
<!-- Button -->
<script src="{{ asset('kendoui.core/js/kendo.button.min.js') }}"></script>

<!-- MultiSelect -->
<script src="{{ asset('kendoui.core/js/kendo.data.min.js') }}"></script>
<script src="{{ asset('kendoui.core/js/kendo.list.min.js') }}"></script>
<script src="{{ asset('kendoui.core/js/kendo.popup.min.js') }}"></script>

<script src="{{ asset('kendoui.core/js/kendo.fx.min.js') }}"></script>
<script src="{{ asset('kendoui.core/js/kendo.multiselect.min.js') }}"></script>

<!-- ListView -->
<script src="{{ asset('kendoui.core/js/kendo.listview.min.js') }}"></script>

<!-- Pager -->
<script src="{{ asset('kendoui.core/js/kendo.pager.min.js') }}"></script>
@stop

@section('scripts')
<script>
$(function() {
	var dataSource = new kendo.data.DataSource({
		transport: {
			read: {
				url: "{{ action('ProductsController@index') }}",
				dataType: 'json'
			}
		}		
	});

	/*dataSource.fetch(function(){
		var data = dataSource.data();
		console.log(data);
	});*/
	
	var dataSourceListView = null;

	var searchResultPager = $('#pager').kendoPager({
		dataSource: dataSourceListView		
	}).data('kendoPager');
	
	var searchResult = $('#searchResult').kendoListView({
		dataSource: dataSourceListView,
		template: kendo.template($('#template').html()),
		dataBound: function() {
			$('img').error(function() {
				$(this).attr('src', "{{ asset('images/no_image.jpg') }}")
			});
		}
	}).data('kendoListView');		
	
	var searchKey = $('#searchKey').kendoMultiSelect({
		dataSource: dataSource,
		dataTextField: 'name',
		dataValueField: 'id'
	}).data('kendoMultiSelect');
	
	$('#searchBtn').kendoButton({
		icon: 'search',
		click: function(e) {			
			dataSourceListView = new kendo.data.DataSource({
				transport: {
					read: {
						url: "{{ action('PriceWatchController@index') }}",
						dataType: 'json',								
						data: {
							keys: searchKey.value()
						}
					}
				},
				pageSize: 4
			});

			searchResult.setDataSource(dataSourceListView);
			searchResultPager.setDataSource(dataSourceListView);
			$('#pagerWrapper').show();			

			/*dataSourceListView.fetch(function() {
				var data = dataSourceListView.data();
				console.log(data);
			});*/			

		}
	}).trigger('click');



});
</script>
<script type="text/kendo-x-tmpl" id="template">	
	<div class="vegetable col-md-6">
		<div class="media well well-sm" style="margin-bottom: 18px; margin-right: 6px; opacity: 0.8;">
			<div class="media-left media-top">
				<img src="{{ asset('images/#:name#.jpg') }}" class="media-object img-thumbnail">
			</div>
			<div class="media-body">
				<h4 class="media-heading" style="border-bottom: 1px solid silver; margin-right: 12px;">
					#:name# 
					#if(unit_price_latest!=null){#
						#:'Php ' + unit_price_latest.unit_price + ' / Kg'#
					#}#
				</h4>
				
				#if(unit_price_latest!=null){#
					<p><strong>&raquo; Price update as of #:unit_price_latest.datetime_posted#</strong></p>
					<p>Min: #:unit_price_min#</p>
					<p>Max: #:unit_price_max#</p>
					<p>Ave: #:unit_price_avg#</p>
				#}#							
			</div>
		</div>
	</div>
</script>
@stop

@section('styles')
<style type="text/css">
#searchResult {
	padding: 18px;	
	margin: 0;
}
.vegetable {
	padding: 0px;	
}
</style>
@stop