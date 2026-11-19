<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    private const KNOWN_LOG_NAMES = [
        'Auth',
        'User',
        'Role',
        'Project',
        'Oka',
        'SapDataset',
        'BillOfMaterial',
        'BillOfMaterialRevision',
        'DaftarKebutuhanMaterial',
        'OfflineRequest',
    ];

    private const KNOWN_EVENTS = [
        'created',
        'updated',
        'deleted',
        'login',
        'logout',
        'login_failed',
        'import',
        'import_queued',
        'export',
        'download',
        'view',
        'role_updated',
        'delete_all',
    ];

    public function __construct()
    {
        $this->middleware('role:admin');
    }

    public function index()
    {
        $logNames = Activity::query()
            ->whereNotNull('log_name')
            ->distinct()
            ->pluck('log_name')
            ->merge(self::KNOWN_LOG_NAMES)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        $events = Activity::query()
            ->whereNotNull('event')
            ->distinct()
            ->pluck('event')
            ->merge(self::KNOWN_EVENTS)
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return view('pages.activity-log.index', [
            'users' => User::orderBy('name')->get(['id', 'name', 'email', 'nip']),
            'logNames' => $logNames,
            'events' => $events,
        ]);
    }

    public function data(Request $request)
    {
        if (! $request->ajax()) {
            abort(404);
        }

        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
        ]);

        $query = Activity::query()->with('causer');

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('log_name', 'like', "%{$search}%")
                    ->orWhere('feature', 'like', "%{$search}%")
                    ->orWhere('event', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
                    ->orWhere('subject_id', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($causer) use ($search) {
                        $causer->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('nip', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('user_id')) {
            $query->where('causer_type', User::class)
                ->where('causer_id', $request->input('user_id'));
        }

        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        if ($request->filled('log_name')) {
            $logName = $request->input('log_name');
            $query->where(function ($q) use ($logName) {
                $q->where('log_name', $logName)
                    ->orWhere('feature', $logName);
            });
        }

        if (! empty($validated['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($validated['date_from'])->startOfDay());
        }

        if (! empty($validated['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($validated['date_to'])->endOfDay());
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = max(1, min($perPage, 100));

        $activities = $query->orderByDesc('id')->paginate($perPage);

        $activities->setCollection(
            $activities->getCollection()->map(function (Activity $activity) {
                $properties = $this->properties($activity);
                $causer = $activity->causer;

                return [
                    'id' => $activity->id,
                    'created_at' => optional($activity->created_at)->format('d-m-Y H:i:s'),
                    'user' => $causer->name ?? ($properties['causer_name'] ?? 'Sistem'),
                    'user_email' => $causer->email ?? null,
                    'role' => $this->roleNames($causer),
                    'log_name' => $activity->log_name ?? $activity->feature,
                    'event' => $activity->event,
                    'description' => $activity->description,
                    'subject' => $activity->subject_type
                        ? class_basename($activity->subject_type)
                        : null,
                    'subject_id' => $activity->subject_id,
                    'ip' => $properties['ip'] ?? null,
                    'url' => $properties['url'] ?? null,
                    'detail_url' => route('activity-log.show', $activity->id),
                ];
            })
        );

        return response()->json($activities);
    }

    public function show(Activity $activity)
    {
        $properties = $this->properties($activity);
        $causer = $activity->causer;

        $old = $properties['old'] ?? null;
        $new = $properties['new'] ?? null;

        unset($properties['old'], $properties['new']);

        return response()->json([
            'id' => $activity->id,
            'created_at' => optional($activity->created_at)->format('d-m-Y H:i:s'),
            'description' => $activity->description,
            'event' => $activity->event,
            'log_name' => $activity->log_name ?? $activity->feature,
            'user' => $causer->name ?? ($properties['causer_name'] ?? 'Sistem'),
            'user_email' => $causer->email ?? null,
            'role' => $this->roleNames($causer),
            'subject_type' => $activity->subject_type,
            'subject_id' => $activity->subject_id,
            'ip' => $properties['ip'] ?? null,
            'url' => $properties['url'] ?? null,
            'user_agent' => $properties['user_agent'] ?? null,
            'old' => $old,
            'new' => $new,
            'properties' => $properties,
        ]);
    }

    private function properties(Activity $activity): array
    {
        $properties = $activity->properties;

        if ($properties instanceof Collection) {
            return $properties->toArray();
        }

        return (array) $properties;
    }

    private function roleNames($causer): ?string
    {
        if (! $causer || ! method_exists($causer, 'getRoleNames')) {
            return null;
        }

        try {
            return $causer->getRoleNames()->join(', ') ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
