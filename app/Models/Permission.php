<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Permission Model
 *
 * Berechtigungen sind vordefinierte Datenbankeinträge und können nur direkt über das Datenbank-Administrationspanel geändert werden.
 * Eine Berechtigung hat einen Schlüssel und eine Beschreibung.
 */
class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * Die zuweisbaren Attribute.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'friendly_name',
        'description',
        'parent'
    ];
}
