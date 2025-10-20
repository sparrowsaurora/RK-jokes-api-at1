<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

const API_VER = 'v3';

uses(RefreshDatabase::class);

test('retrieve all users', function () {
    // Arrange
    $users = User::factory(5)->create();

    $expectedData = [
        'message' => 'Users retrieved',
        'success' => true,
        'data' => $users->toArray(),
    ];

    // Act
    $response = $this->getJson('/api/' . API_VER . '/users');

    // Assert
    $response
        ->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJson($expectedData);
});

test('retrieve one user', function () {
    // Arrange
    $user = User::factory()->create();

    $expectedData = [
        'message' => 'User retrieved',
        'success' => true,
        'data' => [$user->toArray()],
    ];

    // Act
    $response = $this->getJson('/api/' . API_VER . '/users/' . $user->id);

    // Assert
    $response
        ->assertStatus(200)
        ->assertJson($expectedData)
        ->assertJsonCount(1, 'data');
});

test('return error on missing user', function () {
    $expectedData = [
        'message' => 'User not found',
        'success' => false,
        'data' => [],
    ];

    // Act
    $response = $this->getJson('/api/' . API_VER . '/users/9999');

    // Assert
    $response
        ->assertStatus(404)
        ->assertJson($expectedData)
        ->assertJsonCount(0, 'data');
});

test('create a new user', function () {
    // Arrange
    $data = [
        'name' => 'Fake User',
        'email' => 'fake@example.com',
        'password' => 'password123',
    ];

    $expectedResponse = [
        'message' => 'User created successfully',
        'success' => true,
    ];

    // Act
    $response = $this->postJson('/api/' . API_VER . '/users', $data);

    // Assert
    $response
        ->assertStatus(201)
        ->assertJsonFragment($expectedResponse)
        ->assertJsonStructure([
            'success',
            'message',
            'data' => ['id', 'name', 'email']
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'fake@example.com',
    ]);
});

test('create user with invalid data fails', function () {
    // Missing name, invalid email, short password
    $data = [
        'name' => '',
        'email' => 'invalid-email',
        'password' => '123',
    ];

    $response = $this->postJson('/api/' . API_VER . '/users', $data);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name',
            'email',
            'password',
        ]);
});

test('update user information', function () {
    // Arrange
    $user = User::factory()->create();
    $data = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ];

    $expected = [
        'message' => 'User updated successfully',
        'success' => true,
        'data' => $data,
    ];

    // Act
    $response = $this->putJson('/api/' . API_VER . '/users/' . $user->id, $data);

    // Assert
    $response
        ->assertStatus(200)
        ->assertJsonFragment($expected);

    $this->assertDatabaseHas('users', [
        'email' => 'updated@example.com',
    ]);
});

test('delete a user', function () {
    // Arrange
    $user = User::factory()->create();

    $expected = [
        'message' => 'User deleted successfully',
        'success' => true,
    ];

    // Act
    $response = $this->deleteJson('/api/' . API_VER . '/users/' . $user->id);

    // Assert
    $response
        ->assertStatus(200)
        ->assertJsonFragment($expected);

    $this->assertDatabaseMissing('users', [
        'id' => $user->id,
    ]);
});

test('delete missing user returns error', function () {
    $expected = [
        'message' => 'User not found',
        'success' => false,
        'data' => [],
    ];

    $response = $this->deleteJson('/api/' . API_VER . '/users/9999');

    $response
        ->assertStatus(404)
        ->assertJson($expected);
});
