<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PermissionController;
use App\Models\TimeRecording;
use DateTime;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Dashboard extends Component
{
    /**
     * Die Benutzeranfragen für das Dashboard.
     *
     * @var array
     */
    public $user_requests;

    /**
     * Render-Methode für das Dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        // Überprüfen, ob der authentifizierte Benutzer die Berechtigung für das Dashboard hat
        if (PermissionController::authUserHas('dashboard_requests')) {
            $this->user_requests = $this->getUserRequests();
        }

        return view('livewire.dashboard.dashboard');
    }

    /**
     * Ruft die Benutzeranfragen ab.
     *
     * @return array
     */
    private function getUserRequests(): array
    {
        $all_requests = [];

        // Alle Timerecording-Anfragen abrufen, die sich im Status "create-requested" oder "update-requested" befinden,
        // nach Aktualisierungszeitpunkt absteigend sortiert
        $timerecording_requests = TimeRecording::whereIn('status', ['create-requested', 'update-requested'])
            ->orderBy('updated_at', 'desc')
            ->get();

        // Jede Timerecording-Anfrage in das $all_requests-Array einfügen
        foreach ($timerecording_requests as $tr) {
            $all_requests[] = [
                'id' => $tr->id,
                'user' => $tr->user,
                'model' => 'timerecording',
                'type' => $tr->status,
                'timestamp' => new DateTime($tr->updated_at),
                'request_string' => $tr->request_string,
            ];
        }

        return $all_requests;
    }

    /**
     * Genehmigt eine Benutzeranfrage.
     *
     * @param  string  $model
     * @param  int  $id
     * @return void
     */
    public function approveUserRequest($model, $id): void
    {
        // Wenn das Modell "timerecording" ist, die entsprechende Anfrage genehmigen
        if ($model === 'timerecording') {
            $timerecording = TimeRecording::findOrFail($id);
            $timerecording->approveRequest();
        }

        // Aktualisierte Benutzeranfragen abrufen und Toast-Nachricht anzeigen
        $this->user_requests = $this->getUserRequests();
        Toaster::success('Antrag wurde genehmigt.');
    }

    /**
     * Lehnt eine Benutzeranfrage ab.
     *
     * @param  string  $model
     * @param  int  $id
     * @return void
     */
    public function denyUserRequest($model, $id): void
    {
        // Wenn das Modell "timerecording" ist, die entsprechende Anfrage ablehnen
        if ($model === 'timerecording') {
            $timerecording = TimeRecording::findOrFail($id);
            $timerecording->denyRequest();
        }

        // Aktualisierte Benutzeranfragen abrufen und Toast-Nachricht anzeigen
        $this->user_requests = $this->getUserRequests();
        Toaster::success('Antrag wurde abgelehnt.');
    }
}
