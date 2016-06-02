@extends('layouts.main')

@section('title')
Dashboard > Products
@stop

@section('content')
<ul class="breadcrumb">
	<li><a href=""><i class="fa fa-dashboard"></i> Dashboard</a></li>
	<li class="active">Products</li>
</ul>

<div class="row">
	<div class="col-md-8 col-sm-8">
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<input id="datepicker" class="pull-right">
			</div>			
		</div>
		<hr>
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<div id="grid"></div>				
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			</div>
		</div>				
	</div>
	<div class="col-md-4 col-sm-4">
		<div class="well">
			<form id="addProductForm">
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<label>Product Name</label>
					</div>
					<div class="col-md-12 col-sm-12">
						<input name="name" class="k-input k-textbox" style="width: 100%;" required>
					</div>
				</div>				
				<div class="row">
					<div class="col-md-12 col-sm-12">
						<hr>
						<button class="k-button k-primary pull-right">Add</button>
					</div>
				</div>
			</form>
		</div>
	</div>	
</div>
@stop

@section('scripts')
<script type="text/javascript">
var BVTProductManagement = {};
BVTProductManagement._token = null;
BVTProductManagement.productGrid = null
BVTProductManagement.init = function() {
	var ref = this;
	ref._token = $("input[name='_token']").val();
	ref.initProductGrid();
	ref.initAddProductForm();

	$('#datepicker').kendoDatePicker({
		start: 'year',
		depth: 'year',
		format: 'yyyy MMM',
		footer: false,
		value: new Date()
	});	

	$('#panelbar').kendoPanelBar();
}
BVTProductManagement.initAddProductForm = function() {
	var ref = this;
	var validator = $('#addProductForm').kendoValidator({
		rules: {
			nameUnique: function(input) {
				var status = null;
				if(input.is('input[name="name"]')) {					
					$.ajaxSetup({ async: false });
					$.post("{{ action('ProductsController@productNameUnique') }}", { name: input.val(), _token: ref._token }, function(r) {
						if($.isEmptyObject(r)) {
							status = true;
						} else {
							status = false;
						}
						return status;						
					}, 'json');
				}

				return status;
			}
		},
		messages: {
			required: 'Required',
			nameUnique: 'Product name already exist.'
		}
	}).data('kendoValidator');
	$('#addProductForm').submit(function() {
		if(validator.validate()) {
			var data = $('#addProductForm').serialize();
			data += '&_token='+ref._token;
			var url = "{{ action('ProductsController@store') }}";
			$.post(url, data, function(product) {
				if(!$.isEmptyObject(product)) {					
					$('input[name="name"]').val('');

					var dialog = new BVTDialog();
					dialog.config.title = '<i class="fa fa-lg fa-check-circle"></i> Success';		
					dialog.content = '<p><strong><em>'+product.name+'</em></strong> added successfully.</p>';
					dialog.buttons = [{ id: 'ok-btn', label: 'Ok'}];
					dialog.instance.center().open().pin();

					dialog.instance.element.find('#ok-btn').click(function() {
						dialog.instance.close();
					});

					ref.productGrid.object.dataSource.add(product);					

				}
			});
		}
		return false;
	});
}
BVTProductManagement.initProductGrid = function() {
	var ref = this;

	var grid = new BVTGrid();
	grid.target = $('#grid');	

	var columns = [
		{ field: 'id', title: 'ID', filterable: false, width: 100},
		{ field: 'name', title: 'Name' },
		{ field: 'latest_price', title: 'Latest Price as of {{ date('M d, Y') }}', filterable: false, attributes: { style: 'text-align: right;' } },
		{ template: '<a href="#= add_unit_price_url #" class="grid-add-button"><i class="fa fa-lg fa-plus"></i></a>', width: 40 },
		{ template: '<a href="#= price_history_url #" class="grid-history-button"><i class="fa fa-lg fa-history"></i></a>', width: 40 },
		{ template: '<a href="javascript:void(0)" class="grid-edit-button"><i class="fa fa-lg fa-edit"></i></a>', width: 40 },
		{ template: '<a href="#= delete_url #" class="grid-delete-button"><i class="fa fa-lg fa-trash"></i></a>', width: 40 }
	];

	var dataSource = {
		transport: {
			read: {
				url: "{{ action('ProductsController@index') }}",
				dataType: 'json'
			}
		},		
		pageSize: 5
	};

	grid.config.dataSource = dataSource;
	grid.config.columns = columns;

	grid.dataBoundExtension = function(e) { 
		var _grid = e.sender;
		$('.grid-add-button', _grid.element).click(function() {
			var row = $(this).closest('tr');
			var dataItem = grid.object.dataItem(row);
			var addUnitPriceURL = $(this).prop('href');

			var dialog = new BVTDialog();			
			
			var content = $('<div/>');
			var unitPrice = $('<input name="unit_price"/>');				
			content.append('<label>Unit Price: </label>&nbsp;&nbsp;');
			content.append(unitPrice);

			dialog.config.title = '<i class="fa fa-lg fa-plus"></i> <em>' + dataItem.name + '</em> Price Update';
			dialog.content = content;			
			unitPrice.kendoNumericTextBox();

			dialog.buttons = [
				{ id: 'ok-btn', label: 'Ok', primary: true },
				{ id: 'cancel-btn', label: 'Cancel' }
			];

			dialog.instance.center().open().pin();

			dialog.instance.element.find('#ok-btn').click(function() {
				$.post(addUnitPriceURL, { _token: ref._token, unit_price: unitPrice.val() }, function(r) {
					_grid.dataSource.read();
					dialog.instance.close();
				}, 'json');
				
			});

			dialog.instance.element.find('#cancel-btn').click(function() {
				dialog.instance.close();
			});

			return false;
		});
		$('.grid-history-button', _grid.element).click(function() {	
			var row = $(this).closest('tr');
			var dataItem = grid.object.dataItem(row);	
			var priceHistoryURL = $(this).prop('href');
			$.get(priceHistoryURL, function(r) {
				var dialog = new BVTDialog();
				dialog.config.title = '<i class="fa fa-lg fa-history"></i> Price History';	
				dialog.config.width = '60%';

				var gridElement = $('<div/>');
				dialog.content = gridElement;

				var columns = [
					{ field: 'datetime_posted', title: 'Date/Time Posted', filterable: false },
					{ field: 'unit_price', title: 'Unit Price', attributes: { style: 'text-align: right;'}, format: 'Php. {0}', filterable: false }
				];
				var dataSource = {
					transport: {
						read: {
							url: priceHistoryURL,
							dataType: 'json'
						}
					},
					schema: {
						data: function(response) {
							return response.prices;
						}
					},
					pageSize: 5
				};
				var priceHistoryGrid = new BVTGrid();
				priceHistoryGrid.target = gridElement;
				priceHistoryGrid.config.columns = columns;
				priceHistoryGrid.config.dataSource = dataSource;
				priceHistoryGrid.create();

				dialog.instance.center().open().pin();
			});
			return false;
		});
	}
	grid.create();

	this.productGrid = grid;
}
$(function() {
	BVTProductManagement.init();
});
</script>
@stop