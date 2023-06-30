<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PermissionController;
use App\Models\UserDataKey;
use Livewire\Component;
use App\Models\User;
use \DateTime;
use Masmerise\Toaster\Toaster;
use \Auth;


class UserOverview extends Component
{
    // Eigenschaften
    public $user_page = 'overview';
    public $user, $user_id;
    public $data_categories;
    public $data_modal_heading, $data_key, $data_value, $data_valid_from, $data_valid_to, $data_key_object;
    public $data_history_modal_heading, $data_history;
    public $isEditModalOpen = 0;
    public $isHistoryModalOpen = 0;

    /**
     * Initialisiert die Komponente und setzt die User-ID, falls angegeben.
     *
     * @param int|null $id
     * @return void
     */
    public function mount($id = null): void
    {
        $this->user_id = $id ?? Auth::user()->id;
    }

    /**
     * Render-Methode für das Livewire-Komponenten-View.
     * Hier werden die erforderlichen Daten abgerufen und an das View zurückgegeben.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        $this->user = User::findOrFail($this->user_id);

        // Überprüfung der Berechtigungen des Benutzers
        if (($this->user->id === Auth::user()->id && !PermissionController::authUserHas('user_self_overview')) || ($this->user->id !== Auth::user()->id && !PermissionController::authUserHas('user_other_overview'))) {
            PermissionController::abort();
        }

        $this->user_page = 'overview';

        $this->data_categories = UserDataKey::getAllAllowedByCategory($this->user);

        return view('livewire.user.overview');
    }

    /**
     * Erstellt einen neuen Datensatz.
     *
     * @return void
     */
    public function create()
    {
        $this->data_modal_heading = 'hinzufügen';
        $this->resetCreateForm();
        $this->openModalPopover();
    }

    /**
     * Öffnet das Modalfenster zur Datensatzbearbeitung.
     *
     * @return void
     */
    public function openModalPopover()
    {
        $this->isEditModalOpen = true;
    }

    /**
     * Schließt das Modalfenster zur Datensatzbearbeitung.
     *
     * @return void
     */
    public function closeModalPopover()
    {
        $this->isEditModalOpen = false;
    }

    /**
     * Setzt das Formular zur Datensatzerstellung zurück.
     *
     * @return void
     */
    private function resetCreateForm()
    {
        $this->data_value = null;
        $this->data_valid_from = null;
        $this->data_valid_to = null;
        $this->data_key = null;
    }

    /**
     * Öffnet das Modalfenster zur Historieansicht eines Datensatzes.
     *
     * @param string $key
     * @return void
     */
    public function openHistoryModalPopover(string $key)
    {
        if (($this->user->id === Auth::user()->id && PermissionController::authUserHas('user_self_overview_history')) || ($this->user->id !== Auth::user()->id && PermissionController::authUserHas('user_other_overview_history'))) {

            $this->data_history_modal_heading = 'Historie für Datensatz ' . $this->user->get($key)->key->friendly_name;
            $this->data_history = $this->user->getDataHistory($key);
            $this->isHistoryModalOpen = true;
        }
    }

    /**
     * Schließt das Modalfenster zur Historieansicht eines Datensatzes.
     *
     * @return void
     */
    public function closeHistoryModalPopover()
    {
        $this->isHistoryModalOpen = false;
    }

    /**
     * Speichert einen Datensatz.
     *
     * @return void
     */
    public function store()
    {
        if (($this->user->id === Auth::user()->id && PermissionController::authUserHas('user_self_overview_edit')) || ($this->user->id !== Auth::user()->id && PermissionController::authUserHas('user_other_overview_edit'))) {

            $this->validate([
                'data_value' => 'required',
                'data_valid_from' => 'required|date',
                'data_valid_to' => 'date|nullable',
            ]);

            $this->user->set(
                $this->data_key,
                $this->data_value,
                $this->data_valid_from,
                $this->data_valid_to
            );

            Toaster::success($this->data_key ? 'Datensatz wurde aktualisiert.' : 'Datensatz wurde erstellt.');
            $this->closeModalPopover();
            $this->resetCreateForm();
        }
    }

    /**
     * Bearbeitet einen Datensatz.
     *
     * @param string $data_key
     * @return void
     */
    public function edit($data_key)
    {
        if (($this->user->id === Auth::user()->id && PermissionController::authUserHas('user_self_overview_edit')) || ($this->user->id !== Auth::user()->id && PermissionController::authUserHas('user_other_overview_edit'))) {

            $data = $this->user->get($data_key);

            $this->data_modal_heading = $data->key->friendly_name . ' bearbeiten';
            $this->data_value = $data->raw_value;
            $this->data_valid_from = (new DateTime())->format('Y-m-d');
            $this->data_valid_to = null;
            $this->data_key = $data_key;
            $this->data_key_object = $data->key;

            $this->openModalPopover();
        }
    }
}
