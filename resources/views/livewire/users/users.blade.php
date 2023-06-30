<x-slot name="header">
    <div class="pb-6">
        <h2 class="text-center">Teamübersicht</h2>
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
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4">
            <div class="flex flex-row justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold mt-4 mb-4">Teamübersicht</h1>
                </div>
                @if(App\Http\Controllers\PermissionController::authUserHas('create_user'))
                    <button wire:click="create()"
                        class="my-4 inline-flex justify-center rounded-md border border-transparent px-4 py-2 bg-gray-600 text-base font-bold text-white shadow-sm hover:bg-gray-700">
                        Mitarbeiter:in hinzufügen
                    </button>
                    @if($isModalOpen)
                    @include('livewire.users.create')
                    @endif
                @endif
            </div>
        </div>
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mt-5">

            <table class="table-fixed w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 w-20">ID</th>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">E-Mail</th>
                        <th class="px-4 py-2">Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="border px-4 py-2">{{ $user->id }}</td>
                        <td class="border px-4 py-2"><a href="/user/{{ $user->id }}">{{ $user->name }}</a></td>
                        <td class="border px-4 py-2">{{ $user->email}}</td>
                        <td class="border px-4 py-2">
                            @if(($user->id === \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_self')) || ($user->id !== \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_other')))
                                <a href="/user/{{ $user->id }}" class="inline-flex justify-center rounded-md border border-transparent px-4 py-2 bg-cyan-500 bg- text-base leading-6 font-bold text-white shadow-sm hover:bg-cyan-600 focus:outline-none focus:border-cyan-600 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">Zur Akte</a>
                            @endif
                            @if(App\Http\Controllers\PermissionController::authUserHas('remove_user'))
                                <button wire:click="delete({{ $user->id }})" class="inline-flex justify-center rounded-md border border-transparent px-4 py-2 bg-red-500 bg- text-base leading-6 font-bold text-white shadow-sm hover:bg-red-600 focus:outline-none focus:border-red-600 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5">Archivieren</button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
