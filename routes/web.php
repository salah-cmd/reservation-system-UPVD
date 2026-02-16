<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AuthController;
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
            'etudiant' => redirect('/dashboard/etudiant'),
            'enseignant' => redirect('/dashboard/enseignant'),
            'admin' => redirect('/dashboard/admin'),
        };
    });

    Route::get('/dashboard/etudiant', function(){
        $user = session('user');
        return "Dashboard Etudiant -- ". $user['idUtilisateur'] ." | ".$user['nomUtilisateur']." | ".$user['prenomUtilisateur']." | ".$user['mailUtilisateur'];
    })->middleware('role:etudiant');

    Route::get('/dashboard/enseignant', function(){
        $user = session('user');
        return "Dashboard Enseignant -- ". $user['idUtilisateur'] ." | ".$user['nomUtilisateur']." | ".$user['prenomUtilisateur']." | ".$user['mailUtilisateur'];
    })->middleware('role:enseignant');

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

});


