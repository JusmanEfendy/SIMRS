<nav class="fixed w-full top-0 z-50 navbar-blur dark:navbar-blur-dark border-b border-gray-100 dark:border-gray-800 transition-colors duration-300" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                {{-- <div class="w-12 h-12 gradient-tosca rounded-xl flex items-center justify-center">
                    <i class="fas fa-hospital text-white text-2xl"></i>
                </div> --}}
                <div class="flex flex-col">
                    <img
                        src="{{ asset('images/logo-kemenkes.png') }}"
                        alt="Logo RSUP Ngoerah"
                        class="h-10 w-auto object-contain dark:brightness-110"
                    >
                    <span class="text-xs text-tosca-600 dark:text-tosca-400 font-medium tracking-wide">
                        RSUP Prof. Dr.I.G.N.G Ngoerah
                    </span>
                </div>

            </div>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center space-x-8">
                <a href="#beranda" class="text-gray-700 dark:text-gray-200 hover:text-tosca-600 dark:hover:text-tosca-400 font-medium transition">Beranda</a>
                <a href="#layanan" class="text-gray-700 dark:text-gray-200 hover:text-tosca-600 dark:hover:text-tosca-400 font-medium transition">Layanan</a>
                <a href="#sop" class="text-gray-700 dark:text-gray-200 hover:text-tosca-600 dark:hover:text-tosca-400 font-medium transition">SOP</a>
                <a href="#panduan" class="text-gray-700 dark:text-gray-200 hover:text-tosca-600 dark:hover:text-tosca-400 font-medium transition">Panduan</a>

                <!-- Dark Mode Toggle -->
                <button
                    @click="darkMode = !darkMode"
                    class="p-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200"
                    :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                >
                    <i x-show="!darkMode" class="fas fa-moon text-gray-600 text-lg"></i>
                    <i x-show="darkMode" x-cloak class="fas fa-sun text-gray-200 text-lg"></i>
                </button>

                <a href="{{ route('filament.admin.auth.login') }}" class="gradient-tosca text-white px-6 py-2.5 rounded-xl font-semibold hover:shadow-lg transition btn-ripple">
                    <i class="fas fa-sign-in-alt mr-2"></i>Login SIPSOP
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center space-x-3 lg:hidden">
                <!-- Dark Mode Toggle Mobile -->
                <button
                    @click="darkMode = !darkMode"
                    class="p-2.5 rounded-xl bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200"
                >
                    <i x-show="!darkMode" class="fas fa-moon text-gray-600 text-lg"></i>
                    <i x-show="darkMode" x-cloak class="fas fa-sun text-yellow-400 text-lg"></i>
                </button>

                <button @click="mobileOpen = !mobileOpen" class="text-gray-700 dark:text-gray-200">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="lg:hidden bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
        <div class="px-4 py-4 space-y-3">
            <a href="#beranda" class="block text-gray-700 dark:text-gray-200 hover:text-tosca-600 dark:hover:text-tosca-400 font-medium">Beranda</a>
            <a href="#layanan" class="block text-gray-700 dark:text-gray-200 hover:text-tosca-600 dark:hover:text-tosca-400 font-medium">Layanan</a>
            <a href="#sop" class="block text-gray-700 dark:text-gray-200 hover:text-tosca-600 dark:hover:text-tosca-400 font-medium">SOP</a>
            <a href="#dokter" class="block text-gray-700 dark:text-gray-200 hover:text-tosca-600 dark:hover:text-tosca-400 font-medium">Dokter</a>
            <a href="#jadwal" class="block text-gray-700 dark:text-gray-200 hover:text-tosca-600 dark:hover:text-tosca-400 font-medium">Jadwal</a>
            <a href="#kontak" class="block text-gray-700 dark:text-gray-200 hover:text-tosca-600 dark:hover:text-tosca-400 font-medium">Kontak</a>
            <a href="{{ route('filament.admin.auth.login') }}" class="block w-full gradient-tosca text-white px-6 py-2.5 rounded-xl font-semibold text-center">
                <i class="fas fa-sign-in-alt mr-2"></i>Login SIPSOP
            </a>
        </div>
    </div>
</nav>

<style>
    [x-cloak] { display: none !important; }
</style>
