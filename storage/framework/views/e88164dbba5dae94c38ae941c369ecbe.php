<?php $__env->startSection('title', 'Scan QR Code'); ?>
<?php $__env->startSection('page-title', 'Scan QR Code'); ?>

<?php $__env->startSection('content'); ?>
<!-- ════════════════════════════════════════════════════════════════
     LOAD HTML5-QRCODE LIBRARY FIRST (BEFORE scanner.js runs)
     Using jsDelivr CDN - minified UMD dist for browser compatibility
     ═════════════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@latest/dist/html5-qrcode.min.js"></script>

<div class="max-w-6xl mx-auto px-4 py-6">
    <!-- ══════════════════════════════════════════════════════════════
         BREADCRUMB & HEADER
         ═════════════════════════════════════════════════════════════ -->
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-sm mb-4">
            <a href="<?php echo e(route('dashboard')); ?>" class="text-gold-lt hover:text-gold transition-colors duration-200">Dashboard</a>
            <span class="text-bark-light/40">/</span>
            <a href="<?php echo e(route('rentals.index')); ?>" class="text-gold-lt hover:text-gold transition-colors duration-200">Penyewaan</a>
            <span class="text-bark-light/40">/</span>
            <span class="text-bark font-semibold">Scan QR Code</span>
        </nav>

        <div class="mb-6">
            <h1 class="text-3xl font-bold tracking-tight mb-2" style="color: var(--text-dark)">Scan QR Code</h1>
            <p class="text-sm leading-relaxed" style="color: var(--text-soft)">
                Arahkan kamera ke QR Code pada Invoice, Receipt, atau Rental untuk melihat detail. 
                QR Code dapat diperoleh dari dokumen atau aplikasi SewaJas.
            </p>
        </div>
    </div>

    <!-- ══════════════════════════════════════════════════════════════
         MAIN CONTENT - RESPONSIVE GRID
         ═════════════════════════════════════════════════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- ══════════════════════════════════════════════════════════
             LEFT COLUMN - SCANNER AREA (Large)
             ═══════════════════════════════════════════════════════ -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 overflow-hidden hover:shadow-lg transition-all duration-300">
                <!-- Camera Preview Container -->
                <div class="relative rounded-xl overflow-hidden aspect-square max-w-2xl mx-auto"
                     style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #312e81 100%); box-shadow: inset 0 0 60px rgba(15, 23, 42, 0.5);">
                    
                    <!-- Loading Spinner -->
                    <div id="camera-loader" class="absolute inset-0 flex items-center justify-center bg-black/30 backdrop-blur-sm z-40 hidden">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-12 h-12 border-4 border-gold-lt border-t-gold rounded-full animate-spin"></div>
                            <span class="text-white text-sm font-medium">Membuka Kamera...</span>
                        </div>
                    </div>

                    <!-- QR Scanner Area (html5-qrcode will render here) -->
                    <div id="qr-reader" class="w-full h-full"></div>

                    <!-- Scan Frame Border (Visual Guide) -->
                    <div class="absolute inset-0 pointer-events-none" style="border: 2px dashed rgba(201, 168, 76, 0.3);">
                        <!-- Corner Markers -->
                        <div class="absolute top-4 left-4 w-8 h-8 border-l-2 border-t-2 border-gold/70"></div>
                        <div class="absolute top-4 right-4 w-8 h-8 border-r-2 border-t-2 border-gold/70"></div>
                        <div class="absolute bottom-4 left-4 w-8 h-8 border-l-2 border-b-2 border-gold/70"></div>
                        <div class="absolute bottom-4 right-4 w-8 h-8 border-r-2 border-b-2 border-gold/70"></div>

                        <!-- Center Text -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="bg-black/40 backdrop-blur-sm text-center px-4 py-2 rounded-lg">
                                <p class="text-gold text-xs font-semibold tracking-wider uppercase">Arahkan QR Code ke Kamera</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Camera Status -->
                <div class="mt-6 flex items-center justify-between">
                    <div id="camera-status" class="flex items-center gap-2">
                        <span class="inline-block w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-sm text-red-600">Kamera Tidak Aktif</span>
                    </div>
                    <div class="text-xs text-bark-light">
                        <?php if(env('APP_ENV') === 'production'): ?>
                            <span class="text-green-600 font-semibold">✓ HTTPS Aktif</span>
                        <?php else: ?>
                            <span class="text-yellow-600">⚠ Dev Mode</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 CAMERA CONTROLS
                 ═══════════════════════════════════════════════════════ -->
            <div class="mt-6 bg-white border border-slate-200 rounded-2xl shadow-sm p-4 hover:shadow-lg transition-all duration-300">
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Start Button — PRIMARY -->
                    <button id="start-camera-btn"
                            class="flex-1 px-4 py-3 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md hover:-translate-y-0.5"
                            type="button"
                            title="Klik untuk memulai scanner kamera">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Mulai Kamera</span>
                    </button>

                    <!-- Stop Button — DANGER -->
                    <button id="stop-camera-btn"
                            class="flex-1 px-4 py-3 bg-red-500 hover:bg-red-600 text-white font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md hover:-translate-y-0.5"
                            type="button"
                            title="Klik untuk menghentikan scanner">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                        </svg>
                        <span>Hentikan</span>
                    </button>

                    <!-- Switch Camera Button — SECONDARY (outline) -->
                    <button id="switch-camera-btn"
                            class="flex-1 px-4 py-3 bg-white border-2 border-slate-200 hover:border-blue-500 hover:bg-blue-50 text-slate-700 hover:text-blue-700 font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-sm hover:shadow-md hover:-translate-y-0.5"
                            type="button"
                            title="Klik untuk mengganti kamera">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span class="hidden sm:inline">Tukar Kamera</span>
                        <span class="sm:hidden">Tukar</span>
                    </button>
                </div>

                <!-- Camera Selector (if multiple cameras available) -->
                <div class="mt-3" id="camera-selector-wrapper" style="display: none;">
                    <label for="camera-select" class="block text-sm font-semibold mb-2" style="color:var(--text-dark)">Pilih Kamera:</label>
                    <select id="camera-select" class="form-input">
                        <option>Pilih Kamera...</option>
                    </select>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 AUTO-START PREFERENCE
                 ═══════════════════════════════════════════════════════ -->
            <div class="mt-4 bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center gap-3">
                <input type="checkbox" id="auto-start-checkbox" class="w-4 h-4 rounded cursor-pointer text-blue-600 border-slate-300 focus:ring-blue-500" />
                <label for="auto-start-checkbox" class="text-sm cursor-pointer font-medium" style="color:var(--text-dark)">
                    Mulai kamera otomatis saat halaman dibuka
                </label>
            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════
             RIGHT COLUMN - INFO & HISTORY (Sidebar)
             ═══════════════════════════════════════════════════════ -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- ═══════════════════════════════════════════════════════
                 HASIL SCAN
                 ═══════════════════════════════════════════════════════ -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 hover:shadow-lg transition-all duration-300">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2" style="color: var(--text-dark)">
                    <svg class="w-5 h-5" style="color: var(--primary)" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Hasil Scan Terakhir
                </h3>

                <div id="scan-result-container" class="space-y-3">
                    <div class="text-center py-6">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-3" style="background: rgba(37,99,235,.08);">
                            <svg class="w-7 h-7" style="color: var(--primary); opacity: .7;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium" style="color: var(--text-soft)">Belum ada QR yang dipindai</p>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 RIWAYAT SCAN
                 ═══════════════════════════════════════════════════════ -->
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 hover:shadow-lg transition-all duration-300">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2" style="color: var(--text-dark)">
                    <svg class="w-5 h-5" style="color: var(--primary)" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000-2H6a6 6 0 016 6v3.586l1.707-1.707a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 9.586V8a4 4 0 00-4-4H4z" clip-rule="evenodd"></path>
                    </svg>
                    Riwayat Scan
                </h3>

                <div id="scan-history-container" class="space-y-2">
                    <div class="text-center py-6">
                        <p class="text-sm font-medium" style="color: var(--text-soft)">Tidak ada riwayat</p>
                    </div>
                </div>
            </div>

            <!-- ═══════════════════════════════════════════════════════
                 TIPS & INFORMASI
                 ═══════════════════════════════════════════════════════ -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <h4 class="font-semibold text-blue-900 text-sm mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    Tips Scanning
                </h4>
                <ul class="text-xs text-blue-800 space-y-2">
                    <li class="flex gap-2">
                        <span class="flex-shrink-0">✓</span>
                        <span>Pastikan pencahayaan cukup</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="flex-shrink-0">✓</span>
                        <span>Arahkan QR Code ke tengah layar</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="flex-shrink-0">✓</span>
                        <span>Jangan menggerakkan terlalu cepat</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="flex-shrink-0">✓</span>
                        <span>Browser perlu akses ke kamera</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════
     ALPINE.JS PAGE STATE
     ═════════════════════════════════════════════════════════════ -->
<script>
function scannerPage() {
  return {
    isScanning: false,

    async startCamera() {
      if (window.QRScanner && window.QRScanner.start) {
        await window.QRScanner.start();
        this.isScanning = true;
      }
    },

    async stopCamera() {
      if (window.QRScanner && window.QRScanner.stop) {
        await window.QRScanner.stop();
        this.isScanning = false;
      }
    },

    async switchCamera() {
      if (window.QRScanner && window.QRScanner.switch) {
        await window.QRScanner.switch();
      }
    }
  };
}
</script>

<!-- ════════════════════════════════════════════════════════════════
     CUSTOM ANIMATIONS & STYLES
     ═════════════════════════════════════════════════════════════ -->
<style>
  /* Hide html5-qrcode buttons (we provide our own) */
  #qr-reader__scan_region {
    position: relative;
  }

  /* Customize scanner container */
  #qr-reader > div {
    border-radius: 0.75rem;
  }

  /* Toast animations */
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(1rem);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes fadeOut {
    from {
      opacity: 1;
      transform: translateY(0);
    }
    to {
      opacity: 0;
      transform: translateY(1rem);
    }
  }

  .animate-fadeIn {
    animation: fadeIn 0.3s ease-out forwards;
  }

  .animate-fadeOut {
    animation: fadeOut 0.3s ease-out forwards;
  }

  /* Scanner frame animation */
  @keyframes scanPulse {
    0%, 100% {
      border-color: rgba(201, 168, 76, 0.3);
    }
    50% {
      border-color: rgba(201, 168, 76, 0.6);
    }
  }

  #qr-reader:hover {
    animation: scanPulse 2s ease-in-out infinite;
  }

  /* Responsive adjustments for mobile */
  @media (max-width: 768px) {
    #qr-reader {
      height: auto;
      max-width: 100% !important;
    }

    .max-w-6xl {
      padding-left: 1rem;
      padding-right: 1rem;
    }
  }
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('Layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp82\htdocs\rental-jas-main\resources\views/rentals/scan.blade.php ENDPATH**/ ?>