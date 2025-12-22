<section id="beranda" class="relative pt-32 pb-20 lg:pt-40 lg:pb-32 gradient-bg overflow-hidden">
    <!-- Decorative Elements -->
    <div class="absolute top-20 right-10 w-72 h-72 bg-tosca-200 dark:bg-tosca-800 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-30 dark:opacity-20 animate-pulse"></div>
    <div class="absolute bottom-20 left-10 w-72 h-72 bg-blue-200 dark:bg-blue-800 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-3xl opacity-30 dark:opacity-20 animate-pulse" style="animation-delay: 1s;"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <!-- Left Content -->
            <div data-aos="fade-right" data-aos-duration="1000">
                <div class="inline-block mb-4">
                    {{-- <span class="bg-tosca-100 dark:bg-tosca-900/50 text-tosca-700 dark:text-tosca-300 px-4 py-2 rounded-full text-sm font-semibold">
                        🏥 Smart Hospital System
                    </span> --}}
                </div>
                <h1 class="text-5xl lg:text-6xl font-extrabold text-gray-900 dark:text-white leading-tight mb-6">
                    Pelayanan Kesehatan
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-tosca-500 to-tosca-700 dark:from-tosca-400 dark:to-tosca-500">
                        Modern & Terintegrasi
                    </span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                    Sistem Informasi Manajemen Rumah Sakit berbasis digital dengan teknologi terkini untuk efisiensi operasional dan pelayanan pasien yang optimal.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="gradient-tosca text-white px-8 py-4 rounded-xl font-semibold hover:shadow-2xl transition transform hover:scale-105 btn-ripple">
                        <i class="fas fa-file-medical mr-2"></i>Lihat SOP Digital
                    </button>
                    <a href="{{ route('filament.admin.auth.login') }}" class="bg-white dark:bg-gray-800 text-tosca-600 dark:text-tosca-400 px-8 py-4 rounded-xl font-semibold border-2 border-tosca-500 dark:border-tosca-600 hover:bg-tosca-50 dark:hover:bg-gray-700 transition transform hover:scale-105 text-center">
                        <i class="fas fa-desktop mr-2"></i>Masuk Sistem
                    </a>
                </div>

                <!-- Mini Stats -->
                <div class="grid grid-cols-3 gap-4 mt-12" data-aos="fade-up" data-aos-delay="200">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-tosca-600 dark:text-tosca-400">24/7</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Layanan</div>
                    </div>
                    <div class="text-center border-x border-gray-200 dark:border-gray-700">
                        <div class="text-3xl font-bold text-tosca-600 dark:text-tosca-400">100%</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Digital</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-tosca-600 dark:text-tosca-400">ISO</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Certified</div>
                    </div>
                </div>
            </div>

            <!-- Right Illustration -->
            <div class="relative" data-aos="fade-left" data-aos-duration="1000">
                <div class="relative z-10 float-animation">
                    <!-- Doctor Illustration -->
                    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl dark:shadow-gray-900/50 p-8 border border-gray-100 dark:border-gray-700">
                        <div class="text-center">
                            <i class="fas fa-user-md text-9xl text-tosca-500 dark:text-tosca-400 mb-4"></i>
                            <div class="space-y-3">
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mx-auto"></div>
                                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2 mx-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Floating Cards -->
                <div class="absolute -top-8 -right-8 bg-white dark:bg-gray-800 rounded-2xl shadow-xl dark:shadow-gray-900/50 p-4 pulse-glow" data-aos="zoom-in" data-aos-delay="400">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Status</div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">Verified</div>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-8 -left-8 bg-white dark:bg-gray-800 rounded-2xl shadow-xl dark:shadow-gray-900/50 p-4" data-aos="zoom-in" data-aos-delay="600">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                            <i class="fas fa-heartbeat text-blue-600 dark:text-blue-400 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Real-time</div>
                            <div class="text-sm font-bold text-gray-900 dark:text-white">Monitoring</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
