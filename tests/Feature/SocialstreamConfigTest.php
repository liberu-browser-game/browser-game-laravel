<?php

namespace Tests\Feature;

use JoelButcher\Socialstream\Socialstream;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocialstreamConfigTest extends TestCase
{
    #[Test]
    public function test_socialstream_config_has_social_media_providers(): void
    {
        $providers = Socialstream::providers();

        $this->assertNotEmpty($providers, 'socialstream.providers config must not be empty');

        $providerIds = collect($providers)->pluck('id')->toArray();

        foreach ($this->expectedProviders() as $expected) {
            $this->assertContains(
                $expected,
                $providerIds,
                "Provider '{$expected}' is missing from socialstream config"
            );
        }
    }

    #[Test]
    #[DataProvider('providerDataProvider')]
    public function test_each_provider_is_present_in_config(string $provider): void
    {
        $providerIds = collect(Socialstream::providers())->pluck('id')->toArray();

        $this->assertContains(
            $provider,
            $providerIds,
            "Provider '{$provider}' should be enabled in socialstream config"
        );
    }

    #[Test]
    public function test_twitter_oauth1_is_not_in_config(): void
    {
        $providerIds = collect(Socialstream::providers())->pluck('id')->toArray();

        $this->assertNotContains(
            'twitter',
            $providerIds,
            'Twitter OAuth 1.0 should not be configured (requires live API keys for redirect)'
        );
    }

    #[Test]
    public function test_socialstream_config_has_required_features(): void
    {
        $features = config('socialstream.features');

        $this->assertNotEmpty($features, 'socialstream.features config must not be empty');
    }

    #[Test]
    public function test_socialstream_home_is_configured(): void
    {
        $home = config('socialstream.home');

        $this->assertNotEmpty($home);
        $this->assertStringStartsWith('/', $home);
    }

    #[Test]
    public function test_each_provider_has_required_keys(): void
    {
        foreach (Socialstream::providers() as $provider) {
            $this->assertArrayHasKey('id', $provider, 'Provider must have an id key');
            $this->assertArrayHasKey('name', $provider, 'Provider must have a name key');
            $this->assertNotEmpty($provider['id'], 'Provider id must not be empty');
        }
    }

    public static function providerDataProvider(): array
    {
        return [
            'bitbucket'       => ['bitbucket'],
            'facebook'        => ['facebook'],
            'github'          => ['github'],
            'gitlab'          => ['gitlab'],
            'google'          => ['google'],
            'linkedin'        => ['linkedin'],
            'linkedin-openid' => ['linkedin-openid'],
            'slack'           => ['slack'],
            'twitter-oauth-2' => ['twitter-oauth-2'],
            // twitterOAuth1 excluded: OAuth 1.0 requires live API keys even for redirect
        ];
    }

    private function expectedProviders(): array
    {
        return [
            'bitbucket',
            'facebook',
            'github',
            'gitlab',
            'google',
            'linkedin',
            'linkedin-openid',
            'slack',
            'twitter-oauth-2',
        ];
    }
}
