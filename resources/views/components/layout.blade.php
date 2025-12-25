<!-- resources/views/components/layout.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Monitoring Mitra' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        {{ $slot }}
    </div>

    <script>
        // Setup Axios defaults
        window.axios = {
            post: async (url, data) => {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Authorization': localStorage.getItem('token') ?
                            `Bearer ${localStorage.getItem('token')}` : ''
                    },
                    body: JSON.stringify(data)
                });
                return response.json();
            },
            get: async (url) => {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': localStorage.getItem('token') ?
                            `Bearer ${localStorage.getItem('token')}` : ''
                    }
                });
                return response.json();
            }
        };
    </script>

    @stack('scripts')
</body>

</html>
