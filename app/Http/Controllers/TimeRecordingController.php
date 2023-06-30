<?php

namespace App\Http\Controllers;

use App\Models\TimeRecording;
use DateTime;

class TimeRecordingController extends Controller
{
    private $user;
    private $is_running;

    /**
     * Erstellt eine neue Instanz des TimeRecordingController.
     *
     * @param mixed $user der Benutzer, dem der Controller zugeordnet ist.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Ruft die aktuelle Zeiterfassung des Benutzers ab.
     *
     * @return array ein assoziatives Array mit den Schlüsseln 'isRunning' (boolean) und 'timeRecording' (TimeRecording-Objekt).
     */
    public function getCurrentTimeRecording(): array
    {
        $timeRecordings = TimeRecording::where('user_id', $this->user->id)
            ->where('status', 'running')
            ->orderBy('start_time', 'desc')
            ->limit(1)
            ->get();

        $this->is_running = count($timeRecordings) > 0;

        return [
            'isRunning' => $this->is_running,
            'timeRecording' => $this->is_running ? $timeRecordings[0] : null,
        ];
    }

    /**
     * Startet eine neue Zeiterfassung mit optionaler Tätigkeit.
     *
     * @param string|null $work_activity die Tätigkeit (Standardwert: null).
     *
     * @return void
     */
    public function start($work_activity = null): void
    {
        $this->stop();

        TimeRecording::updateOrCreate(['id' => null], [
            'user_id' => $this->user->id,
            'start_time' => (new DateTime)->format("Y-m-d H:i:s"),
            'duration' => 0,
            'status' => 'running',
            'work_activity' => $work_activity,
        ]);

        $this->getCurrentTimeRecording();
    }

    /**
     * Stoppt die aktuelle Zeiterfassung, sofern eine läuft.
     *
     * @return void
     */
    public function stop(): void
    {
        $currentTimeRecording = $this->getCurrentTimeRecording();

        if ($currentTimeRecording["isRunning"]) {
            $endTime = new DateTime;
            $duration = $endTime->getTimestamp() - $currentTimeRecording["timeRecording"]->getStartDateTime()->getTimestamp();

            TimeRecording::updateOrCreate(['id' => $currentTimeRecording["timeRecording"]->id], [
                'end_time' => $endTime->format("Y-m-d H:i:s"),
                'duration' => $duration,
                'status' => 'completed',
            ]);
        }
    }
}
