<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestLoginDebug2Test extends TestCase
{
    use RefreshDatabase;

    public function test_1_get_login(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        
        // Check routes after GET /login
        $routes = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn($r) => in_array('POST', $r->methods()) && str_contains($r->uri(), 'login'))
            ->map(fn($r) => ['method' => $r->methods(), 'uri' => $r->uri(), 'name' => $r->getName()])
            ->values();
        
        echo "\nRoutes with POST and 'login' after GET /login:\n";
        echo json_encode($routes->toArray(), JSON_PRETTY_PRINT) . "\n";
    }

    public function test_2_check_routes_before_login(): void
    {
        // Check routes before any request
        $routes = collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn($r) => in_array('POST', $r->methods()) && str_contains($r->uri(), 'login'))
            ->map(fn($r) => ['method' => $r->methods(), 'uri' => $r->uri(), 'name' => $r->getName()])
            ->values();
        
        echo "\nRoutes with POST and 'login' before POST /login:\n";
        echo json_encode($routes->toArray(), JSON_PRETTY_PRINT) . "\n";
        
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        echo "POST /login response: " . $response->status() . "\n";
        $this->assertAuthenticated();
    }
}
