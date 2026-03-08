<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestLoginDebug3Test extends TestCase
{
    use RefreshDatabase;

    private function getLoginRoutes() {
        return collect(app('router')->getRoutes()->getRoutes())
            ->filter(fn($r) => in_array('POST', $r->methods()) && str_contains($r->uri(), 'login'))
            ->count();
    }

    public function test_1_check_routes_before_request(): void
    {
        echo "\nTest 1: Routes before any request: " . $this->getLoginRoutes() . "\n";
        $response = $this->get('/login');
        echo "Test 1: Routes after GET /login: " . $this->getLoginRoutes() . "\n";
        $response->assertStatus(200);
    }

    public function test_2_check_routes_before_post(): void
    {
        echo "\nTest 2: Routes before POST /login: " . $this->getLoginRoutes() . "\n";
        
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        echo "Test 2: POST /login status: " . $response->status() . "\n";
        echo "Test 2: Routes after POST /login: " . $this->getLoginRoutes() . "\n";
        $this->assertAuthenticated();
    }
}
