<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * PermissionRoleEntry Model
 *
 * Verbindet PermissionRole (Berechtigungsrolle) and Permission (Berechtigung).
 * Wenn ein User (Benutzer) über eine PermissionRole (Berechtigungsrolle) mit einem PermissionRoleEntry (Berechtigungsrolleneintrag) verfügt, der mit der erforderlichen Permission (Berechtigung) verknüpft ist, wird der Zugriff gewährt.
 */
class PermissionRoleEntry extends Model
{
    use SoftDeletes;
    use LogsActivity;
    use HasFactory;

    /**
     * Die zuweisbaren Attribute.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'permission_role_id',
        'permission_id'
    ];

    /**
     * Initialisierung und Konfigurierung der Klasse ActivityLog.
     * Diese Klasse stammt aus der Erweiterung spatie/laravel-activitylog und ermöglicht das automatische Speichern von Modellattributsänderungen in die Datenbank.
     *
     * Setzt das Attribut LogName auf "permission_role_entry".
     *
     * @return LogOptions das konfigurierte LogOptions-Objekt.
     *
     * @access public
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('permission_role_entry')
        ->logFillable();
    }
}
