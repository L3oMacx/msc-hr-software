<?php

namespace App\Models;

use App\Http\Controllers\PermissionController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use \Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use \DateTime;

/**
 * TimeRecording Model
 *
 * Ein Zeiterfassungs-Eintrag.
 */
class TimeRecording extends Model
{
    use SoftDeletes;
    use LogsActivity;

    /**
     * Die zuweisbaren Attribute.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'duration',
        'work_activity',
        'status',
        'request_values',
    ];

    /**
     * Initialisierung und Konfigurierung der Klasse ActivityLog.
     * Diese Klasse stammt aus der Erweiterung spatie/laravel-activitylog und ermöglicht das automatische Speichern von Modellattributsänderungen in die Datenbank.
     *
     * Setzt das Attribut LogName auf "time_recording".
     *
     * @return LogOptions das konfigurierte LogOptions-Objekt.
     *
     * @access public
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('time_recording')
        ->logFillable();
    }

    /**
     * Abrufen des formatierten Attributs start_time.
     *
     * @param string $format = 'H:i'
     *
     * @return string
     *
     * @access public
     */
    public function getStartTimeFormatted(string $format = 'H:i'): string
    {
        return $this->start_time === null ? '' : (new DateTime($this->start_time))->format($format);
    }

    /**
     * Abrufen des Attributs start_time als DateTime-Objekt.
     *
     * @return DateTime
     *
     * @access public
     */
    public function getStartDateTime(): DateTime
    {
        return new DateTime($this->start_time);
    }

    /**
     * Abrufen des formatierten Attributs end_time.
     *
     * @param string $format = 'H:i'
     *
     * @return string
     *
     * @access public
     */
    public function getEndTimeFormatted(string $format = 'H:i'): string
    {
        return $this->end_time === null ? '' : (new DateTime($this->end_time))->format($format);
    }

    /**
     * Abrufen des Attributs end_time als DateTime-Objekt.
     *
     * @return DateTime
     *
     * @access public
     */
    public function getEndDateTime(): DateTime
    {
        return new DateTime($this->end_time);
    }

    /**
     * Abrufen des Attributs duration im Stundenformat (2,75 h).
     *
     * @return string
     *
     * @access public
     */
    public function getDurationHours(): string
    {
        return $this->duration == 0 ? '' : round($this->duration/60/60, 2) . ' h';
    }

    /**
     * Liefert das Attribut request_values_array. Gibt ein Array mit allen gespeicherten Anfragedetails dieser Zeiterfassung oder null zurück.
     *
     * @return array|null
     *
     * @access public
     */
    public function getRequestValuesArrayAttribute(): ?array
    {
        if($this->request_values === null)
            return null;

        $ar = json_decode($this->request_values, true);
        $ar["start_time"] = new DateTime($ar["start_time"]);
        $ar["end_time"] = new DateTime($ar["end_time"]);
        $ar["duration_formatted"] = round($ar["duration"]/60/60, 2) . ' h';

        return $ar;
    }

    /**
     * Abrufen des Attributs friendly_status.
     *
     * @return string
     *
     * @access public
     */
    public function getFriendlyStatusAttribute(): string
    {
        if($this->status === 'completed') return '';
        if($this->status === 'running') return 'läuft aktuell';
        if($this->status === 'create-requested') return 'neu beantragt';
        if($this->status === 'update-requested') return 'Änderung beantragt';
        if($this->status === 'denied') return 'Antrag abgelehnt';
        return '';
    }

    /**
     * Aktualisiert oder erstellt eine neue Zeiterfassungsanforderung (je nach Berechtigung). Eine angeforderte Zeiterfassung (neue Zeiterfassung oder aktualisierte Zeiterfassung) muss zunächst vom Vorgesetzten bestätigt werden.
     *
     * @param int|null $id wenn neue Zeiterfassung: null; wenn bearbeitete Zeiterfassung: TimeRecording id
     * @param array $attributes die gewünschten Änderungen (start_time, end_time, ...)
     *
     * @return TimeRecording die erstellte oder aktualisierte TimeRecording
     *
     * @static
     * @access public
     */
    public static function updateOrCreateRequest($id, array $attributes): TimeRecording
    {

        $timerecording = TimeRecording::where('id', $id)->first();

        if($timerecording === null) { // create timerecording
            if(($attributes["user_id"] == Auth::user()->id && PermissionController::authUserHas('user_self_timerecording_create_request') && !PermissionController::authUserHas('user_self_timerecording_create'))
            || ($attributes["user_id"] != Auth::user()->id && PermissionController::authUserHas('user_other_timerecording_create_request') && !PermissionController::authUserHas('user_other_timerecording_create'))) { // has to request first

                $timerecording = new TimeRecording();

                $timerecording->user_id = $attributes["user_id"];
                $timerecording->start_time = $attributes["start_time"];
                $timerecording->end_time = $attributes["end_time"];
                $timerecording->duration = 0;
                $timerecording->status = 'create-requested';
                $timerecording->request_values = json_encode($attributes);

                $timerecording->save();

                return $timerecording;
            }

            if(($attributes["user_id"] == Auth::user()->id && PermissionController::authUserHas('user_self_timerecording_create'))
            || ($attributes["user_id"] != Auth::user()->id && PermissionController::authUserHas('user_other_timerecording_create')))
                return TimeRecording::updateOrCreate(['id' => $id], $attributes);


        } else { // edit timerecording
            if(($attributes["user_id"] == Auth::user()->id && PermissionController::authUserHas('user_self_timerecording_edit_request') && !PermissionController::authUserHas('user_self_timerecording_edit'))
            || ($attributes["user_id"] != Auth::user()->id && PermissionController::authUserHas('user_other_timerecording_edit_request') && !PermissionController::authUserHas('user_other_timerecording_edit'))) { // has to request first

                $timerecording->status = 'update-requested';
                $timerecording->request_values = json_encode($attributes);

                $timerecording->save();

                return $timerecording;
            }

            if(($attributes["user_id"] == Auth::user()->id && PermissionController::authUserHas('user_self_timerecording_edit'))
            || ($attributes["user_id"] != Auth::user()->id && PermissionController::authUserHas('user_other_timerecording_edit')))
                return TimeRecording::updateOrCreate(['id' => $id], $attributes);

        }

        PermissionController::abort();
    }

    /**
     * Abrufen des user-Attributs.
     *
     * @return User der User dieser Zeiterfassung
     *
     * @access public
     */
    public function getUserAttribute(): User
    {
        return User::findOrFail($this->user_id);
    }

    /**
     * Abrufen des Attributs request_string. Gibt die Informationen der Anfrage in einem lesbaren Format zurück.
     *
     * @return ?string
     *
     * @access public
     */
    public function getRequestStringAttribute(): ?string
    {
        if($this->status === 'create-requested') {
            return 'Neue Zeiterfassung wurde beantragt.
            <br>Start: ' . $this->request_values_array["start_time"]->format("d.m.Y H:i") . '
            <br>Ende: ' . $this->request_values_array["end_time"]->format("d.m.Y H:i") . '
            <br>Dauer: '.$this->request_values_array["duration_formatted"];
        } else if($this->status === 'update-requested') {
            return 'Änderung an Zeiterfassung wurde beantragt.
            <br>Start: <s>' . $this->getStartDateTime()->format("d.m.Y H:i") . '</s> → ' . $this->request_values_array["start_time"]->format("d.m.Y H:i") . '
            <br>Ende: <s>' . $this->getEndDateTime()->format("d.m.Y H:i") . '</s> → ' . $this->request_values_array["end_time"]->format("d.m.Y H:i") . '
            <br>Dauer: <s>'.$this->getDurationHours().'</s> → '.$this->request_values_array["duration_formatted"];

        }

        return null;
    }

    /**
     * Genehmigung der beantragten Zeiterfassung.
     *
     * @return void
     *
     * @access public
     */
    public function approveRequest(): void
    {

        PermissionController::authUserHasOrAbort('dashboard_requests');

        $this->start_time = $this->request_values_array["start_time"];
        $this->end_time = $this->request_values_array["end_time"];
        $this->duration = $this->request_values_array["duration"];
        $this->work_activity = $this->request_values_array["work_activity"];

        $this->request_values = null;
        $this->status = 'completed';

        $this->save();
    }

    /**
     * Ablehnen der beantragten Zeiterfassung.
     *
     * @return void
     *
     * @access public
     */
    public function denyRequest(): void
    {

        PermissionController::authUserHasOrAbort('dashboard_requests');

        if($this->status === 'create-requested') {
            $this->status = 'denied';
        } else {
            $this->request_values = null;
            $this->status = 'completed';
        }

        $this->save();
    }
}
