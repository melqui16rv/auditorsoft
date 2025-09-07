<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isSuperAdmin()) {
                abort(403, 'Acceso denegado para este rol.');
            }
            return $next($request);
        });
    }

    /**
     * Dashboard del Super Administrador
     */
    public function dashboard()
    {
        return view('super-admin.dashboard');
    }
}
