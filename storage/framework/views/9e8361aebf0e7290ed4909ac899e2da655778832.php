<?php $__env->startSection('title'); ?>
BVT > Price Trends
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<div id="home" data-role="view" data-title="Home" data-layout="default">
	<h1><i class="fa fa-lg fa-home"></i> Home</h1>
	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
	tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
	quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
	consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
	cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
	proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
	
	<h2>Price Watch</h2>
	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestiae accusantium sapiente doloribus dolorum odio repudiandae repellendus cumque similique, repellat quis labore blanditiis illum tempore neque harum officia vel ut ex.</p>
	<p><a href="#pricewatch" class="km-button">See More</a></p>

	<h2>Price Trends</h2>
	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptates inventore expedita, cumque consequuntur dolores doloremque labore placeat accusamus maxime perferendis vitae odio quia ut amet aut tenetur et. Magnam, harum.</p>
	<p><a href="#pricetrends" class="km-button">See Trends</a></p>
</div>

<div id="pricewatch" data-role="view" data-title="Price Watch" data-layout="default" data-init="initPriceWatch">
	<h1>Price Watch</h1>	
	<ul id="vegetablesList"></ul>
</div>

<div id="pricetrends" data-role="view" data-title="Price Trends" data-layout="default" data-init="initPriceTrends">
	<h1>Price Trends</h1>
	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sequi beatae a, asperiores similique voluptatum rerum laboriosam hic suscipit explicabo eligendi vero debitis? Dicta libero fugiat dolore vitae, doloremque assumenda et!</p>
	<ul id="vegetablesList"></ul>
</div>

<div id="sign-in" data-role="view" data-title="Sign-in" data-layout="default" data-init="initSignIn">	
	<ul data-role="listview">
		<li>
			<label for="email">Email:</label>
			<input type="email" id="email" name="email">
		</li>
		<li>
			<label for="password">Password:</label>
			<input type="password" id="password" name="password">
		</li>
		<li>
			<button id="signInBtn" data-role="button" class="km-primary km-justified">Sign-in</button>	
		</li>
	</ul>	
</div>

<section data-role="layout" data-id="default">
	<header data-role="header">
		<div data-role="navbar">
			<a href="#menu" data-rel="drawer" data-role="button" data-align="left"><i class="fa fa-lg fa-bars"></i></a>
			Benguet Vegetable Trading
			<?php if(Auth::check()): ?>
			<a href="#user" data-rel="drawer" data-role="button" data-align="right"><i class="fa fa-lg fa-user"></i></a>
			<a href="#settings" data-rel="drawer" data-role="button" data-align="right"><i class="fa fa-lg fa-cog"></i></a>
			<?php else: ?> 
			<a href="#sign-in" data-role="button" data-align="right"><i class="fa fa-lg fa-sign-in"></i></a>
			<?php endif; ?>
		</div>
	</header>

	<!-- View content will render here -->
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script type="text/javascript">
function initPriceWatch(e) {
	var dataSource = new kendo.data.DataSource({
		transport: {
			read: {
				url: "<?php echo e(action('PriceWatchController@index')); ?>",
				dataType: 'json',
				data: {
					keys: []
				}
			}
		}		
	});

	e.view.element.find('#vegetablesList').kendoMobileListView({
		dataSource: dataSource,
		filterable: {
			field: 'name',
			placeholder: 'Vegetable Name'
		},
		template: kendo.template($('#vegetablePrice').html())
	});
}

function initPriceTrends(e) {
	var dataSource = new kendo.data.DataSource({
		transport: {
			read: {
				url: "<?php echo e(action('ProductsController@index')); ?>",
				dataType: 'json'
			}
		}
	});

	e.view.element.find('#vegetablesList').kendoMobileListView({
		dataSource: dataSource,
		filterable: {
			field: 'name',
			placeholder: 'Vegetable Name'
		},
		template: kendo.template($('#vegetableName').html())
	});
}

function initSignIn(e) {
	var emailTxt = e.view.element.find('#email');
	var passwordTxt = e.view.element.find('#password');
	var signInBtn = e.view.element.find('#signInBtn');

	signInBtn.click(function() {
		$.post("<?php echo e(url('mobile-authenticate')); ?>", { email: emailTxt.val(), password: passwordTxt.val(), _token: "<?php echo e(csrf_token()); ?>" }, function(data) {
			window.location.replace("<?php echo e(url('mobile')); ?>");
		}, 'json');
	});
}
</script>

<script type="text/x-kendo-template" id="vegetablePrice">
	<h3>#: name #</h3>
	<p><strong>Latest price: Php #: unit_price_latest.unit_price # / Kg</strong></p>
	<p>Min: Php #: unit_price_min # / Kg</p>
	<p>Max: Php #: unit_price_max # / Kg</p>
	<p>Ave: Php #: unit_price_avg # / Kg</p>
</script>

<script type="text/x-kendo-template" id="vegetableName">	
	<a href="\#pricetrends/#: id #">#: name #</a>
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main-mobile', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>