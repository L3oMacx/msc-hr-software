<div class="flex items-center justify-center mb-2">
    <img class="h-12 w-12 rounded-full object-cover" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" />
</div>
<div>
    <h2 class="text-center">{{ $user->get('fullname')->value }}</h2>
</div>

<div class="mt-6 flex items-center">
    @if(($user->id === \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_self_overview')) || ($user->id !== \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_other_overview')))
        <div class="flex-auto py-2 text-center border-b-2 hover:border-cyan-500 cursor-pointer {{ $user_page == 'overview' ? 'border-cyan-600' : 'border-transparent'}}"
        wire:click="dasExisiterttNicht()">
            <a href="/user/{{ $user->id }}">Übersicht</a>
        </div>
    @endif
    @if(($user->id === \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_self_timerecording')) || ($user->id !== \Auth::user()->id && App\Http\Controllers\PermissionController::authUserHas('user_other_timerecording')))
        <div class="flex-auto py-2 text-center border-b-2 hover:border-cyan-500 cursor-pointer {{ $user_page == 'timerecording' ? 'border-cyan-600' : ' border-transparent'}}">
            <a href="/user/{{ $user->id }}/timerecording">Zeiterfassung</a>
        </div>
    @endif
</div>
