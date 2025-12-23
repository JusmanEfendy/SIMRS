@props(['stats'])

<section class="py-20 bg-white relative" x-data="statsCounter({{ $stats['sop_count'] }}, {{ $stats['directorate_count'] }}, {{ $stats['unit_count'] }})">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Stat Card 1 - SOP Aktif -->
            <div class="bg-gradient-to-br from-tosca-500 to-tosca-700 rounded-3xl p-8 text-white card-hover relative overflow-hidden" data-aos="fade-up" data-aos-delay="0">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-file-medical text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-file-medical text-4xl"></i>
                        <!-- <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-xs font-semibold">Active</span> -->
                    </div>
                    <h3 class="text-5xl font-bold mb-2" x-text="sopCount"></h3>
                    <p class="text-tosca-100 font-medium">SOP Aktif</p>
                    <div class="mt-4 flex items-center text-sm">
                        <i class="fas fa-file-alt mr-2"></i>
                        <span>Dokumen terverifikasi</span>
                    </div>
                </div>
            </div>

            <!-- Stat Card 2 - Direktorat -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-3xl p-8 text-white card-hover relative overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-sitemap text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-sitemap text-4xl"></i>
                        <!-- <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-xs font-semibold">Struktur</span> -->
                    </div>
                    <h3 class="text-5xl font-bold mb-2" x-text="directorateCount"></h3>
                    <p class="text-blue-100 font-medium">Direktorat</p>
                    <div class="mt-4 flex items-center text-sm">
                        <i class="fas fa-check-circle mr-2"></i>
                        <span>Direktorat aktif</span>
                    </div>
                </div>
            </div>

            <!-- Stat Card 3 - Unit Kerja -->
            <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-3xl p-8 text-white card-hover relative overflow-hidden" data-aos="fade-up" data-aos-delay="400">
                <div class="absolute top-0 right-0 opacity-10">
                    <i class="fas fa-building text-9xl"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <i class="fas fa-building text-4xl"></i>
                        <!-- <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-xs font-semibold">Online</span> -->
                    </div>
                    <h3 class="text-5xl font-bold mb-2" x-text="unitCount"></h3>
                    <p class="text-green-100 font-medium">Unit Kerja</p>
                    <div class="mt-4 flex items-center text-sm">
                        <i class="fas fa-users mr-2"></i>
                        <span>Semua unit aktif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
function statsCounter(sopTarget, directorateTarget, unitTarget) {
    return {
        sopCount: 0,
        directorateCount: 0,
        unitCount: 0,

        init() {
            this.animateCounter('sopCount', sopTarget, 2000);
            this.animateCounter('directorateCount', directorateTarget, 2000);
            this.animateCounter('unitCount', unitTarget, 2000);
        },

        animateCounter(property, target, duration) {
            const startTime = Date.now();
            const startValue = 0;

            const animate = () => {
                const currentTime = Date.now();
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);

                const easeOut = 1 - Math.pow(1 - progress, 3);

                this[property] = Math.floor(startValue + (target - startValue) * easeOut);

                if (progress < 1) {
                    requestAnimationFrame(animate);
                }
            };

            animate();
        }
    }
}
</script>
@endpush
