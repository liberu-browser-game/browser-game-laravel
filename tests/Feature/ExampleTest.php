<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test the root route ("/") returns a successful response.
     */
    public function test_the_root_route_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test the "/app" route is accessible (redirects unauthenticated users).
     */
    public function test_the_app_route_returns_a_successful_response(): void
    {
        $response = $this->get('/app');
        // Unauthenticated users are redirected to login
        $response->assertRedirect();
    }

    /**
     * Test the "/admin" route is accessible (redirects unauthenticated users).
     */
    public function test_the_admin_route_returns_a_successful_response(): void
    {
        $response = $this->get('/admin');
        // Unauthenticated users are redirected to login
        $response->assertRedirect();
    }
}
