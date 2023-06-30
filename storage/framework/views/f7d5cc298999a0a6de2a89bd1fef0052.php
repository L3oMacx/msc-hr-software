 <?php $__env->slot('header', null, []); ?> 
    <?php echo $__env->make('livewire.user.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
 <?php $__env->endSlot(); ?>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
            <h1 class="text-xl font-bold">Übersicht</h1>
            <?php if($isEditModalOpen): ?>
                <?php echo $__env->make('livewire.user.overview-data-edit-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
            <?php if($isHistoryModalOpen): ?>
                <?php echo $__env->make('livewire.user.overview-data-history-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">

            <?php $__currentLoopData = $data_categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
                    <h3 class="text-l font-bold"><?php echo e($category['friendly_name']); ?></h3>
                    <table width="100%">
                        <?php $__currentLoopData = $category['keys']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr><td><?php echo e($key->friendly_name); ?></td><td><?php echo e(Illuminate\Support\Str::limit($user->get($key->data_key)->value, 30)); ?></td><td style="text-align: right;">
                                <?php if(($user->id === \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_self_overview_edit')) || ($user->id !== \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_other_overview_edit'))): ?>
                                    <button wire:click="edit('<?php echo e($key->data_key); ?>')"><i class="fa-solid fa-pen"></i></button>
                                <?php endif; ?>
                                <?php if(($user->id === \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_self_overview_history')) || ($user->id !== \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_other_overview_history'))): ?>
                                    <button wire:click="openHistoryModalPopover('<?php echo e($key->data_key); ?>')"><i class="fa-solid fa-clock-rotate-left"></i></button>
                                <?php endif; ?>
                            </td></tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </table>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hr-software-3\resources\views/livewire/user/overview.blade.php ENDPATH**/ ?>