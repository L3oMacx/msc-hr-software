<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Company Model
 *
 * Ein Unternehmen, bei welchem Mitarbeiter:innen angestellt sind.
 */
class Company extends Model
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
        'short_name',
        'address'
    ];


    /**
     * Initialisierung und Konfigurierung der Klasse ActivityLog.
     * Diese Klasse stammt aus der Erweiterung spatie/laravel-activitylog und ermöglicht das automatische Speichern von Modellattributsänderungen in die Datenbank.
     *
     * Setzt das Attribut LogName auf "company".
     *
     * @return LogOptions das konfigurierte LogOptions-Objekt.
     *
     * @access public
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('company')
        ->logFillable();
    }
}
