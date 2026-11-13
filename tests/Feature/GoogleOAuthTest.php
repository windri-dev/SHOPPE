<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_google_sso_button(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee(route('auth.google.redirect'));
        $response->assertSee('Google');
    }

    public function test_google_redirect_notifies_when_credentials_missing(): void
    {
        config(['services.google.client_id' => null]);

        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect(route('account.index'));
        $response->assertSessionHas('error');
    }

    public function test_google_redirect_redirects_to_google_when_configured(): void
    {
        config([
            'services.google.client_id' => 'google-client-test-id.apps.googleusercontent.com',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);

        $response = $this->get(route('auth.google.redirect'));

        $response->assertRedirect();
        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('accounts.google.com/o/oauth2/v2/auth', $targetUrl);
        $this->assertStringContainsString('google-client-test-id.apps.googleusercontent.com', $targetUrl);
        $this->assertNotEmpty(session('google_oauth_state'));
    }

    public function test_google_callback_rejects_invalid_state(): void
    {
        session(['google_oauth_state' => 'valid-secret-state-123']);

        $response = $this->get(route('auth.google.callback', [
            'code' => 'auth-code-123',
            'state' => 'wrong-state-456',
        ]));

        $response->assertRedirect(route('account.index'));
        $response->assertSessionHas('error');
        $this->assertFalse(Auth::check());
    }

    public function test_google_callback_handles_user_cancel(): void
    {
        $response = $this->get(route('auth.google.callback', [
            'error' => 'access_denied',
        ]));

        $response->assertRedirect(route('account.index'));
        $response->assertSessionHas('info');
        $this->assertFalse(Auth::check());
    }

    public function test_google_callback_registers_and_authenticates_new_user(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);

        $state = 'valid-csrf-token-abc';
        session(['google_oauth_state' => $state]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-google-access-token',
                'token_type' => 'Bearer',
                'expires_in' => 3600,
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'sub' => 'google-uid-109829381923',
                'name' => 'Maya Wulan',
                'email' => 'maya.wulan@gmail.com',
                'picture' => 'https://lh3.googleusercontent.com/a/dummy-photo',
                'email_verified' => true,
            ], 200),
        ]);

        $response = $this->get(route('auth.google.callback', [
            'code' => 'valid-google-code',
            'state' => $state,
        ]));

        $response->assertRedirect(route('account.index'));
        $this->assertTrue(Auth::check());

        /** @var User $authUser */
        $authUser = Auth::user();
        $this->assertEquals('maya.wulan@gmail.com', $authUser->email);
        $this->assertEquals('Maya Wulan', $authUser->name);
        $this->assertEquals('google-uid-109829381923', $authUser->google_id);
        $this->assertEquals(UserRole::CUSTOMER, $authUser->role);
        $this->assertNotNull($authUser->email_verified_at);
    }

    public function test_google_callback_links_to_existing_user(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'http://localhost/auth/google/callback',
        ]);

        $existingUser = User::factory()->create([
            'email' => 'existing.customer@example.com',
            'name' => 'Rina Wijaya',
            'google_id' => null,
        ]);

        $state = 'valid-csrf-token-xyz';
        session(['google_oauth_state' => $state]);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response([
                'access_token' => 'mock-google-access-token',
            ], 200),
            'https://www.googleapis.com/oauth2/v3/userinfo' => Http::response([
                'sub' => 'google-uid-883311',
                'name' => 'Rina Wijaya',
                'email' => 'existing.customer@example.com',
                'picture' => 'https://lh3.googleusercontent.com/a/rina-photo',
            ], 200),
        ]);

        $response = $this->get(route('auth.google.callback', [
            'code' => 'valid-code-xyz',
            'state' => $state,
        ]));

        $response->assertRedirect(route('account.index'));
        $this->assertTrue(Auth::check());
        $this->assertEquals($existingUser->id, Auth::id());

        $existingUser->refresh();
        $this->assertEquals('google-uid-883311', $existingUser->google_id);
    }
}
