<section id="beranda" class="relative pt-32 pb-20 lg:pt-40 lg:pb-32 gradient-bg overflow-hidden"
    x-data="sopModal()"
    x-cloak>
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
                    Manajemen SOP
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-tosca-500 to-tosca-700 dark:from-tosca-400 dark:to-tosca-500">
                        Digital & Terintegrasi
                    </span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 leading-relaxed">
                    Sistem Informasi Pengajuan SOP berbasis digital dengan teknologi terkini untuk efisiensi operasional.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <button @click="openModal()" class="gradient-tosca text-white px-8 py-4 rounded-xl font-semibold hover:shadow-2xl transition transform hover:scale-105 btn-ripple">
                        <i class="fas fa-file-medical mr-2"></i>Lihat SOP Digital
                    </button>
                    {{-- <a href="{{ route('filament.admin.auth.login') }}" class="bg-white dark:bg-gray-800 text-tosca-600 dark:text-tosca-400 px-8 py-4 rounded-xl font-semibold border-2 border-tosca-500 dark:border-tosca-600 hover:bg-tosca-50 dark:hover:bg-gray-700 transition transform hover:scale-105 text-center">
                        <i class="fas fa-desktop mr-2"></i>Masuk Sistem
                    </a> --}}
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

    <!-- SOP Modal Popup -->
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm" @click="closeModal()"></div>

        <!-- Modal Content -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="isOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-2xl shadow-2xl">

                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                            <i class="fas fa-file-medical text-tosca-500"></i>
                            Dokumen SOP Digital
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Daftar Standard Operating Procedure yang aktif
                        </p>
                    </div>
                    <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="relative">
                        <input type="text"
                               x-model="searchQuery"
                               @input.debounce.300ms="fetchSops()"
                               placeholder="Cari SOP berdasarkan nama atau unit kerja..."
                               class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-tosca-500 focus:border-transparent transition">
                        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="p-6 max-h-[60vh] overflow-y-auto">
                    <!-- Loading State -->
                    <div x-show="isLoading" class="flex items-center justify-center py-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-4 border-tosca-500 border-t-transparent"></div>
                    </div>

                    <!-- SOP List -->
                    <div x-show="!isLoading && sops.length > 0" class="space-y-4">
                        <template x-for="sop in sops" :key="sop.id">
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition group">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 dark:text-white text-lg truncate" x-text="sop.sop_name"></h4>
                                        <div class="flex flex-wrap items-center gap-3 mt-2 text-sm">
                                            <span class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-300">
                                                <i class="fas fa-building text-tosca-500"></i>
                                                <span x-text="sop.unit_name"></span>
                                            </span>
                                            <span class="inline-flex items-center gap-1 text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-hashtag"></i>
                                                <span x-text="sop.sk_number"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <template x-if="sop.file_url">
                                            <a :href="sop.file_url"
                                               target="_blank"
                                               class="inline-flex items-center gap-2 px-4 py-2 bg-tosca-500 hover:bg-tosca-600 text-white rounded-lg font-medium transition transform hover:scale-105">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="hidden sm:inline">Lihat PDF</span>
                                            </a>
                                        </template>
                                        <template x-if="!sop.file_url">
                                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 rounded-lg font-medium cursor-not-allowed">
                                                <i class="fas fa-file-pdf"></i>
                                                <span class="hidden sm:inline">Tidak Ada</span>
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Empty State -->
                    <div x-show="!isLoading && sops.length === 0" class="text-center py-12">
                        <i class="fas fa-folder-open text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Tidak Ada SOP Ditemukan</h4>
                        <p class="text-gray-500 dark:text-gray-400" x-text="searchQuery ? 'Coba kata kunci lain' : 'Belum ada dokumen SOP yang tersedia'"></p>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-between p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-2xl">
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        Total: <span class="font-semibold text-tosca-600 dark:text-tosca-400" x-text="total"></span> SOP aktif
                    </span>
                    <button @click="closeModal()" class="px-6 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function sopModal() {
    return {
        isOpen: false,
        isLoading: false,
        sops: [],
        total: 0,
        searchQuery: '',

        openModal() {
            this.isOpen = true;
            this.fetchSops();
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.isOpen = false;
            this.searchQuery = '';
            document.body.style.overflow = '';
        },

        async fetchSops() {
            this.isLoading = true;
            try {
                const response = await fetch(`/api/sops?search=${encodeURIComponent(this.searchQuery)}`);
                const result = await response.json();
                if (result.success) {
                    this.sops = result.data;
                    this.total = result.total;
                }
            } catch (error) {
                console.error('Error fetching SOPs:', error);
                this.sops = [];
                this.total = 0;
            } finally {
                this.isLoading = false;
            }
        }
    }
}
</script>

