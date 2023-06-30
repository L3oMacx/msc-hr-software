<?php

namespace App\Http\Livewire;

use App\Http\Controllers\PermissionController;
use App\Models\Company;
use App\Models\Departement;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\User;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class SystemSettings extends Component
{
    // Eigenschaften
    public $permissions, $permission_roles;
    public $permission_role_name;
    public $companies, $company_id, $company_friendly_name, $company_short_name, $company_address;
    public $departements, $departement_id, $departement_friendly_name, $departement_head, $all_users;
    public $isPermissionRoleModalOpen = 0;
    public $isCompanyModalOpen = 0;
    public $isDepartementModalOpen = 0;

    /**
     * Render-Methode für das Livewire-Komponenten-View.
     * Hier werden die erforderlichen Daten abgerufen und an das View zurückgegeben.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): \Illuminate\Contracts\View\View
    {
        PermissionController::authUserHasOrAbort('system_settings');

        // Bereitstellen der Daten für das View
        $this->permissions = Permission::where('parent', null)
            ->orderBy('friendly_name')
            ->get();

        foreach ($this->permissions as $permission) {
            $childs = Permission::where('parent', $permission->id)
                ->orderBy('friendly_name')
                ->get();

            $permission->childs = $childs;
        }

        $this->permission_roles = PermissionRole::orderBy('id')
            ->get();

        $this->companies = Company::orderBy('friendly_name')
            ->get();

        $this->departements = Departement::orderBy('friendly_name')
            ->get();

        $this->all_users = User::all();

        return view('livewire.system-settings.system-settings');
    }

    /**
     * Schaltet die Berechtigung ein oder aus.
     *
     * @param int $permission_role_id
     * @param int $permission_id
     * @return void
     */
    public function togglePermission(int $permission_role_id, int $permission_id): void
    {
        $permission_role = PermissionRole::findOrFail($permission_role_id);

        if ($permission_role->hasPermission($permission_id))
            $permission_role->removePermission($permission_id);
        else
            $permission_role->addPermission($permission_id);
    }

    /**
     * Erstellt eine neue Firma.
     *
     * @return void
     */
    public function createCompany(): void
    {
        $this->resetCompanyCreateForm();
        $this->openCompanyModalPopover();
    }

    /**
     * Öffnet das Modalfenster zur Erstellung einer Firma.
     *
     * @return void
     */
    public function openCompanyModalPopover(): void
    {
        $this->isCompanyModalOpen = true;
    }

    /**
     * Schließt das Modalfenster zur Erstellung einer Firma.
     *
     * @return void
     */
    public function closeCompanyModalPopover(): void
    {
        $this->isCompanyModalOpen = false;
    }

    /**
     * Setzt das Formular zur Erstellung einer Firma zurück.
     *
     * @return void
     */
    private function resetCompanyCreateForm(): void
    {
        $this->company_id = null;
        $this->company_friendly_name = '';
        $this->company_short_name = '';
        $this->company_address = '';
    }

    /**
     * Bearbeitet eine vorhandene Firma.
     *
     * @param int $id
     * @return void
     */
    public function editCompany(int $id): void
    {
        $company = Company::findOrFail($id);

        $this->company_id = $company->id;
        $this->company_friendly_name = $company->friendly_name;
        $this->company_short_name = $company->short_name;
        $this->company_address = $company->address;

        $this->openCompanyModalPopover();
    }

    /**
     * Löscht eine Firma.
     *
     * @param int $id
     * @return void
     */
    public function deleteCompany(int $id): void
    {
        $company = Company::findOrFail($id);

        $company->delete();
    }

    /**
     * Speichert eine Firma (erstellt oder aktualisiert).
     *
     * @return void
     */
    public function storeCompany(): void
    {
        $this->validate([
            'company_friendly_name' => 'required',
            'company_short_name' => 'required',
            'company_address' => 'required',
        ]);

        $company = Company::updateOrCreate(['id' => $this->company_id], [
            'friendly_name' => $this->company_friendly_name,
            'short_name' => $this->company_short_name,
            'address' => $this->company_address,
        ]);

        Toaster::success($this->company_id ? 'Firma wurde aktualisiert.' : 'Firma wurde erstellt.');

        $this->closeCompanyModalPopover();
        $this->resetCompanyCreateForm();
    }

    /**
     * Erstellt eine neue Abteilung.
     *
     * @return void
     */
    public function createDepartement(): void
    {
        $this->resetDepartementCreateForm();
        $this->openDepartementModalPopover();
    }

    /**
     * Öffnet das Modalfenster zur Erstellung einer Abteilung.
     *
     * @return void
     */
    public function openDepartementModalPopover(): void
    {
        $this->isDepartementModalOpen = true;
    }

    /**
     * Schließt das Modalfenster zur Erstellung einer Abteilung.
     *
     * @return void
     */
    public function closeDepartementModalPopover(): void
    {
        $this->isDepartementModalOpen = false;
    }

    /**
     * Setzt das Formular zur Erstellung einer Abteilung zurück.
     *
     * @return void
     */
    private function resetDepartementCreateForm(): void
    {
        $this->departement_id = null;
        $this->departement_friendly_name = '';
        $this->departement_head = null;
    }

    /**
     * Bearbeitet eine vorhandene Abteilung.
     *
     * @param int $id
     * @return void
     */
    public function editDepartement(int $id): void
    {
        $departement = Departement::findOrFail($id);

        $this->departement_id = $departement->id;
        $this->departement_friendly_name = $departement->friendly_name;
        $this->departement_head = $departement->head_of_departement;

        $this->openDepartementModalPopover();
    }

    /**
     * Löscht eine Abteilung.
     *
     * @param int $id
     * @return void
     */
    public function deleteDepartement(int $id): void
    {
        $departement = Departement::findOrFail($id);

        $departement->delete();
    }

    /**
     * Speichert eine Abteilung (erstellt oder aktualisiert).
     *
     * @return void
     */
    public function storeDepartement(): void
    {
        $this->validate([
            'departement_friendly_name' => 'required',
            'departement_head' => 'required|numeric',
        ]);

        $departement = Departement::updateOrCreate(['id' => $this->departement_id], [
            'friendly_name' => $this->departement_friendly_name,
            'head_of_departement' => $this->departement_head
        ]);

        Toaster::success($this->departement_id ? 'Abteilung wurde aktualisiert.' : 'Abteilung wurde erstellt.');

        $this->closeDepartementModalPopover();
        $this->resetDepartementCreateForm();
    }

    /**
     * Erstellt eine neue Berechtigungsrolle.
     *
     * @return void
     */
    public function createPermissionRole(): void
    {
        $this->resetPermissionRoleCreateForm();
        $this->openPermissionRoleModalPopover();
    }

    /**
     * Öffnet das Modalfenster zur Erstellung einer Berechtigungsrolle.
     *
     * @return void
     */
    public function openPermissionRoleModalPopover(): void
    {
        $this->isPermissionRoleModalOpen = true;
    }

    /**
     * Schließt das Modalfenster zur Erstellung einer Berechtigungsrolle.
     *
     * @return void
     */
    public function closePermissionRoleModalPopover(): void
    {
        $this->isPermissionRoleModalOpen = false;
    }

    /**
     * Setzt das Formular zur Erstellung einer Berechtigungsrolle zurück.
     *
     * @return void
     */
    private function resetPermissionRoleCreateForm(): void
    {
        $this->permission_role_name = '';
    }

    /**
     * Löscht eine Berechtigungsrolle.
     *
     * @param int $id
     * @return void
     */
    public function deletePermissionRole(int $id): void
    {
        if ($id === 1) return;

        $permission_role = PermissionRole::findOrFail($id);

        $permission_role->delete();
    }

    /**
     * Speichert eine Berechtigungsrolle (erstellt oder aktualisiert).
     *
     * @return void
     */
    public function storePermissionRole(): void
    {
        $this->validate([
            'permission_role_name' => 'required',
        ]);

        $permission_role = PermissionRole::updateOrCreate(['id' => null], [
            'friendly_name' => $this->permission_role_name
        ]);

        Toaster::success('Rolle wurde erstellt.');

        $this->closePermissionRoleModalPopover();
        $this->resetPermissionRoleCreateForm();
    }
}
