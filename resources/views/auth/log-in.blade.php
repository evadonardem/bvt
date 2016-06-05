@extends('layouts.main')

@section('title')
BVT > Log-in
@stop

@section('content')
<form method="post" action="{{ url('authenticate') }}">
	<div class="row">
		<div class="col-md-push-4 col-md-pull-4 col-md-4 col-sm-12">
			<p style="font-size: 200%; text-align: center;"><i class="fa fa-lg fa-user"></i> BVT Log-in</p>
		</div>
	</div>
	<div class="row">
		<div class="col-md-push-4 col-md-pull-4 col-md-4 col-sm-12">
			<input type="text" name="email" class="k-input k-textbox" style="width: 100%;">
		</div>
	</div>
	<div class="row">
		<div class="col-md-push-4 col-md-pull-4 col-md-4 col-sm-12">
			<input type="password" name="password" class="k-input k-textbox" style="width: 100%;">
		</div>
	</div>
	<div class="row">
		<div class="col-md-push-4 col-md-pull-4 col-md-4 col-sm-12">
			<input type="hidden" name="_token" value="{{ csrf_token() }}">
			<button class="k-button k-primary" style="width: 100%;"><i class="fa fa-lg fa-sign-in"></i> Log-in</button>
		</div>
	</div>
</form>
@stop

@section('scripts')
@stop

