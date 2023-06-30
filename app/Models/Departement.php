<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Departement Model
 *
 * Eine Abteilung, welche Mitarbeiter:innen zugewiesen werden kann.
 */
class Departement extends Model
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
        'friendly_name',
        'head_of_departement'
    ];

    /**
     * Initialisierung und Konfigurierung der Klasse ActivityLog.
     * Diese Klasse stammt aus der Erweiterung spatie/laravel-activitylog und ermöglicht das automatische Speichern von Modellattributsänderungen in die Datenbank.
     *
     * Setzt das Attribut LogName auf "departement".
     *
     * @return LogOptions das konfigurierte LogOptions-Objekt.
     *
     * @access public
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('departement')
        ->logFillable();
    }

    /**
     * Gibt die Abteilungsleitung als User-Objekt zurück.
     *
     * @return ?User die Abteilungsleitung als User-Objekt oder null.
     *
     * @access public
     */
    public function getHeadOfDepartementUserAttribute(): ?User
    {
        return User::findOrFail($this->head_of_departement);
    }
}
