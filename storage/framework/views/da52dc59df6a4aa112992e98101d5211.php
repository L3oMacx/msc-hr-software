 <?php $__env->slot('header', null, []); ?> 
    <div class="pb-6">
        <h2 class="text-center">Dashboard</h2>
        <?php if(session()->has('message')): ?>
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3"
                    role="alert">
                    <div class="flex">
                        <div>
                            <p class="text-sm"><?php echo e(session('message')); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
    </div>
 <?php $__env->endSlot(); ?>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="columns-1">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mb-4">
                <h4>Hallo, <?php echo e(\Auth::user()->get('fname')->value); ?>!</h4>
            </div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mb-4">
                <h4>Was möchtest du tun?</h4>
                <div class="flex flex-col md:flex-row gap-2 my-4">
                    <?php if(App\Http\Controllers\PermissionController::authUserHas('page_team')): ?>
                        <div><a class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-gray-600 text-base font-bold text-white shadow-sm hover:bg-gray-700" href="<?php echo e(route('team')); ?>">Zur Teamübersicht</a></div>
                    <?php endif; ?>
                    <?php if(App\Http\Controllers\PermissionController::authUserHas('user_self')): ?>
                        <div><a class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-gray-600 text-base font-bold text-white shadow-sm hover:bg-gray-700" href="<?php echo e(route('own-file')); ?>">Zur eigenen Akte</a></div>
                    <?php endif; ?>
                    <?php if(App\Http\Controllers\PermissionController::authUserHas('system_settings')): ?>
                        <div><a class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-gray-600 text-base font-bold text-white shadow-sm hover:bg-gray-700" href="<?php echo e(route('system-settings')); ?>">Zur den Systemeinstellungen</a></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php if(App\Http\Controllers\PermissionController::authUserHas('dashboard_requests')): ?>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mb-4">
                <h3 class="text-l font-bold">Anträge von Mitarbeitenden</h3>
                <table width="100%" class="table-auto mt-2">
                    <tr>
                        <th class="border p-2">Mitarbeitender</th>
                        <th class="border p-2">Antrag</th>
                        <th class="border p-2">Aktionen</th>
                    </tr>
                    <?php $__currentLoopData = $user_requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="border p-2"><?php echo e($r['user']->get('fullname')->value); ?></td>
                            <td class="border p-2"><?php echo $r['request_string']; ?></td>
                            <td class="border p-2">
                                <button wire:click="approveUserRequest('<?php echo e($r["model"]); ?>', <?php echo e($r["id"]); ?>)" class="inline-flex justify-center rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-bold text-white shadow-sm hover:bg-green-700 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">Genehmigen</button>
                                <button wire:click="denyUserRequest('<?php echo e($r["model"]); ?>', <?php echo e($r["id"]); ?>)" class="inline-flex justify-center rounded-md border border-transparent px-4 py-2 bg-red-500 bg- text-base leading-6 font-bold text-white shadow-sm hover:bg-red-600 focus:outline-none focus:border-red-600 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">Ablehnen</button>
                            </td>
                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hr-software-3\resources\views/livewire/dashboard/dashboard.blade.php ENDPATH**/ ?>