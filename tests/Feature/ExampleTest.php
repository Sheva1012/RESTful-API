<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_returns_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_assets_page_returns_successful_response(): void
    {
        $response = $this->get('/assets');
        $response->assertStatus(200);
    }

    public function test_loans_page_returns_successful_response(): void
    {
        $response = $this->get('/loans');
        $response->assertStatus(200);
    }

    public function test_borrowers_page_returns_successful_response(): void
    {
        $response = $this->get('/borrowers');
        $response->assertStatus(200);
    }

    public function test_docs_page_returns_successful_response(): void
    {
        $response = $this->get('/docs');
        $response->assertStatus(200);
    }
}
