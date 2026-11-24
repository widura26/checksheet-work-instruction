<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ViewerAccessTest extends TestCase
{
    use DatabaseTransactions;

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate($role, 'web'));

        return $user;
    }

    public function test_viewer_can_access_material_planning_dashboard(): void
    {
        $viewer = $this->userWithRole('viewer');

        $this->actingAs($viewer)
            ->get('/material-planning')
            ->assertOk()
            ->assertSee('Dashboard Monitoring')
            ->assertDontSee('Upload Daftar Kebutuhan Material')
            ->assertDontSee('Bill Of Material')
            ->assertDontSee('Kode Proyek')
            ->assertDontSee('User Management');
    }

    public function test_viewer_can_access_dashboard_data_and_export(): void
    {
        $viewer = $this->userWithRole('viewer');

        $this->actingAs($viewer)
            ->getJson('/material-planning/data?per_page=5')
            ->assertOk();

        $this->actingAs($viewer)
            ->get('/material-planning/export')
            ->assertOk();
    }

    public function test_viewer_cannot_access_other_menus(): void
    {
        $viewer = $this->userWithRole('viewer');

        $this->actingAs($viewer)->get('/users')->assertForbidden();
        $this->actingAs($viewer)->get('/roles')->assertForbidden();
        $this->actingAs($viewer)->get('/project-code')->assertForbidden();
        $this->actingAs($viewer)->get('/bill-of-material')->assertForbidden();
    }

    public function test_viewer_cannot_upload_excel_files(): void
    {
        $viewer = $this->userWithRole('viewer');
        $file = UploadedFile::fake()->createWithContent('bom.csv', "kode,jumlah\nA,1\n");

        $this->actingAs($viewer)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/material-planning/import-excel', ['file' => $file])
            ->assertForbidden();

        $this->actingAs($viewer)
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/bom/import', ['file' => $file])
            ->assertForbidden();
    }

    public function test_admin_can_access_all_menus_and_see_upload_section(): void
    {
        $admin = $this->userWithRole('admin');

        $this->actingAs($admin)->get('/users')->assertOk();
        $this->actingAs($admin)->get('/roles')->assertOk();
        $this->actingAs($admin)->get('/project-code')->assertOk();
        $this->actingAs($admin)->get('/bill-of-material')->assertOk();

        $this->actingAs($admin)
            ->get('/material-planning')
            ->assertOk()
            ->assertSee('Upload Daftar Kebutuhan Material')
            ->assertSee('Bill Of Material')
            ->assertSee('Kode Proyek')
            ->assertSee('User Management');
    }
}
