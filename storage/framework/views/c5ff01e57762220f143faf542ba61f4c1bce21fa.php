<!DOCTYPE html>
<html>
<head>
	<title><?php echo $__env->yieldContent('title'); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/app.css')); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/font-awesome.min.css')); ?>">
	<script type="text/javascript" src="<?php echo e(asset('js/jquery.min.js')); ?>"></script>
	<script type="text/javascript" src="<?php echo e(asset('js/bootstrap.min.js')); ?>"></script>
	<style type="text/css">
	body {
		margin-top: 64px;
		margin-bottom: 64px;
	}
	footer {		
		background: white;
		border-top: 1px solid silver;
		position: fixed;
		bottom: 0;
		left: 0;
		width: 100%;
	}
	.jumbotron img#banner {
		position: absolute;
		left: 0;	
		top: 0;	
		width: 100%;
	}
	</style>
	<?php echo $__env->yieldContent('styles'); ?>
</head>
<body>

<nav class="navbar navbar-default navbar-fixed-top"> 
	<div class="container-fluid">
		<!-- Brand and toggle get grouped for better mobile display -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bvt-navbar-collapse" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">BVT</a>
		</div>
		
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bvt-navbar-collapse">
			<ul class="nav navbar-nav">
				<li class="<?php echo e(Request::is('/') ? 'active' : null); ?>"><a href="#"><i class="fa fa-home"></i> HOME</a></li>
				<li class="<?php echo e(Request::is('pricewatch*') ? 'active' : null); ?>"><a href="<?php echo e(action('PriceWatchController@index')); ?>"><i class="fa fa-eye"></i> PRICE WATCH</a></li>
				<li class="<?php echo e(Request::is('pricetrends*') ? 'active' : null); ?>"><a href="<?php echo e(action('PriceTrendsController@index')); ?>"><i class="fa fa-line-chart"></i> PRICE TRENDS</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href=""><span class="glyphicon glyphicon-user"></span> DMedrano</a></li>
				<li><a href=""><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container">	
	<?php echo $__env->yieldContent('content'); ?>
</div>

<footer>
	<p class="text-center"><a href="">HOME</a> | <a href="">Price Watch</a> | <a href="">Price Trends</a></p>
</footer>

<!-- Kendo UI Core -->
<link rel="stylesheet" href="<?php echo e(asset('kendoui.core/styles/kendo.common.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('kendoui.core/styles/kendo.office365.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('kendoui.core/styles/kendo.office365.mobile.min.css')); ?>">
<script src="<?php echo e(asset('kendoui.core/js/kendo.core.min.js')); ?>"></script>
<script src="<?php echo e(asset('kendoui.core/js/kendo.all.min.js')); ?>"></script>

<!-- BVT Library -->
<script src="<?php echo e(asset('js/bvt.min.js')); ?>"></script>

<?php echo $__env->yieldContent('plugins'); ?>
<?php echo $__env->yieldContent('scripts'); ?>

</body>
</html>