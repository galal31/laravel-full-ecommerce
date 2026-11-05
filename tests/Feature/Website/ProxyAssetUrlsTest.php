<?php

namespace Tests\Feature\Website;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter;
use Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect;
use Tests\TestCase;

class ProxyAssetUrlsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware([LocaleSessionRedirect::class, LaravelLocalizationRedirectFilter::class]);
    }

    public function test_local_proxy_generates_https_assets_using_forwarded_host(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->withHeaders(['X-Forwarded-Proto' => 'https', 'X-Forwarded-Host' => 'tunnel.example.test'])
            ->get(route('website.login'))
            ->assertOk()->assertSee('https://tunnel.example.test/website-assets/css/style.css', false);
    }

    public function test_direct_local_request_keeps_http_asset_links(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '127.0.0.1'])
            ->get(route('website.login'))
            ->assertOk()->assertSee('http://localhost/website-assets/css/style.css', false);
    }

    public function test_external_address_cannot_override_asset_host_or_scheme(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->withHeaders(['X-Forwarded-Proto' => 'https', 'X-Forwarded-Host' => 'untrusted.example.test'])
            ->get(route('website.login'))
            ->assertOk()->assertSee('http://localhost/website-assets/css/style.css', false)
            ->assertDontSee('https://untrusted.example.test/website-assets/css/style.css', false);
    }
}
