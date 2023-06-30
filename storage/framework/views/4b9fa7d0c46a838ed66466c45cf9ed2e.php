 <?php $__env->slot('header', null, []); ?> 
    <div class="pb-6">
        <h2 class="text-center">Systemeinstellungen</h2>
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
                <?php if($isPermissionRoleModalOpen): ?>
                    <?php echo $__env->make('livewire.system-settings.permission-role-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold">Berechtigungen</h1>
                    </div>
                    <div>
                        <button wire:click="createPermissionRole()"
                            class="my-4 inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-gray-600 text-base font-bold text-white shadow-sm hover:bg-gray-700">
                            Rolle hinzufügen
                        </button>
                    </div>
                </div>
                <table width="100%" class="table-auto border-collapse border border-slate-500">
                    <tr>
                        <th class="border p-2">Berechtigungen</th>
                        <?php $__currentLoopData = $permission_roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission_role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th class="border p-2"><?php echo e($permission_role->friendly_name); ?>

                                <?php if($permission_role->id != 1): ?>
                                    <button wire:click="deletePermissionRole(<?php echo e($permission_role->id); ?>)"><i class="fa-solid fa-trash"></i></button>
                                <?php endif; ?>
                            </th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($permission->parent === null): ?>
                            <tr>
                                <td class="border p-2"><?php echo e($permission->friendly_name); ?></td>
                                <?php $__currentLoopData = $permission_roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission_role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <td class="border p-2"><input type="checkbox" class="form-control" wire:click="togglePermission(<?php echo e($permission_role->id); ?>, <?php echo e($permission->id); ?>)" <?php if($permission_role->hasPermission($permission->id)): ?>
                                        checked="checked"
                                    <?php endif; ?>></td>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tr>
                            <?php $__currentLoopData = $permission->childs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="border p-2" style="padding-left: 25px;"><?php echo e($child->friendly_name); ?></td>
                                    <?php $__currentLoopData = $permission_roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permission_role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <td class="border p-2"><input type="checkbox" class="form-control" wire:click="togglePermission(<?php echo e($permission_role->id); ?>, <?php echo e($child->id); ?>)" <?php if($permission_role->hasPermission($child->id)): ?>
                                            checked="checked"
                                        <?php endif; ?>></td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </table>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mb-4">
                <?php if($isCompanyModalOpen): ?>
                    <?php echo $__env->make('livewire.system-settings.company-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold">Firmen</h1>
                    </div>
                    <div>
                        <button wire:click="createCompany()"
                            class="my-4 inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-gray-600 text-base font-bold text-white shadow-sm hover:bg-gray-700">
                            Firma hinzufügen
                        </button>
                    </div>
                </div>
                <table width="100%" class="table-auto">
                    <tr>
                        <th class="border p-2">Name</th>
                        <th class="border p-2">Kürzel</th>
                        <th class="border p-2">Anschrift</th>
                        <th class="border p-2">Aktionen</th>
                    </tr>
                    <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $company): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="border p-2"><?php echo e($company->friendly_name); ?></td>
                            <td class="border p-2"><?php echo e($company->short_name); ?></td>
                            <td class="border p-2"><?php echo e($company->address); ?></td>
                            <td class="border p-2">
                                <button wire:click="editCompany(<?php echo e($company->id); ?>)"><i class="fa-solid fa-pen"></i></button>
                                <button wire:click="deleteCompany(<?php echo e($company->id); ?>)"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </table>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mb-4">
                <?php if($isDepartementModalOpen): ?>
                    <?php echo $__env->make('livewire.system-settings.departement-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>
                <div class="flex flex-row justify-between items-center">
                    <div>
                        <h1 class="text-xl font-bold">Abteilungen</h1>
                    </div>
                    <div>
                        <button wire:click="createDepartement()"
                            class="my-4 inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-gray-600 text-base font-bold text-white shadow-sm hover:bg-gray-700">
                            Abteilung hinzufügen
                        </button>
                    </div>
                </div>
                <table width="100%" class="table-auto">
                    <tr>
                        <th class="border p-2">Name</th>
                        <th class="border p-2">Abteilungsleitung</th>
                        <th class="border p-2">Aktionen</th>
                    </tr>
                    <?php $__currentLoopData = $departements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $departement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="border p-2"><?php echo e($departement->friendly_name); ?></td>
                            <td class="border p-2"><?php echo e($departement->head_of_departement_user->get('fullname')->value); ?></td>
                            <td class="border p-2">
                                <button wire:click="editDepartement(<?php echo e($departement->id); ?>)"><i class="fa-solid fa-pen"></i></button>
                                <button wire:click="deleteDepartement(<?php echo e($departement->id); ?>)"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </table>
            </div>


        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\hr-software-3\resources\views/livewire/system-settings/system-settings.blade.php ENDPATH**/ ?>