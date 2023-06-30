<x-slot name="header">
    @include('livewire.user.header')
</x-slot>
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4">
            <div class="flex flex-row justify-between items-center">
                <div>
                    <h1 class="text-xl font-bold">Zeiterfassung</h1>
                </div>
                @if(($user->id === \Auth::user()->id && (App\Http\Controllers\PermissionController::authUserHas('user_self_timerecording_create_request') || App\Http\Controllers\PermissionController::authUserHas('user_self_timerecording_create'))) || ($user->id !== \Auth::user()->id && (App\Http\Controllers\PermissionController::authUserHas('user_other_timerecording_create_request') || App\Http\Controllers\PermissionController::authUserHas('user_other_timerecording_create'))))
                    <div>
                        <button wire:click="create()"
                            class="my-4 inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-gray-600 text-base font-bold text-white shadow-sm hover:bg-gray-700">
                            Zeiterfassung hinzufügen
                        </button>
                    </div>
                @endif
            </div>
            @if ($isModalOpen)
                @include('livewire.user.timerecording-modal')
            @endif
        </div>
        <div class="columns-1 mt-5">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mb-4">

                <table class="table-fixed w-full">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 w-20">Datum</th>
                            <th class="px-4 py-2">Start</th>
                            <th class="px-4 py-2">Ende</th>
                            <th class="px-4 py-2">Dauer</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($calendar_rows as $r)
                            @if ($r['isWeekHeader'])
                                <tr style="background-color: rgb(189, 189, 189);">
                                    <td class="border px-4 py-2">KW {{ $r['week'] }}</td>
                                    <td class="border px-4 py-2"></td>
                                    <td class="border px-4 py-2"></td>
                                    <td class="border px-4 py-2">{{ $r['weekDuration'] }}</td>
                                    <td class="border px-4 py-2"></td>
                                    <td class="border px-4 py-2"></td>
                                </tr>
                            @else
                                <tr>
                                    <td class="border px-4 py-2">{{ $r['day'] }}</td>
                                    <td class="border px-4 py-2">{{ $r['start_time'] }}</td>
                                    <td class="border px-4 py-2">{{ $r['end_time'] }}</td>
                                    <td class="border px-4 py-2">{{ $r['duration'] }}</td>
                                    <td class="border px-4 py-2">{{ $r['status'] }}</td>
                                    <td class="border px-4 py-2">
                                        @if ($r['editable'])
                                            @if(($user->id === \Auth::user()->id && (App\Http\Controllers\PermissionController::authUserHas('user_self_timerecording_edit_request') || App\Http\Controllers\PermissionController::authUserHas('user_self_timerecording_edit'))) || ($user->id !== \Auth::user()->id && (App\Http\Controllers\PermissionController::authUserHas('user_other_timerecording_edit_request') || App\Http\Controllers\PermissionController::authUserHas('user_other_timerecording_edit'))))
                                                <button wire:click="edit({{ $r['timeRecordingId'] }})"><i class="fa-solid fa-pen"></i></button>
                                            @endif
                                            @if(($user->id === \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_self_timerecording_delete')) || ($user->id !== \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_other_timerecording_delete')))
                                                <button wire:click="delete({{ $r['timeRecordingId'] }})"><i class="fa-solid fa-trash"></i></button>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <button wire:click="calendarShowMore()"
                    class="my-4 inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-gray-600 text-base font-bold text-white shadow-sm hover:bg-gray-700">
                    Weitere Einträge laden
                </button>
            </div>
        </div>
    </div>
</div>
