<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PermissionController;
use App\Models\WorkActivity;
use Livewire\Component;

class TimeRecordingNavigation extends Component
{
    // Eigenschaften
    public $time_recording_is_running, $current_time_recording_start_time, $tr_activities;
    private $current_time_recording;

    /**
     * Render-Methode für das Livewire-Komponenten-View.
     * Hier werden die erforderlichen Daten abgerufen und an das View zurückgegeben.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        $this->getCurrentTimeRecording();

        // Aktualisieren der Eigenschaften basierend auf dem aktuellen Zeitdokument
        $this->time_recording_is_running = $this->current_time_recording["isRunning"];
        $this->current_time_recording_start_time = $this->time_recording_is_running ? $this->current_time_recording["timeRecording"]->getStartDateTime()->format('H:i') : '';
        $this->tr_activities = WorkActivity::all();

        return view('livewire.time-recording-navigation');
    }

    /**
     * Ruft die aktuelle Zeiterfassung des angemeldeten Users ab.
     *
     * @return void
     */
    public function getCurrentTimeRecording(): void
    {
        $timeRecordingController = new \App\Http\Controllers\TimeRecordingController(\Auth::user());
        $this->current_time_recording = $timeRecordingController->getCurrentTimeRecording();
    }

    /**
     * Startet die Zeiterfassung des angemeldeten Users.
     *
     * @return void
     */
    public function startTimeRecording(): void
    {
        PermissionController::authUserHasOrAbort('timerecording_browser_realtime');

        $timeRecordingController = new \App\Http\Controllers\TimeRecordingController(\Auth::user());
        $timeRecordingController->start();
        $this->getCurrentTimeRecording();
    }

    /**
     * Stoppt die Zeiterfassung des angemeldeten Users.
     *
     * @return void
     */
    public function stopTimeRecording(): void
    {
        PermissionController::authUserHasOrAbort('timerecording_browser_realtime');

        $timeRecordingController = new \App\Http\Controllers\TimeRecordingController(\Auth::user());
        $timeRecordingController->stop();
        $this->getCurrentTimeRecording();
    }
}
