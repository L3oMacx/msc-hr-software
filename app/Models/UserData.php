<?php

namespace App\Models;

use Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

/**
 * UserData Model
 *
 * Eine UserData ist ein Datensatz, der einem User zugeordnet wird. Als UserData werden persönliche Daten oder Unternehmensdaten wie die Abteilung (Departement) oder die Firma (Company) gespeichert. UserDatas können die Attribute valid_from (gültig ab) oder valid_to (gültig bis) besitzen, wodurch eine Historie der gespeicherten Nutzerdaten entsteht.
 *
 * Jede UserData ist einem UserDataKey zugewiesen, welcher den Schlüssel der gespeicherten Daten identifiziert (z. B. Vorname, Straße, etc.)
 */
class UserData extends Model
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
        'user_id',
        'data_key',
        'data_value',
        'valid_from',
        'valid_to'
    ];

    /**
     * Initialisiert und konfiguriert die Klasse ActivityLog.
     * Diese Klasse stammt aus der Erweiterung spatie/laravel-activitylog und ermöglicht das automatische Speichern von Modellattributsänderungen in der Datenbank.
     *
     * Setzt das Attribut LogName auf "user_data".
     *
     * @return LogOptions das konfigurierte LogOptions-Objekt.
     *
     * @access public
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user_data')
            ->logFillable();
    }

    /**
     * Gibt das UserDataKey-Objekt für den Datenkey zurück.
     *
     * @return UserDataKey das UserDataKey-Objekt.
     *
     * @access public
     */
    public function getKeyAttribute(): UserDataKey
    {
        return UserDataKey::where('data_key', $this->data_key)
            ->first();
    }

    /**
     * Gibt den entschlüsselten Wert der Daten zurück.
     *
     * @return string|null der entschlüsselte Wert oder null, wenn der Wert nicht vorhanden ist.
     *
     * @access public
     */
    public function getValueAttribute(): ?string
    {
        return $this->parseGetValue($this->data_value);
    }

    /**
     * Gibt den Rohwert der Daten zurück (ohne Entschlüsselung oder Formatierung).
     *
     * @return string|null der Rohwert oder null, wenn der Wert nicht vorhanden ist.
     *
     * @access public
     */
    public function getRawValueAttribute(): ?string
    {
        return $this->parseGetValue($this->data_value, true);
    }

    /**
     * Mutator für das Setzen eines Wertes. Diese Methode wird automatisch aufgerufen, sobald das Attribut data_value verändert werden soll.
     *
     * @return Attribute das Attribute-Objekt.
     *
     * @access protected
     */
    protected function dataValue(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $this->parseSetValue($value)
        );
    }

    /**
     * Verarbeitet den Wert vor dem Schreiben.
     *
     * @param mixed $value der zu verarbeitende Wert.
     *
     * @return string|null der verarbeitete und verschlüsselte Wert oder null, wenn der Wert null ist.
     *
     * @access private
     */
    private function parseSetValue($value): ?string
    {
        if ($this->key->encrypted) {
            $value = $this->encrypt($value);
        }

        return $value;
    }

    /**
     * Verarbeitet den Wert vor dem Lesen.
     *
     * @param string|null $value der zu verarbeitende Wert.
     * @param bool $raw gibt an, ob der Rohwert zurückgegeben werden soll.
     *
     * @return string|null der verarbeitete Wert oder null, wenn der Wert nicht vorhanden ist.
     *
     * @access private
     */
    private function parseGetValue($value, $raw = false): ?string
    {
        if ($this->key->encrypted) {
            $value = $this->decrypt($value);
        }

        if ($this->key->input_type === 'select' && !$raw) {
            foreach ($this->key->select_options as $option) {
                if ($option["id"] == $value) {
                    $value = $option["name"];
                }
            }
        }

        return $value;
    }

    /**
     * Verschlüsselt den Wert.
     *
     * @param mixed $value der zu verschlüsselnde Wert.
     *
     * @return string|null der verschlüsselte Wert oder null, wenn der Wert null ist.
     *
     * @access private
     */
    private function encrypt($value): ?string
    {
        if ($value === null) {
            $encryptedValue = null;
        } else {
            $encryptedValue = Crypt::encryptString($value);
        }

        return $encryptedValue;
    }

    /**
     * Entschlüsselt den Wert.
     *
     * @param string|null $value der zu entschlüsselnde Wert.
     *
     * @return string|null der entschlüsselte Wert oder null, wenn der Wert nicht entschlüsselt werden kann.
     *
     * @access private
     */
    private function decrypt($value): ?string
    {
        try {
            $decryptedValue = Crypt::decryptString($value);
        } catch (DecryptException $e) {
            $decryptedValue = null;
        }

        return $decryptedValue;
    }
}
