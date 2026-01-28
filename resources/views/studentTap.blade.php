<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Input Absen Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Glassmorphism Effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        .glass-input {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .glass-input:focus {
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.6);
            outline: none;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
        }

        .glass-button-primary {
            background: rgba(79, 70, 229, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .glass-button-primary:hover {
            background: rgba(67, 56, 202, 0.8);
            box-shadow: 0 4px 20px rgba(79, 70, 229, 0.5);
        }

        .glass-button-secondary {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .glass-button-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        /* Toast glassmorphism */
        .glass-toast {
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Text shadow for better readability */
        .text-glass {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
    </style>
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

        <!-- Notifikasi Toast -->
        @if (session('success'))
            <div id="toast"
                class="glass-toast fixed top-10 left-1/2 transform -translate-x-1/2 z-50 
               px-10 py-6 rounded-xl 
               bg-green-600 bg-opacity-80 text-white 
               text-3xl font-bold 
               shadow-2xl
               max-w-3xl text-center text-glass
               transition-opacity duration-500">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div id="toast"
                class="glass-toast fixed top-10 left-1/2 transform -translate-x-1/2 z-50 
               px-10 py-6 rounded-xl 
               bg-red-600 bg-opacity-80 text-white 
               text-3xl font-bold 
               shadow-2xl
               max-w-3xl text-center text-glass
               transition-opacity duration-500">
                {{ session('error') }}
            </div>
        @endif

        @if (session('info'))
            <div id="toast"
                class="glass-toast fixed top-10 left-1/2 transform -translate-x-1/2 z-50 
               px-10 py-6 rounded-xl 
               bg-blue-600 bg-opacity-80 text-white 
               text-3xl font-bold 
               shadow-2xl
               max-w-3xl text-center text-glass
               transition-opacity duration-500">
                {{ session('info') }}
            </div>
        @endif

        <!-- Konten Utama -->
        <div class="w-full max-w-md">
            <div class="glass-card rounded-lg shadow-xl p-8">
                <h1 class="text-2xl font-bold mb-6 text-center text-white text-glass">Input Absen Siswa</h1>

                <form action="{{ route('studentTap.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="nis" class="block text-sm font-medium text-white mb-2 text-glass">
                            Scan / Masukkan NIS
                        </label>
                        <input id="nis" name="nis" type="text" autofocus autocomplete="off" required
                            class="glass-input block w-full rounded-lg shadow-sm p-3 text-lg text-white placeholder-gray-200"
                            placeholder="Masukkan NIS">
                    </div>

                    <div class="flex space-x-3">
                        <button type="submit"
                            class="glass-button-primary flex-1 px-4 py-3 text-white rounded-lg font-semibold shadow-lg transition">
                            Tap
                        </button>
                        <button type="button" id="clearBtn"
                            class="glass-button-secondary px-4 py-3 rounded-lg text-white font-semibold transition">
                            Bersihkan
                        </button>
                    </div>
                </form>

                <p class="mt-4 text-xs text-white text-center text-glass opacity-80">
                    Scanner akan mengisi NIS otomatis. Tekan Enter atau klik Tap untuk mengirim.
                </p>
            </div>
        </div>

        <script>
            const inputNis = document.getElementById('nis');
            const clearBtn = document.getElementById('clearBtn');
            const toast = document.getElementById('toast');

            // Clear button functionality
            clearBtn.addEventListener('click', function() {
                inputNis.value = '';
                inputNis.focus();
            });

            // Auto submit on Enter
            inputNis.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.form.submit();
                }
            });

            // Auto hide toast setelah 3 detik
            if (toast) {
                setTimeout(() => {
                    toast.style.transition = 'opacity 0.5s';
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 500);
                }, 3000);

                // Auto clear dan re-focus setelah toast
                setTimeout(() => {
                    inputNis.value = '';
                    inputNis.focus();
                }, 3500);
            }

            // Initial focus
            setTimeout(() => inputNis.focus(), 100);

            // Maintain focus
            document.addEventListener('click', () => {
                setTimeout(() => inputNis.focus(), 50);
            });

            window.addEventListener('blur', () => {
                setTimeout(() => inputNis.focus(), 100);
            });

            // Re-focus berkala
            setInterval(() => {
                if (document.activeElement !== inputNis) {
                    inputNis.focus();
                }
            }, 500);
        </script>
    </div>
</body>

</html>
