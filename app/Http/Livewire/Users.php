<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PermissionController;
use App\Models\PermissionRole;
use Livewire\Component;
use App\Models\User;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Facades\Hash;

class Users extends Component
{
    // Eigenschaften
    public $users, $fname, $lname, $password, $email, $user_id;
    public $isModalOpen = 0;

    /**
     * Render-Methode für das Livewire-Komponenten-View.
     * Hier werden die erforderlichen Daten abgerufen und an das View zurückgegeben.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        PermissionController::authUserHasOrAbort('page_team');

        $this->users = User::all();
        return view('livewire.users.users');
    }

    /**
     * Erstellt einen neuen Benutzer.
     *
     * @return void
     */
    public function create()
    {
        $this->resetCreateForm();
        $this->openModalPopover();
    }

    /**
     * Öffnet das Modalfenster zur Benutzererstellung.
     *
     * @return void
     */
    public function openModalPopover()
    {
        $this->isModalOpen = true;
    }

    /**
     * Schließt das Modalfenster zur Benutzererstellung.
     *
     * @return void
     */
    public function closeModalPopover()
    {
        $this->isModalOpen = false;
    }

    /**
     * Setzt das Formular zur Benutzererstellung zurück.
     *
     * @return void
     */
    private function resetCreateForm()
    {
        $this->fname = '';
        $this->lname = '';
        $this->email = '';
        $this->password = '';
    }

    /**
     * Speichert einen Benutzer.
     *
     * @return void
     */
    public function store()
    {
        PermissionController::authUserHasOrAbort('create_user');

        $this->validate([
            'fname' => 'required',
            'lname' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::updateOrCreate(['id' => $this->user_id], [
            'name' => $this->fname . ' ' . $this->lname,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);
        $user->set('fname', $this->fname);
        $user->set('lname', $this->lname);
        $user->set('email', $this->email);
        $user->set('permission_role', PermissionRole::getDefault()->id);
        Toaster::success($this->user_id ? 'Mitarbeiter:in wurde aktualisiert' : 'Mitarbeiter:in wurde erstellt');
        $this->closeModalPopover();
        $this->resetCreateForm();
    }

    /**
     * Löscht einen Benutzer.
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        PermissionController::authUserHasOrAbort('remove_user');

        User::find($id)->delete();
        Toaster::success('Mitarbeiter:in wurde archiviert');
    }
}
