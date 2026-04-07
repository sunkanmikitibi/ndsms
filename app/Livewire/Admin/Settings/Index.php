<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('System Settings')]
class Index extends Component
{
    public string $activeTab = 'general';
    
    // General Settings
    public string $appName = '';
    public string $appEmail = '';
    public string $appPhone = '';
    public string $appAddress = '';
    
    // Email Settings
    public string $mailDriver = '';
    public string $mailFrom = '';
    public string $mailHost = '';
    public string $mailPort = '';
    public string $mailUsername = '';
    public string $mailPassword = '';
    
    // Payment Settings
    public string $paystackPublicKey = '';
    public string $paystackSecretKey = '';
    public string $paystackEnv = 'test';
    
    // Feature Flags
    public bool $enableAddressIndexing = true;
    public bool $enableStreetRevalidation = true;
    public bool $enableQrScanner = false;
    public bool $enableAiLookup = false;

    public function mount()
    {
        if (!auth()->user()->hasRole('super-admin')) {
            abort(403, 'Unauthorized access to settings.');
        }

        // Load settings from config/env
        $this->appName = config('app.name', 'NDSMS');
        $this->appEmail = config('app.from.address', '');
        $this->appPhone = env('APP_PHONE', '');
        $this->appAddress = env('APP_ADDRESS', '');
        
        $this->mailDriver = config('mail.driver', 'log');
        $this->mailFrom = config('mail.from.address', '');
        $this->mailHost = config('mail.host', '');
        $this->mailPort = config('mail.port', '587');
        $this->mailUsername = config('mail.username', '');
        
        $this->paystackEnv = config('services.paystack.env', 'test');
        
        // Feature flags
        $this->enableAddressIndexing = (bool) env('FEATURE_ADDRESS_INDEXING', true);
        $this->enableStreetRevalidation = (bool) env('FEATURE_STREET_REVALIDATION', true);
        $this->enableQrScanner = (bool) env('FEATURE_QR_SCANNER', false);
        $this->enableAiLookup = (bool) env('FEATURE_AI_LOOKUP', false);
    }

    public function saveGeneralSettings()
    {
        $this->validate([
            'appName' => 'required|string|max:255',
            'appEmail' => 'required|email',
            'appPhone' => 'nullable|string|max:20',
            'appAddress' => 'nullable|string|max:500',
        ]);

        $this->updateEnvFile([
            'APP_NAME' => $this->appName,
            'APP_EMAIL' => $this->appEmail,
            'APP_PHONE' => $this->appPhone,
            'APP_ADDRESS' => $this->appAddress,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'General settings updated successfully.',
        ]);
    }

    public function saveEmailSettings()
    {
        $this->validate([
            'mailDriver' => 'required|string|in:log,smtp,sendmail',
            'mailFrom' => 'required|email',
            'mailHost' => 'required|string',
            'mailPort' => 'required|numeric',
        ]);

        $this->updateEnvFile([
            'MAIL_DRIVER' => $this->mailDriver,
            'MAIL_FROM_ADDRESS' => $this->mailFrom,
            'MAIL_HOST' => $this->mailHost,
            'MAIL_PORT' => $this->mailPort,
            'MAIL_USERNAME' => $this->mailUsername,
            'MAIL_PASSWORD' => $this->mailPassword,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Email settings updated successfully.',
        ]);
    }

    public function savePaymentSettings()
    {
        $this->validate([
            'paystackPublicKey' => 'required|string',
            'paystackSecretKey' => 'required|string',
            'paystackEnv' => 'required|string|in:test,live',
        ]);

        if (str_contains($this->paystackPublicKey, '*')) {
            $this->paystackPublicKey = env('PAYSTACK_PUBLIC_KEY', '');
        }
        if (str_contains($this->paystackSecretKey, '*')) {
            $this->paystackSecretKey = env('PAYSTACK_SECRET_KEY', '');
        }

        $this->updateEnvFile([
            'PAYSTACK_PUBLIC_KEY' => $this->paystackPublicKey,
            'PAYSTACK_SECRET_KEY' => $this->paystackSecretKey,
            'PAYSTACK_ENV' => $this->paystackEnv,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Payment settings updated successfully.',
        ]);
    }

    public function saveFeatureFlags()
    {
        $this->updateEnvFile([
            'FEATURE_ADDRESS_INDEXING' => $this->enableAddressIndexing ? 'true' : 'false',
            'FEATURE_STREET_REVALIDATION' => $this->enableStreetRevalidation ? 'true' : 'false',
            'FEATURE_QR_SCANNER' => $this->enableQrScanner ? 'true' : 'false',
            'FEATURE_AI_LOOKUP' => $this->enableAiLookup ? 'true' : 'false',
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Feature flags updated successfully.',
        ]);
    }

    private function updateEnvFile(array $updates)
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            return;
        }

        $content = file_get_contents($envPath);

        foreach ($updates as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            if (preg_match($pattern, $content)) {
                $content = preg_replace($pattern, "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $content);
    }

    public function render()
    {
        return view('livewire.admin.settings.index');
    }
}
