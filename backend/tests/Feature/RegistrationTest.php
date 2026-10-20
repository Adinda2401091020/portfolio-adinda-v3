<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_registration_form_from_home_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(route('register'))
            ->assertDontSee('Log in')
            ->assertDontSee('href="/dashboard"');

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create an account')
            ->assertSee('name="name"', false)
            ->assertSee('name="email"', false)
            ->assertSee('name="password"', false)
            ->assertSee('name="password_confirmation"', false);
    }

    public function test_authenticated_navigation_does_not_link_to_a_missing_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertDontSee('href="/dashboard"')
            ->assertDontSee('Dashboard');
    }

    public function test_guest_can_register_with_valid_details(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '08123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('register'))
            ->assertSessionHas('status', 'Registration successful.');

        $this->assertDatabaseHas('users', [
            'name' => 'Test Customer',
            'email' => 'customer@example.com',
            'phone' => '08123456789',
            'role' => 'customer',
        ]);

        $user = User::query()->where('email', 'customer@example.com')->sole();

        $this->assertTrue(Hash::check('password123', $user->password));
    }

    public function test_invalid_registration_input_returns_validation_messages_without_creating_accounts(): void
    {
        User::create([
            'name' => 'Existing Customer',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ]);

        $validDetails = [
            'name' => 'New Customer',
            'email' => 'new@example.com',
            'phone' => null,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $invalidCases = [
            [
                'input' => array_diff_key($validDetails, ['name' => true]),
                'field' => 'name',
                'message' => 'The name field is required.',
            ],
            [
                'input' => [...$validDetails, 'email' => 'not-an-email'],
                'field' => 'email',
                'message' => 'The email field must be a valid email address.',
            ],
            [
                'input' => [...$validDetails, 'email' => 'existing@example.com'],
                'field' => 'email',
                'message' => 'The email has already been taken.',
            ],
            [
                'input' => [...$validDetails, 'password' => 'short', 'password_confirmation' => 'short'],
                'field' => 'password',
                'message' => 'The password field must be at least 8 characters.',
            ],
            [
                'input' => [...$validDetails, 'password_confirmation' => 'different-password'],
                'field' => 'password',
                'message' => 'The password field confirmation does not match.',
            ],
        ];

        foreach ($invalidCases as $case) {
            $this->from(route('register'))
                ->followingRedirects()
                ->post(route('register.store'), $case['input'])
                ->assertOk()
                ->assertSee($case['message']);

            $this->assertDatabaseHas('users', ['email' => 'existing@example.com']);
            $this->assertDatabaseMissing('users', ['email' => 'new@example.com']);
            $this->assertDatabaseCount('users', 1);
        }
    }
}
