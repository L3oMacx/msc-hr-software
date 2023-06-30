<div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
            role="dialog" aria-modal="true" aria-labelledby="modal-headline">



                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="">
                        <h3 class="text-l font-bold"><?php echo e($data_history_modal_heading); ?></h3>


                        <table width="100%">
                            <tr><th  class="border px-4 py-2">Zeitstempel</th><th  class="border px-4 py-2">Auslöser</th><th  class="border px-4 py-2">Aktion</th></tr>
                            <?php $__currentLoopData = $data_history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr><td  class="border px-4 py-2"><?php echo e($lg->created_at); ?></td><td  class="border px-4 py-2"><?php echo e($lg->activities[0]->causer_user->get('identifier')->value); ?></td><td  class="border px-4 py-2">Wert geändert auf <?php echo e($lg->value); ?></td></tr>

                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </table>

                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                        <button wire:click="closeHistoryModalPopover()" type="button"
                            class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-bold text-gray-700 shadow-sm hover:text-gray-700 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                            Schließen
                        </button>
                    </span>
                </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hr-software-3\resources\views/livewire/user/overview-data-history-modal.blade.php ENDPATH**/ ?>