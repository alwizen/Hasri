<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Input Absen RFID</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-indigo-600 to-purple-700 min-h-screen flex items-center justify-center overflow-hidden">

    <!-- Notifikasi Toast -->
    @if(session('success'))
    <div id="toast" class="fixed top-8 left-1/2 transform -translate-x-1/2 z-50 px-6 py-4 rounded-lg bg-green-500 text-white font-semibold shadow-lg animate-bounce">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div id="toast" class="fixed top-8 left-1/2 transform -translate-x-1/2 z-50 px-6 py-4 rounded-lg bg-red-500 text-white font-semibold shadow-lg animate-bounce">
        {{ session('error') }}
    </div>
    @endif

    @if(session('warning'))
    <div id="toast" class="fixed top-8 left-1/2 transform -translate-x-1/2 z-50 px-6 py-4 rounded-lg bg-yellow-500 text-white font-semibold shadow-lg animate-bounce">
        {{ session('warning') }}
    </div>
    @endif

    <!-- Form Tersembunyi -->
    <form id="rfidForm" action="{{ route('rfid.attendance.submit') }}" method="POST" class="hidden">
        @csrf
        <input
            id="rfid_uid"
            name="rfid_uid"
            type="text"
            autofocus
            autocomplete="off">
    </form>

    <!-- Konten Utama Fullscreen -->
    <div class="text-center px-8">
        <!-- Icon RFID -->
        <div class="mb-8 animate-pulse">
            <svg class="w-32 h-32 mx-auto text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>

        <!-- Teks Utama -->
        <h1 class="text-6xl md:text-8xl font-bold text-white mb-4 drop-shadow-lg">
            Silahkan Tap
        </h1>
        <h2 class="text-5xl md:text-7xl font-bold text-white mb-8 drop-shadow-lg">
            Kartu Anda
        </h2>

        <!-- Indikator Status -->
        <div id="statusIndicator" class="mt-12 flex items-center justify-center space-x-3">
            <div class="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
            <p class="text-white text-xl font-medium">Siap Menerima</p>
        </div>

        <!-- Debug Info (Optional - bisa dihapus di production) -->
        @if(config('app.debug'))
        <div class="mt-8 text-white text-sm opacity-50">
            Scanner mode aktif
        </div>
        @endif
    </div>

    <script>
        const input = document.getElementById('rfid_uid');
        const form = document.getElementById('rfidForm');
        const statusIndicator = document.getElementById('statusIndicator');
        const toast = document.getElementById('toast');

        // Pastikan input selalu fokus
        input.focus();

        // Re-focus jika kehilangan fokus
        document.addEventListener('click', () => input.focus());
        window.addEventListener('blur', () => {
            setTimeout(() => input.focus(), 100);
        });

        // Auto submit saat scanner selesai (biasanya dengan Enter)
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                // Animasi saat memproses
                statusIndicator.innerHTML = `
                    <div class="w-4 h-4 bg-yellow-400 rounded-full animate-spin border-2 border-white border-t-transparent"></div>
                    <p class="text-white text-xl font-medium">Memproses...</p>
                `;

                // Submit form
                form.submit();
            }
        });

        // Visual feedback saat mengetik
        input.addEventListener('input', function() {
            if (this.value.length > 0) {
                statusIndicator.innerHTML = `
                    <div class="w-4 h-4 bg-blue-400 rounded-full animate-pulse"></div>
                    <p class="text-white text-xl font-medium">Membaca Kartu...</p>
                `;
            }
        });

        // Auto hide toast setelah 3 detik
        if (toast) {
            setTimeout(() => {
                toast.style.transition = 'opacity 0.5s';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        // Auto clear dan re-focus setelah toast hilang
        if (toast) {
            setTimeout(() => {
                input.value = '';
                input.focus();
                statusIndicator.innerHTML = `
                    <div class="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
                    <p class="text-white text-xl font-medium">Siap Menerima</p>
                `;
            }, 3500);
        }
    </script>
</body>

</html>