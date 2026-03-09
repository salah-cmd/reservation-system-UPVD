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
});


