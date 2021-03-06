@extends('layouts.main')

@section('title')
Dashboard > Products
@stop

@section('content')
<ul class="breadcrumb">
	<li><a href="{{ url('dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
	<li class="active">Products</li>
</ul>
<div class="row">
	<div class="col-md-12 col-sm-12">		
		<div id="grid"></div>				
		<input type="hidden" name="_token" value="{{ csrf_token() }}">			
	</div>
</div>
<hr>
<div class="row">
	<div class="col-md-12 col-sm-12">
		<button id="addProductBtn" class="k-button k-primary pull-right"><i class="fa fa-lg fa-plus"></i> Add Product</button>
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
}
BVTProductManagement.initAddProductForm = function() {
	var ref = this;
	$('#addProductBtn').click(function() {
		var dialog = new BVTDialog();
		dialog.config.title = '<i class="fa fa-lg fa-plus"></i> Add Product';

		var content = $('<div/>');
		content.append($('<label>Product Name</label>'));
		content.append($('<input type="text" name="name" class="k-input k-textbox" required style="width: 100%">'));

		dialog.content = content;

		dialog.buttons = [
			{ id: 'ok-btn', label: 'Ok', primary: true },
			{ id: 'cancel-btn', label: 'Cancel' }
		];

		dialog.instance.center().open().pin();

		var validator = dialog.instance.element.find('.dialog-content').kendoValidator({
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


		dialog.instance.element.find('#ok-btn').click(function() {

			if(!validator.validate()) return false;

			var url = "{{ action('ProductsController@store') }}";
			var name = dialog.instance.element.find('input[name="name"]').val();
			$.post(url, { name: name, _token: ref._token }, function(product) {
				if(!$.isEmptyObject(product)) {
					var successDialog = new BVTDialog();
					successDialog.config.title = '<i class="fa fa-lg fa-check-circle"></i> Success';		
					successDialog.content = '<p><strong><em>'+product.name+'</em></strong> added successfully.</p>';
					successDialog.buttons = [{ id: 'ok-btn', label: 'Ok'}];
					successDialog.instance.center().open().pin();

					successDialog.instance.element.find('#ok-btn').click(function() {
						successDialog.instance.close();
					});

					ref.productGrid.object.dataSource.add(product);
				}
				dialog.instance.close();
			}, 'json');				
		});

		dialog.instance.element.find('#cancel-btn').click(function() {
			dialog.instance.close();
		});
	});
}
BVTProductManagement.initProductGrid = function() {
	var ref = this;

	var grid = new BVTGrid();
	grid.target = $('#grid');	

	var columns = [
		{ field: 'id', title: 'ID', filterable: false, width: 100},
		{ field: 'name', title: 'Name' },
		{ field: 'latest_price', title: 'Latest Price as of {{ date('M d, Y') }}', filterable: false, format: 'Php. {0}', attributes: { style: 'text-align: right;' } },
		{ field: 'latest_price_datetime_posted', title: 'Date/Time Posted', filterable: false },
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
				dialog.config.title = '<i class="fa fa-lg fa-history"></i> <em>'+dataItem.name+'</em> Price History';	
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