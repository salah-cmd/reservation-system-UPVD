<?php

namespace App\Http\Controllers;
use App\Repositories\DashboardRepository;


class EnseignantController extends Controller
{
    public function info(DashboardRepository $dashboardRepo)
    {
        $user = session('user');
        $userId = $user['idUtilisateur'];

        $stats = $dashboardRepo->getAdminStats($userId);

        $etudiants = $dashboardRepo->getEtudiantDash($userId);

        return view('dashboard.enseignant.home', compact('user', 'stats', 'etudiants'));
    }
}
