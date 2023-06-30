<div role="status" id="toaster" x-data="toasterHub(<?php echo \Illuminate\Support\Js::from($toasts)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($config)->toHtml() ?>)" class="<?php echo \Illuminate\Support\Arr::toCssClasses([
    'fixed z-50 p-4 w-full flex flex-col pointer-events-none sm:p-6',
    'bottom-0' => $alignment->is('bottom'),
    'top-0' => $alignment->is('top'),
    'items-start' => $position->is('left'),
    'items-center' => $position->is('center'),
    'items-end' => $position->is('right'),
 ]) ?>">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.isVisible"
             x-init="$nextTick(() => toast.show($el))"
             <?php if($alignment->is('bottom')): ?>
             x-transition:enter-start="translate-y-12 opacity-0"
             <?php else: ?>
             x-transition:enter-start="-translate-y-12 opacity-0"
             <?php endif; ?>
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="relative duration-300 transform transition ease-in-out max-w-xs w-full pointer-events-auto <?php echo e($position->is('center') ? 'text-center' : 'text-left'); ?>"
             :class="toast.select({ error: 'text-white', info: 'text-black', success: 'text-white', warning: 'text-white' })"
        >
            <i x-text="toast.message"
               class="inline-block select-none not-italic px-6 py-3 rounded shadow-lg text-sm w-full <?php echo e($alignment->is('bottom') ? 'mt-3' : 'mb-3'); ?>"
               :class="toast.select({ error: 'bg-red-500', info: 'bg-gray-200', success: 'bg-green-600', warning: 'bg-orange-500' })"
            ></i>

            <?php if($closeable): ?>
            <button @click="toast.dispose()" aria-label="<?php echo app('translator')->get('close'); ?>" class="absolute right-0 p-2 focus:outline-none <?php echo e($alignment->is('bottom') ? 'top-3' : 'top-0'); ?>">
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <?php endif; ?>
        </div>
    </template>
</div>
<?php /**PATH C:\xampp\htdocs\hr-software-3\vendor\masmerise\livewire-toaster\src/../resources/views/hub.blade.php ENDPATH**/ ?>