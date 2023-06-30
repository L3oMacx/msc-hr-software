<div>

    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="flex text border-2 border-transparent rounded-full"
                @if ($time_recording_is_running == true) style="color: green;"
                @else
                    style="color: red;" @endif>
                <i class="fa-solid fa-hourglass"></i>
            </button>
        </x-slot>

        <x-slot name="content">
            <div class="p-3">
                <h3 class="text-l font-bold">Zeiterfassung</h3>

                @if ($time_recording_is_running == false)
                    <button
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 transition ease-in-out duration-150" wire:click="startTimeRecording()">
                        Starten
                    </button>
                @elseif ($time_recording_is_running == true)
                    Deine Zeiterfassung läuft seit {{ $current_time_recording_start_time }} Uhr
                    <button
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 transition ease-in-out duration-150" wire:click="stopTimeRecording()">
                        Stoppen
                    </button>
                @endif
            </div>


        </x-slot>
    </x-dropdown>
</div>
