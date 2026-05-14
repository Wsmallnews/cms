@php
    use Wsmallnews\Cms\Support\Utils;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr"
    @class([
        'sn',
        'dark' => Utils::hasDarkModeForced(),
    ])
>
    <head>
        <meta charset="utf-8">
        <meta name="application-name" content="{{ config('app.name') }}" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        @stack('seo')

        <style>
            :root {
                /** 默认主题设置变量，可以通过读取该变量获取默认主题色 **/
                --default-theme-mode: {{ Utils::getDefaultDarkMode() }};
            }
            [x-cloak] {
                display: none !important;
            }
        </style>

        @filamentStyles

        @if (! Utils::hasDarkMode())
            <!-- 如果没开启暗黑模式，则主题一直是亮色 -->
            <script>
                localStorage.setItem('sn-support-frontend-theme', 'light')
            </script>
        @elseif (Utils::hasDarkModeForced())
            <!-- 如果强制暗黑模式，则主题一直是暗色 -->
            <script>
                localStorage.setItem('sn-support-frontend-theme', 'dark')
                document.documentElement.classList.add('dark')
            </script>
        @else
            <!-- 如果开启了主题，并且未强制暗黑，则加载 storage 中的主题配置 或者 默认主题配置 -->
            <script>
                const loadDarkMode = () => {
                    window.theme = localStorage.getItem('sn-support-frontend-theme') ?? @js(Utils::getDefaultDarkMode())

                    if (
                        window.theme === 'dark' ||
                        (window.theme === 'system' &&
                            window.matchMedia('(prefers-color-scheme: dark)')
                                .matches)
                    ) {
                        document.documentElement.classList.add('dark')
                    }
                }

                // 加载主题色，其实就是给 html 增加 dark
                loadDarkMode()

                // livewire spa 导航时，重新加载主题色
                document.addEventListener('livewire:navigated', loadDarkMode)
            </script>
        @endif

        @vite('resources/css/app.css')
    </head>

    <body class="sn-body antialiased bg-gray-50 dark:bg-gray-950 text-gray-700 dark:text-gray-200 flex flex-col min-h-screen">
        <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:rounded-md focus:bg-primary-600 focus:text-white focus:shadow-lg">
            {{ __('sn-cms::cms.frontend.skip_to_content') }}
        </a>

        <main id="main-content" class="flex flex-col grow">
            {{ $slot }}
        </main>

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