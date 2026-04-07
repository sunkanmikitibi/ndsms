<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    // Field officers are redirected to portal dashboard by HomeRedirectController
    $response->assertRedirect(route('portal.dashboard'));
});

test('authenticated admin users are redirected to portal dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    // Admin users (non-super-admin) are redirected to portal dashboard
    $response->assertRedirect(route('portal.dashboard'));
});

test('authenticated super admin users are redirected to admin dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    // Only super-admin users are redirected to admin dashboard
    $response->assertRedirect(route('admin.dashboard'));
});