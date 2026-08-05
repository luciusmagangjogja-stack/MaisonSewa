import './bootstrap';
import './notifications.js'
// Import scanner module (library is loaded via CDN in blade templates)
import './scanner.js'
import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()