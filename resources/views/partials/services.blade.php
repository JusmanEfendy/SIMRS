<section id="layanan" class="py-20 gradient-bg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16" data-aos="fade-up">
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                Layanan <span class="text-tosca-600 dark:text-tosca-400">Unggulan</span>
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                Sistem terintegrasi untuk mendukung operasional rumah sakit modern
            </p>
        </div>

        <!-- Services Grid -->
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($services as $index => $service)
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 card-hover border border-gray-100 dark:border-gray-700" data-aos="zoom-in" data-aos-delay="{{ $index * 100 }}">
                <div class="w-16 h-16 bg-gradient-to-br from-{{ $service['color'] }}-500 to-{{ $service['color'] }}-700 rounded-2xl flex items-center justify-center mb-6 {{ $index === 0 ? 'pulse-glow' : '' }}">
                    <i class="fas {{ $service['icon'] }} text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ $service['title'] }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $service['description'] }}</p>
                <a href="#" class="text-tosca-600 dark:text-tosca-400 font-semibold hover:text-tosca-700 dark:hover:text-tosca-300 inline-flex items-center">
                    Selengkapnya <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
