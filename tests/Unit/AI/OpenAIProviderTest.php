<?php

namespace Tests\Unit\AI;

use Tests\TestCase;
use App\Services\AI\Providers\OpenAIProvider;
use Illuminate\Support\Facades\Http;

class OpenAIProviderTest extends TestCase
{
    protected $provider;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock the API key
        config(['ai.openai.api_key' => 'test-api-key']);
        config(['ai.openai.model' => 'gpt-4']);
        
        $this->provider = new OpenAIProvider();
    }

    /** @test */
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(OpenAIProvider::class, $this->provider);
    }

    /** @test */
    public function it_checks_availability_based_on_api_key()
    {
        config(['ai.openai.api_key' => 'test-key']);
        $provider = new OpenAIProvider();
        $this->assertTrue($provider->isAvailable());

        config(['ai.openai.api_key' => null]);
        $provider = new OpenAIProvider();
        $this->assertFalse($provider->isAvailable());
    }

    /** @test */
    public function it_returns_model_info()
    {
        $info = $this->provider->getModelInfo();

        $this->assertIsArray($info);
        $this->assertArrayHasKey('name', $info);
        $this->assertArrayHasKey('provider', $info);
        $this->assertEquals('OpenAI', $info['provider']);
    }

    /** @test */
    public function it_returns_null_when_api_key_missing()
    {
        config(['ai.openai.api_key' => null]);
        $provider = new OpenAIProvider();

        $context = [
            'task_title' => 'Test Task',
            'due_date' => now()->addDays(2)->toDateString(),
        ];

        $result = $provider->getSuggestion($context);
        $this->assertNull($result);
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        Http::fake([
            'api.openai.com/*' => Http::response(['error' => 'API Error'], 500)
        ]);

        $context = [
            'task_title' => 'Test Task',
            'due_date' => now()->addDays(2)->toDateString(),
        ];

        $result = $this->provider->getSuggestion($context);
        $this->assertNull($result);
    }

    /** @test */
    public function it_can_record_feedback()
    {
        // Should not throw exception
        $this->provider->recordFeedback('test-suggestion-id', true, [
            'comment' => 'Good suggestion'
        ]);

        $this->assertTrue(true);
    }
}
