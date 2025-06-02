<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UserService();
    }

    /** @test */
    public function it_can_create_a_user_and_return_token()
    {
        $data = [
            'name'     => 'John Doe',
            'email'    => 'john@example.com',
            'password' => 'password123',
        ];

        $result = $this->service->create($data);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertArrayHasKey('token_type', $result);

        $this->assertEquals('Bearer', $result['token_type']);
        $this->assertEquals('john@example.com', $result['user']->email);

        // Confirma se o usuário foi criado no banco
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);

        // Verifica se a senha está hasheada
        $this->assertTrue(Hash::check('password123', $result['user']->password));
    }

    /** @test */
    public function it_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'jane@example.com',
            'password' => Hash::make('securepass'),
        ]);

        $result = $this->service->login([
            'email'    => 'jane@example.com',
            'password' => 'securepass',
        ]);

        $this->assertArrayHasKey('user', $result);
        $this->assertArrayHasKey('access_token', $result);
        $this->assertArrayHasKey('token_type', $result);

        $this->assertEquals('jane@example.com', $result['user']->email);
        $this->assertEquals('Bearer', $result['token_type']);
    }

    /** @test */
    public function it_throws_exception_on_invalid_login()
    {
        $this->expectException(ValidationException::class);

        User::factory()->create([
            'email'    => 'jane@example.com',
            'password' => Hash::make('correctpass'),
        ]);

        $this->service->login([
            'email'    => 'jane@example.com',
            'password' => 'wrongpass',
        ]);
    }
}
