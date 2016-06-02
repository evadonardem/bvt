<?php $__env->startSection('title'); ?>
BVT | Dashboard > Products
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>

<form method="post" action="<?php echo e(action('ProductsController@store')); ?>">
<input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">

<label>Name</label>
<input name="name" class="form-control">

<label>Price</label>
<input name="price" class="form-control">

<input type="submit" value="Create" class="form-control">
</form>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.main', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>