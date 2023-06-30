<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PermissionController;
use App\Models\WorkActivity;
use Livewire\Component;
use App\Models\User;
use App\Models\TimeRecording;
use \DateTime;
use Masmerise\Toaster\Toaster;
use \Auth;


class UserTimeRecording extends Component
{

    // Eigenschaften
    public $user_page = 'timerecording';
    public $user, $user_id, $calendar_rows, $calendar_start_date, $calendar_end_date;
    public $name, $email;
    public $tr_activities;
    public $tr_id, $tr_date, $tr_start, $tr_end, $tr_activity;
    public $permissions;
    public $isModalOpen = 0;

    /**
     * Initialisiert die Komponente.
     *
     * @param int|null $id
     * @return void
     */
    public function mount($id = null): void
    {
        $this->user_id = $id ?? Auth::user()->id;
    }

    /**
     * Render-Methode für das Livewire-Komponenten-View.
     * Hier werden die erforderlichen Daten abgerufen und an das View zurückgegeben.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        $this->user = User::findOrFail($this->user_id);
        $this->calendar_start_date = (new DateTime())->modify('-3 months');
        $this->calendar_end_date = new DateTime();
        $this->calendar_rows = $this->getCalendarTable();
        $this->tr_activities = WorkActivity::all();

        return view('livewire.user.timerecording');
    }

    /**
     * Erstellt eine neue Zeiterfassung.
     *
     * @return void
     */
    public function create()
    {
        $this->resetCreateForm();
        $this->openModalPopover();
    }

    /**
     * Öffnet das Modalfenster zur Zeiterfassungserstellung.
     *
     * @return void
     */
    public function openModalPopover()
    {
        $this->isModalOpen = true;
    }

    /**
     * Schließt das Modalfenster zur Zeiterfassungserstellung.
     *
     * @return void
     */
    public function closeModalPopover()
    {
        $this->isModalOpen = false;
    }

    /**
     * Setzt das Formular zur Zeiterfassungserstellung zurück.
     *
     * @return void
     */
    private function resetCreateForm()
    {
        $this->tr_id = null;
        $this->tr_date = (new DateTime())->format('Y-m-d');
        $this->tr_start = '';
        $this->tr_end = '';
        $this->tr_activity = '';
    }

    /**
     * Speichert eine Zeiterfassung.
     *
     * @return void
     */
    public function store()
    {
        // Überprüfen der Berechtigungen basierend auf Benutzer und Aktion
        if ($this->tr_id === null) { // Neue Zeiterfassung erstellen
            $allowed = ($this->user->id === Auth::user()->id && (PermissionController::authUserHas('user_self_timerecording_create_request') || PermissionController::authUserHas('user_self_timerecording_create'))) || ($this->user->id !== Auth::user()->id && (PermissionController::authUserHas('user_other_timerecording_create_request') || PermissionController::authUserHas('user_other_timerecording_create')));
        } else { // Zeiterfassung bearbeiten
            $allowed = ($this->user->id === Auth::user()->id && (PermissionController::authUserHas('user_self_timerecording_edit_request') || PermissionController::authUserHas('user_self_timerecording_edit'))) || ($this->user->id !== Auth::user()->id && (PermissionController::authUserHas('user_other_timerecording_edit_request') || PermissionController::authUserHas('user_other_timerecording_edit')));
        }

        if (!$allowed) {
            return;
        }

        $this->validate([
            'tr_date' => 'required|date',
            'tr_start' => 'required',
            'tr_end' => 'required',
            'tr_activity' => 'required|numeric',
        ]);

        $start_time = new DateTime($this->tr_date . ' ' . $this->tr_start);
        $end_time = new DateTime($this->tr_date . ' ' . $this->tr_end);
        if ($end_time <= $start_time) {
            $end_time->modify('+1 day');
        }

        $duration = $end_time->getTimestamp() - $start_time->getTimestamp();

        $time_recording = TimeRecording::updateOrCreateRequest($this->tr_id, [
            'user_id' => $this->user_id,
            'start_time' => $start_time->format("Y-m-d H:i:s"),
            'end_time' => $end_time->format("Y-m-d H:i:s"),
            'duration' => $duration,
            'status' => 'completed',
            'work_activity' => $this->tr_activity,
        ]);

        if ($time_recording->status === 'create-requested' || $time_recording->status === 'update-requested') {
            Toaster::success($this->tr_id ? 'Änderung der Zeiterfassung wurde beantragt.' : 'Neue Zeiterfassung wurde beantragt.');
        } else {
            Toaster::success($this->tr_id ? 'Zeiterfassung wurde aktualisiert.' : 'Zeiterfassung wurde erstellt.');
        }

        $this->closeModalPopover();
        $this->resetCreateForm();
    }

    /**
     * Bearbeitet eine Zeiterfassung.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        $time_recording = TimeRecording::findOrFail($id);
        $this->tr_id = $id;
        $this->user_id = $time_recording->user_id;
        $this->tr_date = (new DateTime($time_recording->start_time))->format("Y-m-d");
        $this->tr_start = (new DateTime($time_recording->start_time))->format("H:i");
        $this->tr_end = (new DateTime($time_recording->end_time))->format("H:i");
        $this->tr_activity = $time_recording->work_activity;

        $this->openModalPopover();
    }

    /**
     * Löscht eine Zeiterfassung.
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        TimeRecording::find($id)->delete();
        Toaster::success('Zeiterfassung wurde gelöscht.');
    }

    /**
     * Erstellt eine Tabelle mit Zeiterfassungen für den Kalender.
     *
     * @return array
     */
    public function getCalendarTable(): array
    {
        $startDate = $this->calendar_start_date;
        $endDate = $this->calendar_end_date;

        $startDate->setTime(0, 0, 0, 0);
        $endDate->setTime(0, 0, 0, 0);

        $rows = [];
        $weekDuration = 0;
        $lastWeekPos = null;

        for ($day = clone $endDate; $day >= clone $startDate; $day->modify('-1 day')) {
            $timeRecordings = TimeRecording::where('user_id', $this->user_id)
                ->whereDate('start_time', '=', $day->format('Y-m-d'))
                ->orderBy('start_time', 'asc')
                ->get();

            if ($day->format('N') == 7 || $day == $endDate || $day == $startDate) {
                if ($lastWeekPos !== null) {
                    $rows[$lastWeekPos]['weekDuration'] = round($weekDuration / 60 / 60, 2) . ' h';
                }

                if ($day != $startDate) {
                    $weekDuration = 0;
                    $lastWeekPos = count($rows);
                    $rows[] = [
                        'day' => '',
                        'isWeekHeader' => true,
                        'week' => $day->format('W'),
                        'weekDuration' => 0,
                    ];
                }
            }

            if (count($timeRecordings) == 0) {
                $rows[] = [
                    'day' => $day->format("d.m."),
                    'isWeekHeader' => false,
                    'start_time' => '',
                    'end_time' => '',
                    'duration' => '',
                    'status' => '',
                    'editable' => false,
                    'timeRecordingId' => null,
                ];
            } else {
                foreach ($timeRecordings as $i => $timeRecording) {
                    $weekDuration += $timeRecording->duration;

                    if ($i == 0) {
                        $rows[] = [
                            'day' => $day->format("d.m."),
                            'isWeekHeader' => false,
                            'start_time' => $timeRecording->getStartTimeFormatted(),
                            'end_time' => $timeRecording->getEndTimeFormatted(),
                            'duration' => $timeRecording->getDurationHours(),
                            'status' => $timeRecording->friendly_status,
                            'editable' => $timeRecording->status == 'completed',
                            'timeRecordingId' => $timeRecording->id,
                        ];
                    } else {
                        $rows[] = [
                            'day' => '',
                            'isWeekHeader' => false,
                            'start_time' => $timeRecording->getStartTimeFormatted(),
                            'end_time' => $timeRecording->getEndTimeFormatted(),
                            'duration' => $timeRecording->getDurationHours(),
                            'status' => $timeRecording->friendly_status,
                            'editable' => $timeRecording->status == 'completed',
                            'timeRecordingId' => $timeRecording->id,
                        ];
                    }
                }
            }
        }

        return $rows;
    }

    /**
     * Zeigt mehr Einträge im Kalender an.
     *
     * @return void
     */
    public function calendarShowMore(): void
    {
        $this->calendar_start_date->modify('-3 months');

        $this->calendar_rows = $this->getCalendarTable();

        $this->render();
    }
}
