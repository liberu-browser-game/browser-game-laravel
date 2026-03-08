<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestLoginSequenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_1_get_login(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_2_post_login(): void
    {
        $user = User::factory()->create();
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        echo "\nPOST /login status: " . $response->status() . "\n";
        if ($response->status() != 302) {
            echo "Response headers: " . json_encode($response->headers->all()) . "\n";
            echo "Response content: " . substr($response->content(), 0, 1000) . "\n";
        }
        
        $this->assertAuthenticated();
    }
}
