<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JefeAuditorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isJefeAuditor()) {
                abort(403, 'Acceso denegado para este rol.');
            }
            return $next($request);
        });
    }

    /**
     * Dashboard del Jefe Auditor
     */
    public function dashboard()
    {
        return view('jefe-auditor.dashboard');
    }
}
