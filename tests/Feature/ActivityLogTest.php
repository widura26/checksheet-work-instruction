<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use DatabaseTransactions;

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate($role, 'web'));

        return $user;
    }

    public function test_admin_can_open_activity_log_page(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->get('/activity-log')
            ->assertOk()
            ->assertSee('Activity Log')
            ->assertSee('Riwayat aktivitas pengguna');
    }

    public function test_viewer_cannot_open_activity_log_page(): void
    {
        $viewer = $this->userWithRole('viewer');

        $this->actingAs($viewer)->get('/activity-log')->assertForbidden();

        $this->actingAs($viewer)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->getJson('/activity-log/data')
            ->assertForbidden();
    }

    public function test_admin_can_fetch_activity_log_data(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->getJson('/activity-log/data?per_page=5')
            ->assertOk()
            ->assertJsonStructure(['data', 'total', 'current_page', 'per_page']);
    }

    public function test_model_changes_are_logged_and_sensitive_values_are_redacted(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin);

        $user = User::factory()->create(['password' => 'secret-password']);
        $user->password = 'new-secret-password';
        $user->save();

        $log = Activity::query()
            ->where('event', 'updated')
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($log);
        $this->assertSame($admin->id, $log->causer_id);
        $this->assertSame('***', $log->properties['new']['password']);
        $this->assertSame('***', $log->properties['old']['password']);
    }

    public function test_activity_time_is_displayed_in_indonesia_timezone(): void
    {
        $admin = $this->userWithRole('admin');

        $activity = Activity::create([
            'log_name' => 'Auth',
            'feature' => 'Auth',
            'description' => 'CEK ZONA WAKTU',
            'event' => 'login',
            'created_at' => Carbon::parse('2026-01-01 17:00:00', 'UTC'),
            'updated_at' => Carbon::parse('2026-01-01 17:00:00', 'UTC'),
        ]);

        $response = $this->actingAs($admin)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->getJson('/activity-log/data?search=' . urlencode('CEK ZONA WAKTU'))
            ->assertOk();

        $row = collect($response->json('data'))->firstWhere('id', $activity->id);
        $this->assertNotNull($row);
        $this->assertSame('02-01-2026 00:00:00', $row['created_at']);

        $this->actingAs($admin)
            ->getJson('/activity-log/' . $activity->id)
            ->assertOk()
            ->assertJson(['created_at' => '02-01-2026 00:00:00']);
    }

    public function test_date_filter_uses_indonesia_timezone(): void
    {
        $admin = $this->userWithRole('admin');

        $activity = Activity::create([
            'log_name' => 'Auth',
            'feature' => 'Auth',
            'description' => 'FILTER ZONA WAKTU',
            'event' => 'login',
            'created_at' => Carbon::parse('2026-01-01 17:00:00', 'UTC'),
            'updated_at' => Carbon::parse('2026-01-01 17:00:00', 'UTC'),
        ]);

        $this->actingAs($admin)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->getJson('/activity-log/data?date_from=2026-01-02&date_to=2026-01-02')
            ->assertOk()
            ->assertJsonFragment(['id' => $activity->id]);

        $this->actingAs($admin)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->getJson('/activity-log/data?date_from=2026-01-01&date_to=2026-01-01')
            ->assertOk()
            ->assertJsonMissing(['id' => $activity->id]);
    }

    public function test_login_and_failed_login_are_logged_without_credentials(): void
    {
        $user = User::factory()->create();

        event(new Login('web', $user, false));
        event(new Failed('web', null, ['email' => $user->email, 'password' => 'wrong-password']));

        $this->assertDatabaseHas('activity_log', [
            'event' => 'login',
            'causer_id' => $user->id,
            'log_name' => 'Auth',
        ]);

        $failed = Activity::query()
            ->where('event', 'login_failed')
            ->latest('id')
            ->first();

        $this->assertNotNull($failed);
        $this->assertSame($user->email, $failed->properties['email']);
        $this->assertStringNotContainsString('wrong-password', json_encode($failed->properties));
    }
}
