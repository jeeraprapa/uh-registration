<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RegistrationsExport;
use App\Http\Controllers\Controller;
use App\Models\EventSession;
use App\Models\Registration;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        [$query, $filters, $statuses] = $this->filteredQuery($request);

        return view('admin.registrations.index', [
            'registrations' => $query->latest('id')->paginate(20)->withQueryString(),
            'sessions' => EventSession::orderBy('local_date')->orderBy('local_start_time')->get(),
            'filters' => $filters, 'referralSources' => config('registration.referral_sources'), 'emailStatuses' => $statuses,
        ]);
    }

    public function export(Request $request)
    {
        [$query] = $this->filteredQuery($request);

        return response()->streamDownload(function () use ($query) {
            app(RegistrationsExport::class)->write($query);
        }, 'registrations-'.now()->format('Ymd-His').'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store, private',
        ]);
    }

    private function filteredQuery(Request $request): array
    {
        Gate::authorize('manage-admin');
        $statuses = ['pending', 'processing', 'local_preview', 'provider_accepted', 'failed'];
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:150'], 'session_id' => ['nullable', 'integer', 'exists:event_sessions,id'],
            'registered_from' => ['nullable', 'date_format:Y-m-d'],
            'registered_to' => ['nullable', 'date_format:Y-m-d', Rule::when($request->filled('registered_from'), ['after_or_equal:registered_from'])],
            'referral_source' => ['nullable', Rule::in(config('registration.referral_sources'))],
            'email_status' => ['nullable', Rule::in($statuses)],
        ]);
        $query = Registration::with(['eventSession', 'confirmationEmails']);
        if (! empty($filters['session_id'])) {
            $query->where('event_session_id', $filters['session_id']);
        }
        if (! empty($filters['q'])) {
            $term = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $filters['q']);
            $query->where(function ($q) use ($term) {
                $q->whereRaw("email LIKE ? ESCAPE '!'", [$term.'%'])->orWhereRaw("first_name LIKE ? ESCAPE '!'", ['%'.$term.'%'])->orWhereRaw("last_name LIKE ? ESCAPE '!'", ['%'.$term.'%'])->orWhere('reference', $term);
            });
        }
        if (! empty($filters['registered_from'])) {
            $start = Carbon::parse($filters['registered_from'], config('registration.timezone'))->startOfDay()->utc();
            $query->where('created_at', '>=', $start->format('Y-m-d H:i:s'));
        }
        if (! empty($filters['registered_to'])) {
            $end = Carbon::parse($filters['registered_to'], config('registration.timezone'))->startOfDay()->addDay()->utc();
            $query->where('created_at', '<', $end->format('Y-m-d H:i:s'));
        }
        if (! empty($filters['referral_source'])) {
            $query->where('referral_source', $filters['referral_source']);
        }
        if (! empty($filters['email_status'])) {
            $query->whereHas('confirmationEmails', fn ($q) => $q->where('status', $filters['email_status']));
        }

        return [$query, $filters, $statuses];
    }

    public function show(Registration $registration)
    {
        Gate::authorize('manage-admin');
        $registration->load(['eventSession', 'consents.definition', 'confirmationEmails']);

        return view('admin.registrations.show', compact('registration'));
    }
}
