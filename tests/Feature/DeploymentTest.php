<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeploymentTest extends TestCase
{
    use RefreshDatabase;

    private string $validKey = 'test-deploy-secret';

    protected function setUp(): void
    {
        parent::setUp();
        // Override the deploy_secret config for test isolation
        config(['app.deploy_secret' => $this->validKey]);
    }

    /** Missing key → 403 Unauthorized */
    public function test_deploy_trigger_returns_403_when_key_is_missing(): void
    {
        $response = $this->getJson('/system/deploy/trigger');

        $response->assertStatus(403)
            ->assertJson(['status' => 'error']);
    }

    /** Wrong key → 403 Unauthorized */
    public function test_deploy_trigger_returns_403_when_key_is_wrong(): void
    {
        $response = $this->getJson('/system/deploy/trigger?key=wrong-key');

        $response->assertStatus(403)
            ->assertJson(['status' => 'error']);
    }

    /** Correct key → 200, success, results array with expected keys */
    public function test_deploy_trigger_returns_200_with_correct_key(): void
    {
        $response = $this->getJson('/system/deploy/trigger?key=' . $this->validKey);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'message',
                'results' => [
                    'migrate',
                    'optimize',
                    'view',
                    'config',
                    'symlink_check',
                ],
            ]);
    }

    /** symlink_check key is always present in results */
    public function test_deploy_trigger_response_contains_symlink_check(): void
    {
        $response = $this->getJson('/system/deploy/trigger?key=' . $this->validKey);

        $response->assertStatus(200)
            ->assertJsonPath('results.symlink_check.status', fn($v) => in_array($v, ['exists', 'missing']));
    }
}
