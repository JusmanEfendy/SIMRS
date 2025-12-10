{{-- PDF Viewer Component with Modern Styling --}}
@php
    $fileName = isset($record) ? $record->sop_name : 'Dokumen SOP';
    $fileId = isset($record) ? $record->id_sop : '';
@endphp

@if (!empty($url))
    <div class="pdf-viewer-container">
        {{-- Header dengan info dokumen --}}
        <div class="pdf-header">
            <div class="pdf-header-left">
                <div class="pdf-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <div class="pdf-info">
                    <h3 class="pdf-title">{{ $fileName ?? 'Dokumen SOP' }}</h3>
                    @if($fileId)
                        <span class="pdf-id">{{ $fileId }}</span>
                    @endif
                </div>
            </div>
            <div class="pdf-header-right">
                {{-- Tombol Download --}}
                <a href="{{ $url }}" 
                   download 
                   class="pdf-btn pdf-btn-primary"
                   title="Download PDF">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Download</span>
                </a>
                {{-- Tombol Print --}}
                <button onclick="printPdf('{{ $url }}')" 
                        class="pdf-btn pdf-btn-secondary"
                        title="Print PDF">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Print</span>
                </button>
                {{-- Tombol Fullscreen --}}
                <a href="{{ $url }}" 
                   target="_blank" 
                   class="pdf-btn pdf-btn-secondary"
                   title="Buka di Tab Baru">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>Fullscreen</span>
                </a>
            </div>
        </div>

        {{-- PDF Viewer Frame --}}
        <div class="pdf-frame-wrapper">
            <iframe
                src="{{ $url }}#toolbar=1&navpanes=0&scrollbar=1&view=FitH"
                class="pdf-frame"
                frameborder="0"
                allowfullscreen
                loading="lazy"
            ></iframe>
        </div>

        {{-- Footer dengan tips --}}
        <div class="pdf-footer">
            <div class="pdf-footer-tip">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Gunakan scroll untuk navigasi halaman. Klik Fullscreen untuk tampilan lebih besar.</span>
            </div>
        </div>
    </div>

    <style>
        .pdf-viewer-container {
            display: flex;
            flex-direction: column;
            height: 80vh;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .pdf-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            flex-shrink: 0;
        }

        .pdf-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .pdf-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .pdf-icon svg {
            width: 28px;
            height: 28px;
        }

        .pdf-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .pdf-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 0;
            max-width: 400px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pdf-id {
            font-size: 0.75rem;
            opacity: 0.8;
            font-family: monospace;
            background: rgba(255, 255, 255, 0.15);
            padding: 2px 8px;
            border-radius: 4px;
            width: fit-content;
        }

        .pdf-header-right {
            display: flex;
            gap: 8px;
        }

        .pdf-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
        }

        .pdf-btn svg {
            width: 18px;
            height: 18px;
        }

        .pdf-btn-primary {
            background: rgba(255, 255, 255, 0.95);
            color: #1e40af;
        }

        .pdf-btn-primary:hover {
            background: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .pdf-btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            backdrop-filter: blur(10px);
        }

        .pdf-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .pdf-frame-wrapper {
            flex: 1;
            background: #374151;
            padding: 8px;
            overflow: hidden;
        }

        .pdf-frame {
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 8px;
            background: white;
        }

        .pdf-footer {
            padding: 12px 20px;
            background: #f1f5f9;
            border-top: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .pdf-footer-tip {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: #64748b;
        }

        .pdf-footer-tip svg {
            flex-shrink: 0;
            color: #3b82f6;
        }

        /* Dark mode support */
        .dark .pdf-viewer-container {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .dark .pdf-footer {
            background: #1e293b;
            border-top-color: #334155;
        }

        .dark .pdf-footer-tip {
            color: #94a3b8;
        }

        .dark .pdf-frame-wrapper {
            background: #0f172a;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .pdf-header {
                flex-direction: column;
                gap: 12px;
                padding: 12px 16px;
            }

            .pdf-header-left {
                width: 100%;
            }

            .pdf-header-right {
                width: 100%;
                justify-content: center;
            }

            .pdf-btn span {
                display: none;
            }

            .pdf-btn {
                padding: 10px;
            }

            .pdf-title {
                font-size: 0.95rem;
                max-width: 200px;
            }
        }
    </style>

    <script>
        function printPdf(url) {
            const printWindow = window.open(url, '_blank');
            printWindow.addEventListener('load', function() {
                printWindow.print();
            });
        }
    </script>
@else
    {{-- Empty State --}}
    <div class="pdf-empty-state">
        <div class="pdf-empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>
        <h4 class="pdf-empty-title">Dokumen Tidak Tersedia</h4>
        <p class="pdf-empty-desc">File PDF untuk dokumen ini belum diunggah atau tidak dapat ditemukan.</p>
    </div>

    <style>
        .pdf-empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            text-align: center;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 16px;
            min-height: 300px;
        }

        .pdf-empty-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100px;
            height: 100px;
            background: #e2e8f0;
            border-radius: 50%;
            margin-bottom: 20px;
            color: #94a3b8;
        }

        .pdf-empty-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #334155;
            margin: 0 0 8px 0;
        }

        .pdf-empty-desc {
            font-size: 0.9rem;
            color: #64748b;
            margin: 0;
            max-width: 300px;
        }

        .dark .pdf-empty-state {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        }

        .dark .pdf-empty-icon {
            background: #334155;
            color: #64748b;
        }

        .dark .pdf-empty-title {
            color: #e2e8f0;
        }

        .dark .pdf-empty-desc {
            color: #94a3b8;
        }
    </style>
@endif
