<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Fortify;
use Laravel\Jetstream\Jetstream;
use Tests\TestCase;

class TestLoginDebug4Test extends TestCase
{
    use RefreshDatabase;

    public function test_1_check_static_state(): void
    {
        echo "\nTest 1 start: Fortify::registersRoutes = " . (Fortify::$registersRoutes ? 'true' : 'false') . "\n";
        $response = $this->get('/login');
        echo "Test 1 after GET: Fortify::registersRoutes = " . (Fortify::$registersRoutes ? 'true' : 'false') . "\n";
        $response->assertStatus(200);
    }

    public function test_2_check_static_state(): void
    {
        echo "\nTest 2 start: Fortify::registersRoutes = " . (Fortify::$registersRoutes ? 'true' : 'false') . "\n";
        
        $user = User::factory()->create();
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        echo "Test 2 POST status: " . $response->status() . "\n";
        $this->assertAuthenticated();
    }
}
