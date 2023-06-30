<?php

namespace App\Models;

use App\Http\Controllers\PermissionController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * UserDataKey Model
 *
 * Jede UserData ist einem UserDataKey zugewiesen, welcher den Schlüssel der gespeicherten Daten identifiziert (z. B. Vorname, Straße, etc.)
 */
class UserDataKey extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    /**
     * Die zuweisbaren Attribute.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'data_key',
        'friendly_name',
        'category',
        'value_type',
        'encrypted'
    ];

    public static $category_friendly_names = [
        'public_profile' => 'Öffentliches Profil',
        'personell_infos' => 'Personalinformationen',
        'address' => 'Anschrift',
        'bank' => 'Bankdaten',
        'payroll' => 'Daten zur Lohnabrechnung',
        'system' => 'System',
    ];

    /**
     * Initialisierung und Konfigurierung der Klasse ActivityLog.
     * Diese Klasse stammt aus der Erweiterung spatie/laravel-activitylog und ermöglicht das automatische Speichern von Modellattributsänderungen in die Datenbank.
     *
     * Setzt das Attribut LogName auf "user_data_key".
     *
     * @return LogOptions das konfigurierte LogOptions-Objekt.
     *
     * @access public
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('user_data_key')
        ->logFillable();
    }

    public static function getAllAllowed($user): mixed
    {

        if(($user->id === \Auth::user()->id && PermissionController::authUserHas('user_self_overview_all')) || ($user->id !== \Auth::user()->id && PermissionController::authUserHas('user_other_overview_all')))
            $allowed = UserDataKey::orderBy('category')
            ->orderBy('id')
            ->get();
        else if(($user->id === \Auth::user()->id && PermissionController::authUserHas('user_self_overview')) || ($user->id !== \Auth::user()->id && PermissionController::authUserHas('user_other_overview')))
            $allowed = UserDataKey::where('category', 'public_profile')
            ->orderBy('category')
            ->orderBy('id')
            ->get();
        else
            $allowed = [];


        return $allowed;
    }

    public static function getAllAllowedByCategory($user): array
    {
        $categories = [];
        $all = self::getAllAllowed($user);

        foreach($all as $k) {
            $categories[$k->category]['keys'][] = $k;
            $categories[$k->category]['friendly_name'] = self::$category_friendly_names[$k->category] ?? null;
        }

        return $categories;
    }

    public function getInputTypeAttribute(): string
    {
        $options = [
            'text' => [
                'text',
            ],
            'select' => [
                'select',
                'company',
                'departement',
                'permission_role',
            ],
        ];

        if(in_array($this->value_type, $options['text']))
            return 'text';
        if(in_array($this->value_type, $options['select']))
            return 'select';

        return 'text';
    }

    public function getSelectOptionsAttribute(): ?array
    {
        $options = [];

        if($this->value_type === 'text')
            return null;

        if($this->value_type === 'company') {
            $objs = Company::select('id', 'friendly_name')->orderBy('friendly_name')->get();
            foreach($objs as $obj) {
                $options[] = [ 'id' => $obj->id, 'name' => $obj->friendly_name ];
            }
            return $options;
        }

        if($this->value_type === 'departement') {
            $objs = Departement::select('id', 'friendly_name')->orderBy('friendly_name')->get();
            foreach($objs as $obj) {
                $options[] = [ 'id' => $obj->id, 'name' => $obj->friendly_name ];
            }
            return $options;
        }


        if($this->value_type === 'permission_role') {
            $objs = PermissionRole::select('id', 'friendly_name')->orderBy('friendly_name')->get();
            foreach($objs as $obj) {
                $options[] = [ 'id' => $obj->id, 'name' => $obj->friendly_name ];
            }
            return $options;
        }
    }
}
