<x-slot name="header">
    <div class="pb-6">
        <h2 class="text-center">Systemeinstellungen</h2>
        @if (session()->has('message'))
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3"
                    role="alert">
                    <div class="flex">
                        <div>
                            <p class="text-sm">{{ session('message') }}</p>
                        </div>
                    </div>
                </div>
            @endif

    </div>
</x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="columns-1">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mb-4">
                @if ($isPermissionRoleModalOpen)
                    @include('livewire.system-settings.permission-role-modal')
                @endif
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
                        @foreach ($permission_roles as $permission_role)
                            <th class="border p-2">{{ $permission_role->friendly_name }}
                                @if($permission_role->id != 1)
                                    <button wire:click="deletePermissionRole({{ $permission_role->id }})"><i class="fa-solid fa-trash"></i></button>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                    @foreach ($permissions as $permission)
                        @if($permission->parent === null)
                            <tr>
                                <td class="border p-2">{{ $permission->friendly_name }}</td>
                                @foreach ($permission_roles as $permission_role)
                                    <td class="border p-2"><input type="checkbox" class="form-control" wire:click="togglePermission({{ $permission_role->id }}, {{ $permission->id }})" @if($permission_role->hasPermission($permission->id))
                                        checked="checked"
                                    @endif></td>
                                @endforeach
                            </tr>
                            @foreach ($permission->childs as $child)
                                <tr>
                                    <td class="border p-2" style="padding-left: 25px;">{{ $child->friendly_name }}</td>
                                    @foreach ($permission_roles as $permission_role)
                                        <td class="border p-2"><input type="checkbox" class="form-control" wire:click="togglePermission({{ $permission_role->id }}, {{ $child->id }})" @if($permission_role->hasPermission($child->id))
                                            checked="checked"
                                        @endif></td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif

                    @endforeach

                </table>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mb-4">
                @if ($isCompanyModalOpen)
                    @include('livewire.system-settings.company-modal')
                @endif
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
                    @foreach ($companies as $company)
                        <tr>
                            <td class="border p-2">{{ $company->friendly_name }}</td>
                            <td class="border p-2">{{ $company->short_name }}</td>
                            <td class="border p-2">{{ $company->address }}</td>
                            <td class="border p-2">
                                <button wire:click="editCompany({{ $company->id }})"><i class="fa-solid fa-pen"></i></button>
                                <button wire:click="deleteCompany({{ $company->id }})"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>

                    @endforeach

                </table>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mb-4">
                @if ($isDepartementModalOpen)
                    @include('livewire.system-settings.departement-modal')
                @endif
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
                    @foreach ($departements as $departement)
                        <tr>
                            <td class="border p-2">{{ $departement->friendly_name }}</td>
                            <td class="border p-2">{{ $departement->head_of_departement_user->get('fullname')->value }}</td>
                            <td class="border p-2">
                                <button wire:click="editDepartement({{ $departement->id }})"><i class="fa-solid fa-pen"></i></button>
                                <button wire:click="deleteDepartement({{ $departement->id }})"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>

                    @endforeach

                </table>
            </div>


        </div>
    </div>
</div>
