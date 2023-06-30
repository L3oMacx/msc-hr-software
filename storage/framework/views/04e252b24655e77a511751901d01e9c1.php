<div class="flex items-center justify-center mb-2">
    <img class="h-12 w-12 rounded-full object-cover" src="<?php echo e($user->profile_photo_url); ?>" alt="<?php echo e($user->name); ?>" />
</div>
<div>
    <h2 class="text-center"><?php echo e($user->get('fullname')->value); ?></h2>
</div>

<div class="mt-6 flex items-center">
    <?php if(($user->id === \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_self_overview')) || ($user->id !== \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_other_overview'))): ?>
        <div class="flex-auto py-2 text-center border-b-2 hover:border-cyan-500 cursor-pointer <?php echo e($user_page == 'overview' ? 'border-cyan-600' : 'border-transparent'); ?>"
        wire:click="dasExisiterttNicht()">
            <a href="/user/<?php echo e($user->id); ?>">Übersicht</a>
        </div>
    <?php endif; ?>
    <?php if(($user->id === \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_self_timerecording')) || ($user->id !== \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_other_timerecording'))): ?>
        <div class="flex-auto py-2 text-center border-b-2 hover:border-cyan-500 cursor-pointer <?php echo e($user_page == 'timerecording' ? 'border-cyan-600' : ' border-transparent'); ?>">
            <a href="/user/<?php echo e($user->id); ?>/timerecording">Zeiterfassung</a>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\hr-software-3\resources\views/livewire/user/header.blade.php ENDPATH**/ ?>