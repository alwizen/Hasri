<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Input Absen RFID</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="min-h-screen flex items-center justify-center overflow-hidden relative">
    <!-- Background Image dengan Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="/img/bg.jpg" alt="Background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    </div>

    <!-- Content wrapper untuk memastikan konten di atas background -->
    <div class="relative z-10 w-full h-full flex items-center justify-center">

        <!-- Logo (Pojok Kiri Atas) -->
        <div class="fixed top-6 left-6 z-50">
            <img src="/img/logo.png" alt="Logo SMA NU Hasyim Asy'ari" class="h-20 w-auto drop-shadow-lg">
        </div>

        <!-- Toggle Mode (Pojok Kanan Atas) -->
        <div class="fixed top-6 right-6 z-50">
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-lg p-4 shadow-lg">
                <p class="text-white text-sm font-medium mb-2 text-center">Mode</p>
                <div class="flex items-center space-x-3">
                    <button id="manualBtn"
                        class="px-4 py-2 rounded-lg bg-white text-indigo-600 font-semibold shadow transition">
                        Manual
                    </button>
                    <button id="autoBtn"
                        class="px-4 py-2 rounded-lg bg-indigo-800 bg-opacity-50 text-white font-semibold transition">
                        Otomatis
                    </button>
                </div>
            </div>
        </div>

        <!-- Debug Info (Pojok Kiri Bawah) - Hapus setelah testing -->
        <div id="debugInfo"
            class="fixed bottom-6 left-6 z-50 bg-black bg-opacity-70 text-white p-3 rounded-lg text-xs font-mono max-w-xs hidden">
            <div>Mode: <span id="debugMode">-</span></div>
            <div>Focus: <span id="debugFocus">-</span></div>
            <div>Value: <span id="debugValue">-</span></div>
            <div>Last Key: <span id="debugKey">-</span></div>
        </div>

        <!-- Notifikasi Toast -->
        @if (session('success'))
            <div id="toast"
                class="fixed top-10 left-1/2 transform -translate-x-1/2 z-50 
               px-10 py-6 rounded-xl 
               bg-green-600 text-white 
               text-3xl font-bold 
               shadow-2xl
               max-w-3xl text-center
               transition-opacity duration-500">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div id="toast"
                class="fixed top-10 left-1/2 transform -translate-x-1/2 z-50 
               px-10 py-6 rounded-xl 
               bg-red-600 text-white 
               text-3xl font-bold 
               shadow-2xl
               max-w-3xl text-center
               transition-opacity duration-500">
                {{ session('error') }}
            </div>
        @endif

        @if (session('warning'))
            <div id="toast"
                class="fixed top-10 left-1/2 transform -translate-x-1/2 z-50 
               px-10 py-6 rounded-xl 
               bg-yellow-400 text-black 
               text-3xl font-bold 
               shadow-2xl
               max-w-3xl text-center
               transition-opacity duration-500">
                {{ session('warning') }}
            </div>
        @endif

        <!-- Konten Mode Manual -->
        <div id="manualMode" class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <h1 class="text-2xl font-bold mb-6 text-center text-gray-800">Input Absen (Manual)</h1>

                <form action="{{ route('rfid.attendance.submit') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="rfid_uid_manual" class="block text-sm font-medium text-gray-700 mb-2">
                            Tempelkan kartu RFID / Masukkan UID
                        </label>
                        <input id="rfid_uid_manual" name="rfid_uid" type="text" autofocus autocomplete="off"
                            class="block w-full rounded-lg border-2 border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 p-3 text-lg"
                            placeholder="Contoh: 04A3B2C1">
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold shadow-lg transition">
                            Submit
                        </button>
                        <button type="button" id="clearBtnManual"
                            class="px-4 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition">
                            Bersihkan
                        </button>
                    </div>
                </form>

                <p class="mt-4 text-xs text-gray-500 text-center">
                    Scanner RFID akan mengisi UID otomatis. Tekan Enter atau klik Submit untuk mengirim.
                </p>
            </div>
        </div>

        <!-- Konten Mode Otomatis (Fullscreen) -->
        <div id="autoMode" class="text-center px-8 hidden">
            <!-- Form Tersembunyi -->
            <form id="rfidForm" action="{{ route('rfid.attendance.submit') }}" method="POST">
                @csrf
                <input id="rfid_uid_auto" name="rfid_uid" type="text" autocomplete="off"
                    style="position: absolute; left: -9999px;">
            </form>

            <!-- Icon RFID -->
            <div class="mb-8 animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-32 h-32 mx-auto text-white">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0 1 18 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3 1.5 1.5 3-3.75" />
                </svg>
            </div>

            <!-- Teks Utama -->
            <h1 class="text-6xl md:text-8xl font-bold text-white mb-4 drop-shadow-lg">
                Silahkan Tempelkan
            </h1>
            <h2 class="text-5xl md:text-7xl font-bold text-white mb-8 drop-shadow-lg">
                Kartu Anda
            </h2>

            <!-- Indikator Status -->
            <div id="statusIndicator" class="mt-12 flex items-center justify-center space-x-3">
                <div class="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
                <p class="text-white text-xl font-medium">Siap Menerima</p>
            </div>
        </div>

        <script>
            const manualBtn = document.getElementById('manualBtn');
            const autoBtn = document.getElementById('autoBtn');
            const manualMode = document.getElementById('manualMode');
            const autoMode = document.getElementById('autoMode');
            const inputManual = document.getElementById('rfid_uid_manual');
            const inputAuto = document.getElementById('rfid_uid_auto');
            const form = document.getElementById('rfidForm');
            const statusIndicator = document.getElementById('statusIndicator');
            const toast = document.getElementById('toast');
            const clearBtnManual = document.getElementById('clearBtnManual');

            // Debug elements
            const debugInfo = document.getElementById('debugInfo');
            const debugMode = document.getElementById('debugMode');
            const debugFocus = document.getElementById('debugFocus');
            const debugValue = document.getElementById('debugValue');
            const debugKey = document.getElementById('debugKey');

            let currentMode = 'manual';
            let isProcessing = false;

            // Show debug panel (hapus baris ini setelah testing)
            debugInfo.classList.remove('hidden');

            // Update debug info
            function updateDebug() {
                debugMode.textContent = currentMode;
                debugFocus.textContent = document.activeElement.id || 'none';
                debugValue.textContent = inputAuto.value || '(kosong)';
            }

            setInterval(updateDebug, 500);

            // Load saved mode dari localStorage
            const savedMode = localStorage.getItem('rfidMode');
            if (savedMode) {
                currentMode = savedMode;
                if (currentMode === 'auto') {
                    switchToAuto();
                }
            }

            // Switch ke Manual Mode
            manualBtn.addEventListener('click', function() {
                currentMode = 'manual';
                localStorage.setItem('rfidMode', 'manual');

                manualMode.classList.remove('hidden');
                autoMode.classList.add('hidden');

                manualBtn.classList.add('bg-white', 'text-indigo-600');
                manualBtn.classList.remove('bg-indigo-800', 'bg-opacity-50', 'text-white');

                autoBtn.classList.remove('bg-white', 'text-indigo-600');
                autoBtn.classList.add('bg-indigo-800', 'bg-opacity-50', 'text-white');

                setTimeout(() => inputManual.focus(), 100);
                console.log('Switched to MANUAL mode');
            });

            // Switch ke Auto Mode
            autoBtn.addEventListener('click', switchToAuto);

            function switchToAuto() {
                currentMode = 'auto';
                localStorage.setItem('rfidMode', 'auto');

                manualMode.classList.add('hidden');
                autoMode.classList.remove('hidden');

                autoBtn.classList.add('bg-white', 'text-indigo-600');
                autoBtn.classList.remove('bg-indigo-800', 'bg-opacity-50', 'text-white');

                manualBtn.classList.remove('bg-white', 'text-indigo-600');
                manualBtn.classList.add('bg-indigo-800', 'bg-opacity-50', 'text-white');

                isProcessing = false;
                inputAuto.value = '';
                setTimeout(() => inputAuto.focus(), 100);
                console.log('Switched to AUTO mode');
            }

            // === Manual Mode Logic ===
            clearBtnManual.addEventListener('click', function() {
                inputManual.value = '';
                inputManual.focus();
            });

            inputManual.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.form.submit();
                }
            });

            // === Auto Mode Logic ===

            // Pastikan input selalu fokus di mode auto
            document.addEventListener('click', () => {
                if (currentMode === 'auto' && !isProcessing) {
                    setTimeout(() => inputAuto.focus(), 50);
                }
            });

            window.addEventListener('blur', () => {
                if (currentMode === 'auto' && !isProcessing) {
                    setTimeout(() => inputAuto.focus(), 100);
                }
            });

            // Re-focus berkala untuk mode auto
            setInterval(() => {
                if (currentMode === 'auto' && !isProcessing && document.activeElement !== inputAuto) {
                    inputAuto.focus();
                }
            }, 500);

            // Visual feedback saat mengetik di auto mode
            inputAuto.addEventListener('input', function() {
                console.log('INPUT EVENT - Value:', this.value, 'Length:', this.value.length);

                if (this.value.length > 0 && statusIndicator) {
                    statusIndicator.innerHTML = `
                        <div class="w-4 h-4 bg-blue-400 rounded-full animate-pulse"></div>
                        <p class="text-white text-xl font-medium">Membaca Kartu...</p>
                    `;
                }
            });

            // Deteksi semua key press untuk debugging
            inputAuto.addEventListener('keydown', function(e) {
                console.log('KEYDOWN -', 'Key:', e.key, 'Code:', e.code, 'Value:', this.value);
                debugKey.textContent = e.key + ' (' + e.code + ')';
            });

            // Auto submit saat scanner selesai
            inputAuto.addEventListener('keypress', function(e) {
                console.log('KEYPRESS -', 'Key:', e.key, 'Value:', this.value, 'Processing:', isProcessing);

                if (e.key === 'Enter' && !isProcessing) {
                    e.preventDefault();

                    const rfidValue = this.value.trim();
                    console.log('ENTER PRESSED - RFID Value:', rfidValue);

                    // Validasi input tidak kosong
                    if (!rfidValue) {
                        console.error('Input kosong, tidak submit');

                        if (statusIndicator) {
                            statusIndicator.innerHTML = `
                                <div class="w-4 h-4 bg-red-400 rounded-full"></div>
                                <p class="text-white text-xl font-medium">Error - Coba Lagi</p>
                            `;
                        }

                        setTimeout(() => {
                            isProcessing = false;
                            inputAuto.value = '';
                            inputAuto.focus();
                            if (statusIndicator) {
                                statusIndicator.innerHTML = `
                                    <div class="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
                                    <p class="text-white text-xl font-medium">Siap Menerima</p>
                                `;
                            }
                        }, 1500);
                        return;
                    }

                    isProcessing = true;

                    // Animasi saat memproses
                    if (statusIndicator) {
                        statusIndicator.innerHTML = `
                            <div class="w-4 h-4 bg-yellow-400 rounded-full animate-spin border-2 border-white border-t-transparent"></div>
                            <p class="text-white text-xl font-medium">Memproses...</p>
                        `;
                    }

                    console.log('Submitting form with RFID:', rfidValue);

                    // Submit form ke Laravel
                    form.submit();
                }
            });

            // Auto hide toast setelah 3 detik (untuk session flash messages)
            if (toast) {
                setTimeout(() => {
                    toast.style.transition = 'opacity 0.5s';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 500);
                }, 3000);

                // Auto clear dan re-focus setelah toast
                setTimeout(() => {
                    if (currentMode === 'auto') {
                        inputAuto.value = '';
                        isProcessing = false;
                        inputAuto.focus();
                        if (statusIndicator) {
                            statusIndicator.innerHTML = `
                                <div class="w-4 h-4 bg-green-400 rounded-full animate-pulse"></div>
                                <p class="text-white text-xl font-medium">Siap Menerima</p>
                            `;
                        }
                    } else {
                        inputManual.value = '';
                        inputManual.focus();
                    }
                }, 3500);
            }

            // Initial focus
            if (currentMode === 'auto') {
                setTimeout(() => inputAuto.focus(), 100);
            } else {
                setTimeout(() => inputManual.focus(), 100);
            }
        </script>
    </div>
</body>

</html>
