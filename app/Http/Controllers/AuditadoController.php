<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAuditado()) {
                abort(403, 'Acceso denegado para este rol.');
            }
            return $next($request);
        });
    }

    /**
     * Dashboard del Auditado
     */
    public function dashboard()
    {
        return view('auditado.dashboard');
    }
}
