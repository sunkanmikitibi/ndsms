<?php

use App\Models\User;
use Livewire\Livewire;

test('unauthenticated users cannot access QR scanner', function () {
    $response = $this->get(route('portal.qr-scanner'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can access QR scanner', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('portal.qr-scanner'));
    $response->assertStatus(200);
});

test('QR scanner component renders correctly', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('portal.qr-scanner')
        ->assertStatus(200);
});

test('component initializes with default properties', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('portal.qr-scanner')
        ->assertSet('isScanning', false)
        ->assertSet('scannedAddress', null)
        ->assertSet('manualCode', '')
        ->assertSet('scanError', '');
});

test('start scanning initializes camera', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('portal.qr-scanner')
        ->call('startScanning')
        ->assertSet('isScanning', true)
        ->assertDispatched('start-camera');
});

test('stop scanning halts scanning', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('portal.qr-scanner')
        ->set('isScanning', true)
        ->call('stopScanning')
        ->assertSet('isScanning', false)
        ->assertDispatched('stop-camera');
});

test('clear result resets all properties', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('portal.qr-scanner')
        ->set('scannedAddress', 1)
        ->set('manualCode', 'TEST-CODE')
        ->set('scanError', 'Test error')
        ->set('lastScannedCode', 'NJK-QR-0001')
        ->call('clearResult')
        ->assertSet('scannedAddress', null)
        ->assertSet('manualCode', '')
        ->assertSet('scanError', '')
        ->assertSet('lastScannedCode', null);
});

test('manual code search requires non-empty input', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('portal.qr-scanner')
        ->set('manualCode', '')
        ->call('searchManualCode')
        ->assertHasErrors('manualCode');
});

test('manual code search validates input is not just whitespace', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('portal.qr-scanner')
        ->set('manualCode', '   ')
        ->call('searchManualCode')
        ->assertHasErrors('manualCode');
});

