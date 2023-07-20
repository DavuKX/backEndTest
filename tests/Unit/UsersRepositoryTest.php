<?php

namespace Tests\Unit;

use App\Exceptions\UserDoesNotExistException;
use App\Repositories\UsersRepository;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UsersRepository $usersRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->refreshDatabase();

        $this->usersRepository = new UsersRepository(new User());
    }

    public function testCanCreateUser()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => bcrypt('password123'),
        ];

        $user = $this->usersRepository->create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', ['email' => 'john.doe@example.com']);
    }

    public function testCanUpdateUser()
    {
        $userData = [
            'name' => 'Jane Smith',
            'email' => 'jane.smith@example.com',
            'password' => bcrypt('password123'),
        ];

        $user = $this->usersRepository->create($userData);

        $updatedData = [
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
        ];

        $updatedUser = $this->usersRepository->update($user->id, $updatedData);

        $this->assertEquals('Jane Doe', $updatedUser->name);
        $this->assertEquals('jane.doe@example.com', $updatedUser->email);
    }

    public function testCanDeleteUser()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test.user@example.com',
            'password' => bcrypt('password123'),
        ];

        $user = $this->usersRepository->create($userData);
        $this->assertTrue($this->usersRepository->delete($user->id));
        $this->assertDatabaseMissing('users', ['email' => 'test.user@example.com']);
    }

    /**
     * @throws UserDoesNotExistException
     */
    public function testGetUserByIdOrFail()
    {
        $userData = [
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'password' => bcrypt('password123'),
        ];

        $user = $this->usersRepository->create($userData);

        $foundUser = $this->usersRepository->getByIdOrFail($user->id);

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertEquals('john.smith@example.com', $foundUser->email);
    }

    public function testUserNotFoundByIdErrorIsThrown()
    {
        $this->expectException(UserDoesNotExistException::class);
        $this->usersRepository->getByIdOrFail(9999);
    }

    public function testCanGetAllUsers()
    {
        $usersData = [
            [
                'name' => 'User One',
                'email' => 'user.one@example.com',
                'password' => bcrypt('password123'),
            ],
            [
                'name' => 'User Two',
                'email' => 'user.two@example.com',
                'password' => bcrypt('password123'),
            ],
        ];

        foreach ($usersData as $userData) {
            $this->usersRepository->create($userData);
        }

        $users = $this->usersRepository->all();

        $this->assertCount(2, $users);
    }
}
