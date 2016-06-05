@extends('layouts.main')

@section('title')
BVT > Dashboard
@stop

@section('styles')
@stop

@section('content')
<div class="row">
	<div class="col-md-push-2 col-md-pull-2 col-md-8 col-sm-12">
		<h1>Update Price</h1>
		<input type="text" name="name" style="font-size: 150%; width: 100%;">		
	</div>
	<div id="updatePriceWrapper" style="display: none;">
		<div class="col-md-push-2 col-md-pull-2 col-md-8 col-sm-12">
			<h4>Latest Posted Price: <span id="latestPriceWrapper"></span></h4>
		</div>
		<div class="col-md-push-2 col-md-pull-2 col-md-8 col-sm-12">
			<input type="text" name="unit_price" style="font-size: 130%; width: 100%;" requied>
		</div>

		<div class="col-md-push-2 col-md-pull-2 col-md-8 col-sm-12">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<button id="updatePriceBtn" class="k-button k-primary" style="font-size: 150%; width: 100%;">Update Price</button>
		</div>
	</div>
</div>

@stop

@section('plugins')
<script type="text/javascript">
$(function() {
	var dataSource = {
		transport: {
			read: {
				url: "{{ action('ProductsController@index') }}",
				dataType: 'json'
			}
		}
	};
	$('input[name="name"]').kendoDropDownList({
		dataSource: dataSource,
		dataTextField: 'name',
		dataValueField: 'id',
		filter: 'contains',
		optionLabel: 'Select vegetable...',
		select: function(e) {
			var _dropDownList = e.sender;
			var dataItem = e.sender.dataItem(e.item);

			if(dataItem.id!="") {
				$('#updatePriceWrapper').show();
			} else {
				$('#updatePriceWrapper').hide();
			}
			

			$('#latestPriceWrapper').html('');
			$('#latestPriceWrapper').html(dataItem.latest_price);

			$('input[name="unit_price"]').data('kendoNumericTextBox').value('');

			$('#updatePriceBtn').off().click(function() {
				var productID = dataItem.id;
				var unit_price = $('input[name="unit_price"]').val();
				var url = "{{ url('dashboard/products') }}/"+productID+"/prices";
				$('#latestPriceWrapper').html('');				
				$.post(url, { _token: $('input[name="_token"]').val(), unit_price: unit_price }, function(r) {					
					_dropDownList.dataSource.read();
					_dropDownList.select(0);
					_dropDownList.trigger('select');
				}, 'json');
			});
		}
	});
	$('input[name="unit_price"]').kendoNumericTextBox({
		spinners: false
	});	
});
</script>
@stop

@section('scripts')
@stop

