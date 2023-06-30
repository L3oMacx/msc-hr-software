<?php

namespace App\Http\Controllers;

use \Auth;

class PermissionController extends Controller
{
    /**
     * Überprüft, ob der aktuell authentifizierte Benutzer die angegebene Berechtigung hat.
     *
     * @param string $key der Schlüssel der Berechtigung.
     *
     * @return bool true, wenn der Benutzer die Berechtigung hat, ansonsten false.
     */
    public static function authUserHas(string $key): bool
    {
        $user = Auth::user();

        return $user->hasPermission($key);
    }

    /**
     * Überprüft, ob der aktuell authentifizierte Benutzer die angegebene Berechtigung hat. Wenn nicht, wird ein HTTP-Fehler mit dem angegebenen Fehlercode und Fehlermeldung ausgelöst.
     *
     * @param string $key der Schlüssel der Berechtigung.
     * @param int $error_code der HTTP-Fehlercode (Standardwert: 403).
     * @param string $error_message die Fehlermeldung (Standardwert: 'Kein Zugriff auf diese Seite.').
     *
     * @return void
     */
    public static function authUserHasOrAbort(string $key, int $error_code = 403, string $error_message = 'Kein Zugriff auf diese Seite.'): void
    {
        if (!self::authUserHas($key)) {
            abort($error_code, $error_message);
            die();
        }
    }

    /**
     * Beendet die aktuelle Anfrage mit einem HTTP-Fehler mit dem angegebenen Fehlercode und Fehlermeldung.
     *
     * @param int $error_code der HTTP-Fehlercode (Standardwert: 403).
     * @param string $error_message die Fehlermeldung (Standardwert: 'Kein Zugriff auf diese Seite.').
     *
     * @return void
     */
    public static function abort(int $error_code = 403, string $error_message = 'Kein Zugriff auf diese Seite.'): void
    {
        abort($error_code, $error_message);
        die();
    }
}
