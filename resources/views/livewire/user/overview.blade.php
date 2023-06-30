<x-slot name="header">
    @include('livewire.user.header')
</x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
            <h1 class="text-xl font-bold">Übersicht</h1>
            @if ($isEditModalOpen)
                @include('livewire.user.overview-data-edit-modal')
            @endif
            @if ($isHistoryModalOpen)
                @include('livewire.user.overview-data-history-modal')
            @endif
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">

            @foreach ($data_categories as $category)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
                    <h3 class="text-l font-bold">{{ $category['friendly_name'] }}</h3>
                    <table width="100%">
                        @foreach ($category['keys'] as $key)
                            <tr><td>{{ $key->friendly_name }}</td><td>{{ Illuminate\Support\Str::limit($user->get($key->data_key)->value, 30) }}</td><td style="text-align: right;">
                                @if(($user->id === \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_self_overview_edit')) || ($user->id !== \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_other_overview_edit')))
                                    <button wire:click="edit('{{ $key->data_key }}')"><i class="fa-solid fa-pen"></i></button>
                                @endif
                                @if(($user->id === \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_self_overview_history')) || ($user->id !== \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_other_overview_history')))
                                    <button wire:click="openHistoryModalPopover('{{ $key->data_key }}')"><i class="fa-solid fa-clock-rotate-left"></i></button>
                                @endif
                            </td></tr>
                        @endforeach
                    </table>
            </div>
            @endforeach
        </div>
    </div>
</div>
