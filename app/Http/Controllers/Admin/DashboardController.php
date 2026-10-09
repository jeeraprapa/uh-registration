<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfirmationEmail;
use App\Models\EventSession;
use App\Models\Registration;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __invoke()
    {
        Gate::authorize('manage-admin');

        return view('admin.dashboard', [
            'registrationCount' => Registration::count(), 'guestCount' => Registration::sum('guest_count'),
            'sessions' => EventSession::withCount('registrations')->withSum('registrations', 'guest_count')->orderBy('local_date')->orderBy('local_start_time')->get(),
            'emailCounts' => ConfirmationEmail::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }
}
