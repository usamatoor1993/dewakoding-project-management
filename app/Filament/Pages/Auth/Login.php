<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;

class Login extends BaseLogin
{
    public function mount(): void
    {
        parent::mount();

        // Google login disabled
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
        //     fn (): string => view('filament.components.google-login-button')->render(),
        // );

        FilamentView::registerRenderHook(
            PanelsRenderHook::SIMPLE_LAYOUT_START,
            fn (): string => view('filament.components.bytes-loader')->render(),
        );
    }
}