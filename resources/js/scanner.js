// ════════════════════════════════════════════════════════════════
// QR SCANNER - CORRECT IMPLEMENTATION
// Using Html5Qrcode (NOT Html5QrcodeScanner)
// ════════════════════════════════════════════════════════════════

console.log('[QR Scanner] Module loading...');

// Global state
let scanner = null; // Html5Qrcode instance
let isScanning = false;
let selectedCameraId = null;
let availableCameras = [];
let scanHistory = [];

// ════════════════════════════════════════════════════════════════
// LOGGING
// ════════════════════════════════════════════════════════════════

function log(msg, data) {
  const time = new Date().toLocaleTimeString();
  console.log(`[QR Scanner ${time}] ${msg}`, data || '');
}

function logError(msg, err) {
  const time = new Date().toLocaleTimeString();
  console.error(`[QR Scanner ERROR ${time}] ${msg}`, err || '');
}

function logWarn(msg, data) {
  const time = new Date().toLocaleTimeString();
  console.warn(`[QR Scanner WARN ${time}] ${msg}`, data || '');
}

// ════════════════════════════════════════════════════════════════
// BROWSER SUPPORT CHECK
// ════════════════════════════════════════════════════════════════

function checkBrowserSupport() {
  if (!navigator.mediaDevices) {
    logError('navigator.mediaDevices not available');
    return false;
  }
  if (!navigator.mediaDevices.getUserMedia) {
    logError('getUserMedia not available');
    return false;
  }
  log('✓ Browser support OK');
  return true;
}

// ════════════════════════════════════════════════════════════════
// GET CAMERAS
// ════════════════════════════════════════════════════════════════

async function getAvailableCameras() {
  try {
    // CDN library provides Html5Qrcode.getCameras()
    if (!window.Html5Qrcode) {
      logError('Html5Qrcode not loaded');
      return [];
    }

    availableCameras = await window.Html5Qrcode.getCameras();
    log(`Found ${availableCameras.length} camera(s)`, availableCameras);

    if (availableCameras.length === 0) {
      handleError('NO_CAMERA', 'No cameras found');
      return [];
    }

    // Auto-select back camera
    const backCamera = availableCameras.find(cam => {
      const label = cam.label.toLowerCase();
      return label.includes('back') || label.includes('rear');
    });

    if (backCamera) {
      selectedCameraId = backCamera.id;
      log(`Selected back camera: ${backCamera.label}`);
    } else {
      selectedCameraId = availableCameras[0].id;
      log(`Selected first camera: ${availableCameras[0].label}`);
    }

    return availableCameras;
  } catch (err) {
    logError('Error getting cameras:', err);
    return [];
  }
}

// ════════════════════════════════════════════════════════════════
// INITIALIZE SCANNER
// ════════════════════════════════════════════════════════════════

async function initializeScanner() {
  try {
    log('Creating Html5Qrcode instance...');
    
    // IMPORTANT: Use Html5Qrcode (NOT Html5QrcodeScanner)
    // Html5Qrcode has: .start(), .stop(), .clear()
    scanner = new window.Html5Qrcode('qr-reader');
    
    log('✓ Scanner instance created');
    log('Scanner type:', typeof scanner);
    log('Scanner.start:', typeof scanner.start);
    log('Scanner.stop:', typeof scanner.stop);
    log('Scanner.clear:', typeof scanner.clear);

    if (typeof scanner.start !== 'function') {
      logError('CRITICAL: scanner.start is not a function');
      logError('scanner object:', scanner);
      return false;
    }

    setupEventListeners();
    log('✓ Event listeners registered');
    
    return true;

  } catch (err) {
    logError('Failed to initialize scanner:', err);
    return false;
  }
}

// ════════════════════════════════════════════════════════════════
// EVENT LISTENERS
// ════════════════════════════════════════════════════════════════

function setupEventListeners() {
  const startBtn = document.getElementById('start-camera-btn');
  const stopBtn = document.getElementById('stop-camera-btn');
  const switchBtn = document.getElementById('switch-camera-btn');

  if (!startBtn) {
    logWarn('start-camera-btn not found');
  } else {
    startBtn.addEventListener('click', startCamera);
    log('✓ start-camera-btn listener added');
  }

  if (!stopBtn) {
    logWarn('stop-camera-btn not found');
  } else {
    stopBtn.addEventListener('click', stopCamera);
    log('✓ stop-camera-btn listener added');
  }

  if (!switchBtn) {
    logWarn('switch-camera-btn not found');
  } else {
    switchBtn.addEventListener('click', switchCamera);
    log('✓ switch-camera-btn listener added');
  }
}

// ════════════════════════════════════════════════════════════════
// START CAMERA
// ════════════════════════════════════════════════════════════════

async function startCamera() {
  log('═══════════════════════════════════════════');
  log('START CAMERA BUTTON CLICKED');
  log('═══════════════════════════════════════════');

  if (!scanner) {
    logError('Scanner not initialized');
    handleError('NOT_READY', 'Scanner not ready. Refresh page.');
    return;
  }

  if (isScanning) {
    logWarn('Already scanning');
    return;
  }

  try {
    showLoading();

    // Get cameras
    const cameras = await getAvailableCameras();
    if (cameras.length === 0) {
      hideLoading();
      return;
    }

    log('Starting camera...');
    log('Using camera ID:', selectedCameraId);

    // IMPORTANT: Call .start() with proper parameters
    // .start(cameraIdOrConfig, config, onSuccess, onFailure)
    await scanner.start(
      selectedCameraId,
      {
        fps: 10,
        qrbox: { width: 280, height: 280 }
      },
      onScanSuccess,
      onScanFailure
    );

    isScanning = true;
    log('✓✓✓ Camera started ✓✓✓');
    updateStatus('active');
    hideLoading();
    showToast('Camera started', 'success');

  } catch (err) {
    logError('Error starting camera:', err);
    handleError('START_ERROR', `Camera error: ${err.message}`);
    hideLoading();
  }
}

// ════════════════════════════════════════════════════════════════
// STOP CAMERA
// ════════════════════════════════════════════════════════════════

async function stopCamera() {
  log('═══════════════════════════════════════════');
  log('STOP CAMERA BUTTON CLICKED');
  log('═══════════════════════════════════════════');

  if (!scanner || !isScanning) {
    logWarn('Scanner not running');
    return;
  }

  try {
    log('Stopping scanner...');
    await scanner.stop();
    
    log('Clearing scanner...');
    await scanner.clear();

    isScanning = false;
    log('✓ Scanner stopped');
    updateStatus('inactive');
    showToast('Camera stopped', 'info');

  } catch (err) {
    logError('Error stopping camera:', err);
  }
}

// ════════════════════════════════════════════════════════════════
// SWITCH CAMERA
// ════════════════════════════════════════════════════════════════

async function switchCamera() {
  log('═══════════════════════════════════════════');
  log('SWITCH CAMERA BUTTON CLICKED');
  log('═══════════════════════════════════════════');

  if (!scanner || availableCameras.length < 2) {
    logWarn('Not enough cameras or scanner not ready');
    return;
  }

  if (!isScanning) {
    logWarn('Scanner not running');
    return;
  }

  try {
    const currentIdx = availableCameras.findIndex(c => c.id === selectedCameraId);
    const nextIdx = (currentIdx + 1) % availableCameras.length;
    const nextCamera = availableCameras[nextIdx];

    log('Switching to camera:', nextCamera.label);
    
    // Stop current
    await scanner.stop();
    await scanner.clear();

    // Start with new camera
    selectedCameraId = nextCamera.id;
    await scanner.start(
      selectedCameraId,
      { fps: 10, qrbox: { width: 280, height: 280 } },
      onScanSuccess,
      onScanFailure
    );

    log('✓ Camera switched to:', nextCamera.label);
    showToast(`Switched to ${nextCamera.label}`, 'success');

  } catch (err) {
    logError('Error switching camera:', err);
    handleError('SWITCH_ERROR', `Switch failed: ${err.message}`);
  }
}

// ════════════════════════════════════════════════════════════════
// QR SCAN CALLBACKS
// ════════════════════════════════════════════════════════════════

function onScanSuccess(decodedText, decodedResult) {
  log('═══════════════════════════════════════════');
  log('QR CODE DETECTED:', decodedText);
  log('═══════════════════════════════════════════');

  handleQRCode(decodedText);
}

function onScanFailure(error) {
  // Silently ignore - happens frequently during scanning
  // Only log if it's not the expected "not found" error
  if (!error.includes('NotFound')) {
    logWarn('Scan error:', error);
  }
}

// ════════════════════════════════════════════════════════════════
// HANDLE QR CODE
// ════════════════════════════════════════════════════════════════

async function handleQRCode(qrContent) {
  try {
    // Stop scanning
    if (isScanning) {
      await stopCamera();
    }

    // Play beep
    playBeep();

    // Vibrate
    if (navigator.vibrate) {
      navigator.vibrate([100, 50, 100]);
    }

    // Show success animation
    showSuccessAnimation();

    // Add to history
    addToHistory(qrContent);

    // Update result panel
    updateResultPanel(qrContent);

    // Redirect after 1 second
    setTimeout(() => {
      redirectToQR(qrContent);
    }, 1000);

  } catch (err) {
    logError('Error handling QR code:', err);
  }
}

function redirectToQR(qrContent) {
  // Format: INV/RCPT-INV followed by numbers
  const invoiceRegex = /^(INV|RCPT-INV)(\d{12})$/i;
  const match = qrContent.match(invoiceRegex);

  if (match) {
    const url = `/rentals/scan/${qrContent}`;
    log('Redirecting to:', url);
    window.location.href = url;
  } else if (/^\d+$/.test(qrContent)) {
    // QR lama formatnya hanya ID rental numerik.
    // Requirement: jangan pernah menuju rentals.show (detail penyewaan).
    // Backend scanQr() akan mendukung format ini dan selalu mengembalikan scan-result.
    const url = `/rentals/scan/${qrContent}`;
    log('Redirecting to (scan id -> scan-result):', url);
    window.location.href = url;
  } else if (qrContent.startsWith('http')) {
    log('Redirecting to:', qrContent);
    window.location.href = qrContent;
  } else {
    logWarn('Unknown QR format:', qrContent);
    showToast('QR format not recognized', 'warning');
  }
}

// ════════════════════════════════════════════════════════════════
// UI HELPERS
// ════════════════════════════════════════════════════════════════

function updateStatus(state) {
  const statusEl = document.getElementById('camera-status');
  if (!statusEl) return;

  if (state === 'active') {
    statusEl.innerHTML = `
      <div class="flex items-center gap-2">
        <span class="inline-block w-3 h-3 rounded-full bg-green-500 animate-pulse"></span>
        <span class="text-sm text-green-600">Kamera Aktif</span>
      </div>
    `;
  } else {
    statusEl.innerHTML = `
      <div class="flex items-center gap-2">
        <span class="inline-block w-3 h-3 rounded-full bg-red-500"></span>
        <span class="text-sm text-red-600">Kamera Tidak Aktif</span>
      </div>
    `;
  }
}

function showLoading() {
  const loader = document.getElementById('camera-loader');
  if (loader) loader.classList.remove('hidden');
}

function hideLoading() {
  const loader = document.getElementById('camera-loader');
  if (loader) loader.classList.add('hidden');
}

function showSuccessAnimation() {
  const reader = document.getElementById('qr-reader');
  if (!reader) return;

  reader.classList.add('ring-2', 'ring-green-500', 'ring-offset-2');
  setTimeout(() => {
    reader.classList.remove('ring-2', 'ring-green-500', 'ring-offset-2');
  }, 500);
}

function playBeep() {
  try {
    const ctx = new (window.AudioContext || window.webkitAudioContext)();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();

    osc.connect(gain);
    gain.connect(ctx.destination);

    osc.frequency.value = 800;
    gain.gain.setValueAtTime(0.3, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.2);

    osc.start(ctx.currentTime);
    osc.stop(ctx.currentTime + 0.2);
  } catch (err) {
    logWarn('Could not play beep:', err);
  }
}

function showToast(msg, type = 'info') {
  const toast = document.createElement('div');
  toast.className = `fixed bottom-4 right-4 px-4 py-3 rounded text-white text-sm z-50`;
  
  if (type === 'error') toast.classList.add('bg-red-500');
  else if (type === 'success') toast.classList.add('bg-green-500');
  else if (type === 'warning') toast.classList.add('bg-yellow-500');
  else toast.classList.add('bg-blue-500');

  toast.textContent = msg;
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 3000);
}

function handleError(code, msg) {
  logError(`[${code}] ${msg}`);
  showToast(msg, 'error');
}

function updateResultPanel(qrContent) {
  const container = document.getElementById('scan-result-container');
  if (!container) return;

  container.innerHTML = `
    <div class="text-center py-4">
      <div class="text-green-600 text-lg font-semibold mb-2">✓ QR Terbaca</div>
      <div class="text-sm text-bark bg-cream-warm rounded p-3 break-all">
        ${qrContent}
      </div>
    </div>
  `;
}

function addToHistory(qrContent) {
  scanHistory.unshift({
    content: qrContent,
    timestamp: new Date().toLocaleString()
  });

  if (scanHistory.length > 10) {
    scanHistory.pop();
  }

  try {
    localStorage.setItem('qr_history', JSON.stringify(scanHistory));
  } catch (err) {
    logWarn('Could not save history:', err);
  }
}

// ════════════════════════════════════════════════════════════════
// PAGE INITIALIZATION
// ════════════════════════════════════════════════════════════════

async function initPage() {
  log('═══════════════════════════════════════════');
  log('QR SCANNER READY');
  log('═══════════════════════════════════════════');

  // Check if we're on the scan page
  const qrReader = document.getElementById('qr-reader');
  if (!qrReader) {
    log('SKIP: Not on scan page');
    return;
  }

  log('✓ Scanner container found');

  // Wait for library
  let waitCount = 0;
  while (!window.Html5Qrcode && waitCount < 150) {
    await new Promise(r => setTimeout(r, 100));
    waitCount++;
  }

  if (!window.Html5Qrcode) {
    logError('CRITICAL: Html5Qrcode library not loaded after 15 seconds');
    handleError('LIBRARY', 'QR library failed to load. Check CDN.');
    return;
  }

  log('✓ Html5Qrcode library detected');
  log('Library version:', window.Html5Qrcode.VERSION || 'unknown');

  // Check browser
  if (!checkBrowserSupport()) {
    handleError('BROWSER', 'Browser does not support camera access');
    return;
  }

  // Initialize scanner
  const initialized = await initializeScanner();
  if (!initialized) {
    handleError('INIT', 'Failed to initialize scanner');
    return;
  }

  log('✓ Scanner initialized and ready');
  log('════════════════════════════════════════════');
}

// Auto-initialize on page load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initPage);
} else {
  initPage();
}

// Cleanup on page unload
window.addEventListener('beforeunload', async () => {
  if (scanner && isScanning) {
    try {
      await scanner.stop();
      await scanner.clear();
    } catch (err) {
      console.error('Error cleaning up scanner:', err);
    }
  }
});

// Export public API
window.QRScanner = {
  start: startCamera,
  stop: stopCamera,
  switch: switchCamera,
  getScanner: () => scanner,
  getStatus: () => ({
    isScanning,
    scanner: scanner ? 'initialized' : 'not initialized',
    cameras: availableCameras.length
  })
};

log('════════════════════════════════════════════');
log('QR Scanner module loaded');
log('Public API available at window.QRScanner');
log('════════════════════════════════════════════');
