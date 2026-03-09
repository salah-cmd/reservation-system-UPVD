<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EnseignantController;
use Illuminate\Support\Facades\Route;
use function Pest\Laravel\get;

//for debuging use dump or dd functions

Route::get('/', [AuthController::class, 'showLogin']);
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth.session')->group(function () {

    Route::get('/dashboard', function(){
        $user = session('user');

        return match($user['roleUtilisateur']){
            'etudiant' => redirect('/etudiant/dashboard'),
            'enseignant' => redirect('/dashboard/enseignant'),
            'admin' => redirect('/dashboard/admin'),
        };
    });

    Route::get('/dashboard/enseignant', [EnseignantController::class, 'info'])
        ->name('dashboard.enseignant')
        ->middleware('role:enseignant');

//----------------------------------------------ETUDIANT-------------------------------------------------------------------------------------

    Route::get('/etudiant/dashboard', [EtudiantController::class, 'info'])
        ->name('etudiant.home')
        ->middleware('role:etudiant');

    Route::get('/etudiant/materiels',[EtudiantController::class, 'profile'])
        ->name('materiels.page')
        ->middleware('role:etudiant');

    Route::post('/etudiant/materiels-add',[EtudiantController::class, 'store'])
        ->name('materiels.store')
        ->middleware('role:etudiant');

    Route::post('/etudiant/dashboard',[EtudiantController::class, 'annulationReservation'])
        ->name('reservation.update')
        ->middleware('role:etudiant');

    Route::get('/etudiant/parametre', [EtudiantController::class, 'parametre'])
        ->name('parametre.home')
        ->middleware('role:etudiant');

    Route::post('/etudiant/parametre', [EtudiantController::class, 'modifParametre'])
        ->name('parametre.update')
        ->middleware('role:etudiant');

    Route::get('/etudiant/historique', [EtudiantController::class, 'historique'])
        ->name('etudiant.historique')
        ->middleware('role:etudiant');


//-------------------------------------------------------------------------------------------------------------------------------------------


    Route::get('/dashboard/admin', [AdminDashboardController::class, 'index'])
        ->middleware('role:admin');

    Route::get('/dashboard/admin/utilisateurs',[AdminDashboardController::class, 'utilisateurs']
    )->middleware('role:admin');

    Route::post('/dashboard/admin/utilisateurs', [AdminDashboardController::class, 'storeUtilisateur'])
        ->name('admin.utilisateurs.store')->middleware('role:admin');

    Route::put('/dashboard/admin/utilisateurs', [AdminDashboardController::class, 'updateUtilisateur'])
        ->name('admin.utilisateurs.update')->middleware('role:admin');

    Route::get('/dashboard/admin/salles', [AdminDashboardController::class, 'salles']
    )->middleware('role:admin');

    Route::post('/dashboard/admin/salles', [AdminDashboardController::class, 'storeSalle']
    )->name('admin.salles.store')->middleware('role:admin');

    Route::put('/dashboard/admin/salles', [AdminDashboardController::class, 'updateSalle'])
        ->name('admin.salles.update')->middleware('role:admin');

    Route::get('/dashboard/admin/materiels', [AdminDashboardController::class, 'materiels']
    )->middleware('role:admin');

    Route::post('/dashboard/admin/materiels', [AdminDashboardController::class, 'storeMateriel']
    )->name('admin.materiels.store')->middleware('role:admin');

    Route::put('/dashboard/admin/materiels', [AdminDashboardController::class, 'updateMateriel']
    )->name('admin.materiels.update')->middleware('role:admin');

    Route::delete('/dashboard/admin/materiels/{codeMat}', [AdminDashboardController::class, 'destroyMateriel']
    )->name('admin.materiels.destroy')->middleware('role:admin');

    Route::get('/dashboard/admin/reservations', [AdminDashboardController::class, 'reservations']
    )->middleware('role:admin');

    Route::get('/dashboard/admin/reservations/{id}', [AdminDashboardController::class, 'getReservationDetails']
    )->middleware('role:admin');

    Route::post('/dashboard/admin/reservations/{id}/valider', [AdminDashboardController::class, 'validerReservation']
    )->middleware('role:admin');

    Route::post('/dashboard/admin/reservations/{id}/refuser', [AdminDashboardController::class, 'refuserReservation']
    )->middleware('role:admin');
});


