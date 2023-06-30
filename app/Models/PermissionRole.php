<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * PermissionRole Model
 *
 * Eine PermissionRole (Berechtigungsrolle) kann mehrere PermissionRoleEntries (Berechtigungsrolleneinträge) haben, die die PermissionRole mit einer Permission (Berechtigung) verknüpfen.
 */
class PermissionRole extends Model
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
        'description',
        'is_default'
    ];

    /**
     * Initialisierung und Konfigurierung der Klasse ActivityLog.
     * Diese Klasse stammt aus der Erweiterung spatie/laravel-activitylog und ermöglicht das automatische Speichern von Modellattributsänderungen in die Datenbank.
     *
     * Setzt das Attribut LogName auf "permission_role".
     *
     * @return LogOptions das konfigurierte LogOptions-Objekt.
     *
     * @access public
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->useLogName('permission_role')
        ->logFillable();
    }

    /**
     * Ermittelt die Standard-PermissionRole der Anwendung. Die Standard-PermissionRole besitzt das Attribut is_default = 1.
     *
     * @return PermissionRole die Standard-PermissionRole.
     *
     * @access public
     */
    public static function getDefault(): PermissionRole
    {
        return PermissionRole::where('is_default', 1)->first();
    }

    /**
     * Gibt alle Permissions (Berechtigungen) dieser PermissionRole zurück.
     *
     * @return array<Permission> alle Permissions (Berechtigungen) dieser PermissionRole.
     *
     * @access public
     */
    public function getPermissionsAttribute(): array
    {
        $permissions = [];

        $entries = PermissionRoleEntry::where('permission_role_id', $this->id)->get();

        foreach ($entries as $entry) {
            $permissions[] = Permission::findOrFail($entry->permission_id);
        }

        return $permissions;
    }

    /**
     * Überprüft, ob die PermissionRole die angegebene Berechtigung (Permission) hat.
     *
     * @param int $permission_id Die ID der Berechtigung.
     * @return bool True, wenn die PermissionRole die Berechtigung hat, sonst False.
     *
     * @access public
     */
    public function hasPermission(int $permission_id): bool
    {
        return PermissionRoleEntry::where('permission_role_id', $this->id)->where('permission_id', $permission_id)->first() !== null;
    }

    /**
     * Fügt der PermissionRole eine Berechtigung (Permission) hinzu.
     *
     * @param int $permission_id Die ID der Berechtigung.
     * @return void
     *
     * @access public
     */
    public function addPermission(int $permission_id): void
    {
        $permission_role_entry = new PermissionRoleEntry();

        $permission_role_entry->permission_role_id = $this->id;
        $permission_role_entry->permission_id = $permission_id;

        $permission_role_entry->save();
    }

    /**
     * Entfernt eine Berechtigung (Permission) von der PermissionRole.
     *
     * @param int $permission_id Die ID der Berechtigung.
     * @return void
     *
     * @access public
     */
    public function removePermission(int $permission_id): void
    {
        $permission_role_entries = PermissionRoleEntry::where('permission_role_id', $this->id)->where('permission_id', $permission_id)->get();

        foreach ($permission_role_entries as $permission_role_entry) {
            $permission_role_entry->delete();
        }
    }
}
