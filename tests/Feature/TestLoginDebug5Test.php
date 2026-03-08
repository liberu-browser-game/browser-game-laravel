<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Fortify;
use Tests\TestCase;

class TestLoginDebug5Test extends TestCase
{
    use RefreshDatabase;

    private function getRouteCount() {
        return collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn($r) => in_array('POST', $r->methods()) && str_contains($r->uri(), 'login'))
            ->count();
    }
    
    public function test_1_check_app_instance(): void
    {
        $appId = spl_object_id(app());
        $routerId = spl_object_id(app('router'));
        echo "\nTest 1: App ID=$appId, Router ID=$routerId\n";
        echo "Test 1: Fortify::registersRoutes = " . (Fortify::$registersRoutes ? 'true' : 'false') . "\n";
        echo "Test 1: Route count = " . $this->getRouteCount() . "\n";
        $this->get('/login')->assertStatus(200);
    }

    public function test_2_check_app_instance(): void
    {
        $appId = spl_object_id(app());
        $routerId = spl_object_id(app('router'));
        echo "\nTest 2: App ID=$appId, Router ID=$routerId\n";
        echo "Test 2: Fortify::registersRoutes = " . (Fortify::$registersRoutes ? 'true' : 'false') . "\n";
        echo "Test 2: Route count = " . $this->getRouteCount() . "\n";
        $this->assertTrue(true);
    }
}
