<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#fc466b">

    <title>{{ $title ?? 'Bytes Project Management' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('img/bytes/bytes-logo-blue.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bytes: {
                            deep: '#003784',
                            sky: '#4aa5f0',
                            navy: '#171d34',
                            navydark: '#111426',
                            accent: '#fc466b',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Additional Styles -->
    @stack('styles')

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .bytes-gradient {
            background: linear-gradient(135deg, #003784 0%, #4aa5f0 100%);
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen">
    <!-- Simple Navigation Header -->
    <nav class="bytes-gradient">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center">
                        <img src="{{ asset('img/bytes/bytes-logo-write.png') }}" alt="Byte-s"
                            class="h-8 w-auto" />
                    </a>
                </div>
                <div class="flex items-center text-white">
                    <span class="text-sm opacity-80">
                        {{ now()->format('d M Y, H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen pb-20">
        {{ $slot }}
    </main>

    <!-- Simple Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-6">
            <div class="text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Bytes Project Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Additional Scripts -->
    @stack('scripts')

    <!-- Simplified JavaScript -->
    <script>
        // Simple notification function
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className =
                `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg text-white font-medium shadow-lg transition-all duration-300`;

            switch (type) {
                case 'success':
                    notification.classList.add('bg-green-500');
                    break;
                case 'error':
                    notification.classList.add('bg-red-500');
                    break;
                default:
                    notification.classList.add('bg-bytes-sky');
            }

            notification.textContent = message;
            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</body>

</html>