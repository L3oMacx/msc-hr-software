<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use DateTime;
use stdClass;

/**
 * User Model
 *
 * Ein Benuter bzw. Mitarbeiter, der über Login-Daten verfügt und in der Anwendung navigieren kann.
 */
class User extends Authenticatable
{
    use SoftDeletes;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use LogsActivity;

    /**
     * Die zuweisbaren Attribute.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * Die Attribute, die für die Serialisierung ausgeblendet werden sollen.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'permission_role',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Initialisierung und Konfigurierung der Klasse ActivityLog.
     * Diese Klasse stammt aus der Erweiterung spatie/laravel-activitylog und ermöglicht das automatische Speichern von Modellattributsänderungen in die Datenbank.
     *
     * Setzt das Attribut LogName auf "user".
     *
     * @return LogOptions das konfigurierte LogOptions-Objekt.
     *
     * @access public
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('user')
        ->logFillable();
    }

    /**
     * Abfragen eines Datensatzes.
     *
     * @param string $key Schlüssel des Datensatzes
     *
     * @return mixed
     *
     * @access public
     */
    public function get($key): mixed
    {
        $custom_keys = [
            'fullname',
            'identifier'
        ];

        if(in_array($key, $custom_keys)) {
            if($key == 'fullname') {
                $entry = new stdClass(); // empty Class
                $entry->user_id = $this->id;
                $entry->data_key = $key;
                $entry->value = $this->get('fname')->value.' '.$this->get('lname')->value;
            } else if($key == 'identifier') {
                $entry = new stdClass(); // empty Class
                $entry->user_id = $this->id;
                $entry->data_key = $key;
                $entry->value = $this->get('fname')->value.' '.$this->get('lname')->value.' ('.$this->id.')';
            }

        } else {
            $entry = UserData::where('user_id', $this->id)
            ->where('data_key', $key)
            ->where(function($q) {
                $q->where('valid_from', '<', new DateTime())
                ->orWhere('valid_from', null);

            })
            ->where(function($q) {
                $q->where('valid_to', '>', new DateTime())
                ->orWhere('valid_to', null);

            })
            ->orderBy('updated_at', 'desc')
            ->first();

            if($entry === null) {
                $entry = new UserData();
                $entry->user_id = $this->id;
                $entry->data_key = $key;
            }

        }

        return $entry;
    }

    /**
     * Abfragen der Historie eines Datensatzes.
     *
     * @param string $key Schlüssel des Datensatzes
     *
     * @return mixed
     *
     * @access public
     */
    public function getDataHistory($key): mixed
    {
        $entries = UserData::where('user_id', $this->id)
            ->where('data_key', $key)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach($entries as $entry) {
            $entry->activities = Activity::where('log_name' , 'user_data')->where('subject_id', $entry->id)->get();
            foreach($entry->activities as $a) {
                $a->causer_user = User::findOrFail($a->causer_id);
            }
        }


        return $entries;
    }

    /**
     * Setzen eines Datensatzes
     *
     * @param string $key Schlüssel des Datensatzes
     * @param mixed $value Neuer Wert des Datensatzes
     * @param ?string $valid_from Zeitstempel, ab wann der neue Wert gültig ist
     * @param ?string $valid_to Zeitstempel, bis wann der neue Wert gültig ist
     *
     * @return void
     *
     * @access public
     */
    public function set($key, $value = null, $valid_from = null, $valid_to = null): void
    {

        $userData = new UserData();
        $userData->user_id = $this->id;
        $userData->data_key = $key;
        $userData->data_value = $value;
        $userData->valid_from = $valid_from === null ? null : new DateTime($valid_from);
        $userData->valid_to = $valid_to === null ? null : new DateTime($valid_to);
        $userData->save();

        if($key == 'fname' || $key == 'lname') {
            $this->name = $this->get('fname')->value . ' ' . $this->get('lname')->value;
            $this->save();
        }

        if($key == 'email') {
            $this->email = $this->get('email')->value;
            $this->save();
        }

    }

    /**
     * Abrufen der Berechtigungsrolle (PermissionRole)
     *
     * @return PermissionRole
     *
     * @access public
     */
    public function getPermissionRole(): PermissionRole
    {
        if($this->get('permission_role')->raw_value === null)
            return PermissionRole::getDefault();
        else
            return PermissionRole::findOrFail($this->get('permission_role')->raw_value);
    }

    /**
     * Abfragen, ob der User eine Berechtigung (Permission) besitzt.
     *
     * @return bool
     *
     * @access public
     */
    public function hasPermission($permission_name): bool
    {
        $role = $this->getPermissionRole();
        foreach($role->permissions as $permission) {
            if($permission->name === $permission_name)
                return true;
        }
        return false;
    }

}
