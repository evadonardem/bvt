<!DOCTYPE html>
<html>
<head>
	<title><?php echo $__env->yieldContent('title'); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('css/font-awesome.min.css')); ?>">
	<link rel="stylesheet" type="text/css" href="<?php echo e(asset('kendoui.core/styles/kendo.mobile.all.min.css')); ?>">	
</head>
<body>

<?php echo $__env->yieldContent('content'); ?>

<div data-role="drawer" data-position="left" id="menu">
	<ul data-role="listview">
		<li><a href="#home"><i class="fa fa-lg fa-home"></i> Home</a></li>
		<li><a href="#pricewatch"><i class="fa fa-lg fa-eye"></i> Price Watch</a></li>
		<li><a href="#pricetrends"><i class="fa fa-lg fa-bar-chart"></i> Price Trends</a></li>
	</ul>
</div>

<?php if(Auth::check()): ?>
<div data-role="drawer" data-position="right" id="user" data-init="initSignOut">
	<ul data-role="listview">
		<li>
			<label for="">Hi! <?php echo e(Auth::user()->name); ?></label>			
		</li>		
	</ul>
	<p>
		<button id="signOutBtn" data-role="button" class="km-primary km-justified"><i class="fa fa-lg fa-sign-out"></i> Sign-out</button>
	</p>
</div>
<div data-role="drawer" data-position="right" id="settings">	
	<ul data-role="listview">
		<li>T</li>
		<li>X</li>
	</ul>
</div>
<?php else: ?>

<?php endif; ?>

<script type="text/javascript" src="<?php echo e(asset('kendoui.core/js/jquery.min.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('kendoui.core/js/kendo.all.min.js')); ?>"></script>

<script type="text/javascript">
function initSignOut(e) {	
	var signOutBtn = e.sender.element.find('#signOutBtn');
	signOutBtn.click(function() {
		$.get("<?php echo e(url('mobile-sign-out')); ?>", function() {
			window.location.replace("<?php echo e(url('mobile')); ?>");
		});
	});
}
</script>

<?php echo $__env->yieldContent('scripts'); ?>

<script type="text/javascript">
	var app = new kendo.mobile.Application(document.body, {
		//useNativeScrolling: true, 
		skin: "nova"
	});
</script>

</body>
</html>