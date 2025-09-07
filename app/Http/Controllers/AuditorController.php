<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->isAuditor()) {
                abort(403, 'Acceso denegado para este rol.');
            }
            return $next($request);
        });
    }

    /**
     * Dashboard del Auditor
     */
    public function dashboard()
    {
        return view('auditor.dashboard');
    }
}
