<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_redirect_to_google_works(): void
    {
        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this->get(route('auth.google'));

        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_login_page_renders_google_login_button(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee(route('auth.google'));
        $response->assertSee('Login with Google');
    }

    public function test_handle_callback_logs_in_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'role' => 'staff',
            'google_id' => null,
        ]);

        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('existing@example.com');
        $socialiteUser->shouldReceive('getId')->andReturn('google-12345');
        $socialiteUser->shouldReceive('getName')->andReturn('Existing User');

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticatedAs($user);
        $this->assertEquals('google-12345', $user->fresh()->google_id);
        $response->assertRedirect(route('dashboard.staff'));
    }

    public function test_handle_callback_creates_and_logs_in_new_user(): void
    {
        $socialiteUser = Mockery::mock(SocialiteUser::class);
        $socialiteUser->shouldReceive('getEmail')->andReturn('newuser@example.com');
        $socialiteUser->shouldReceive('getId')->andReturn('google-99999');
        $socialiteUser->shouldReceive('getName')->andReturn('New Google User');
        $socialiteUser->shouldReceive('getNickname')->andReturn(null);

        $provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $provider->shouldReceive('user')->once()->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->with('google')
            ->once()
            ->andReturn($provider);

        $response = $this->get(route('auth.google.callback'));

        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('google-99999', $newUser->google_id);
        $this->assertEquals('New Google User', $newUser->name);
        $this->assertEquals('staff', $newUser->role);

        $this->assertAuthenticatedAs($newUser);
        $response->assertRedirect(route('dashboard.staff'));
    }
}
