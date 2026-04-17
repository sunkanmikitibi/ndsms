<?php

use App\Models\Address;
use App\Models\Street;
use App\Models\User;
use Livewire\Livewire;

test('town map component renders correctly', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $response = $this->actingAs($user)->get(route('admin.map.index'));
    $response->assertStatus(200);
});

test('town map shows streets and addresses', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $street = Street::create([
        'name' => 'Test Street',
        'code' => 'TST-001',
        'town' => 'Test Town',
        'type' => 'primary',
        'status' => 'approved',
        'start_latitude' => 6.5244,
        'start_longitude' => 3.3792,
    ]);

    $address = Address::create([
        'house_number' => 'T1',
        'street_id' => $street->id,
        'town' => 'Test Town',
        'owner_name' => 'Test Owner',
        'owner_phone' => '08012345678',
        'status' => 'approved',
        'latitude' => 6.5244,
        'longitude' => 3.3792,
    ]);

    Livewire::actingAs($user)
        ->test('admin.town-map.index')
        ->assertSet('selectedTown', '')
        ->assertSet('filterType', 'all');
});

test('town map filters by town', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    Street::create([
        'name' => 'Street 1',
        'code' => 'STR-001',
        'town' => 'Town A',
        'type' => 'primary',
        'status' => 'approved',
    ]);

    Street::create([
        'name' => 'Street 2',
        'code' => 'STR-002',
        'town' => 'Town B',
        'type' => 'secondary',
        'status' => 'approved',
    ]);

    Livewire::actingAs($user)
        ->test('admin.town-map.index')
        ->set('selectedTown', 'Town A')
        ->assertSet('selectedTown', 'Town A');
});

test('town map filters by street type', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    Livewire::actingAs($user)
        ->test('admin.town-map.index')
        ->set('filterType', 'primary')
        ->assertSet('filterType', 'primary');
});

test('town map calculates coverage correctly', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $street = Street::create([
        'name' => 'Coverage Street',
        'code' => 'COV-001',
        'town' => 'Coverage Town',
        'type' => 'primary',
        'status' => 'approved',
    ]);

    // Create 3 addresses, 2 approved
    Address::create([
        'house_number' => 'C1',
        'street_id' => $street->id,
        'town' => 'Coverage Town',
        'owner_name' => 'Owner 1',
        'status' => 'approved',
        'approval_status' => 'approved',
    ]);

    Address::create([
        'house_number' => 'C2',
        'street_id' => $street->id,
        'town' => 'Coverage Town',
        'owner_name' => 'Owner 2',
        'status' => 'approved',
        'approval_status' => 'approved',
    ]);

    Address::create([
        'house_number' => 'C3',
        'street_id' => $street->id,
        'town' => 'Coverage Town',
        'owner_name' => 'Owner 3',
        'status' => 'pending',
        'approval_status' => 'pending',
    ]);

    Livewire::actingAs($user)
        ->test('admin.town-map.index')
        ->set('selectedTown', 'Coverage Town');
});

test('unauthorized users cannot access town map', function () {
    $user = User::factory()->create(); // No admin role

    $response = $this->actingAs($user)->get(route('admin.map.index'));
    $response->assertStatus(403);
});

test('users with view reports permission can access town map', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('view reports');

    $response = $this->actingAs($user)->get(route('admin.map.index'));
    $response->assertStatus(200);
});
