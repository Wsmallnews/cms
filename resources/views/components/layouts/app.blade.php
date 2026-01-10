<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        @stack('seo')

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        @filamentStyles
        @vite('resources/css/app.css')

        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>

    <body class="antialiased bg-slate-50 flex flex-col h-dvh">
        <div class="w-full shrink-0 flex h-32 overflow-hidden bg-[url({{ asset('image/banner.jpg') }})] bg-top-right bg-cover">
            <div class="container mx-auto flex items-center justify-between">
                <img src="{{ asset('image/logo.png') }}" alt="logo" class="h-full object-contain">

                <div class="flex gap-4">
                    @php
                        use Wsmallnews\Cms\Support\Utils;
                        use Wsmallnews\Cms\CmsPlugin;
                    @endphp
                    @auth
                        <livewire:sn-user-components-user-user-menu :module="app(CmsPlugin::class)->getId()" dark-mode="{{ Utils::getConfig('themes.dark-mode', false) }}" />
                    @else
                        <x-filament::button tag="a" href="{{ Utils::route('login') }}">
                            登录
                        </x-filament::button>
                        <x-filament::button color="gray" tag="a" href="{{ Utils::route('register') }}">
                            注册
                        </x-filament::button>
                    @endauth
                </div>
            </div>
        </div>
        {{ $slot }}

        @livewire('notifications')
        {{-- @livewire('database-notifications') --}}

        @filamentScripts
        @vite('resources/js/app.js')

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('refresh', () => {
                    window.location.reload();
                });
            });
        </script>
    </body>
</html>