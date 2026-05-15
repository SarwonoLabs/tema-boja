/**
 * TEMA BOJA - Helper Functions
 */

/**
 * Capitalize first character of each word
 */
function capitalizeFirstCharacterOfEachWord(str) {
    if (!str) return '';
    return str.replace(/\w\S*/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

/**
 * Truncate text to specified length
 */
function truncateText(text, maxLength) {
    if (!text) return '';
    maxLength = maxLength || 100;
    if (text.length <= maxLength) return text;
    return text.substr(0, maxLength) + '...';
}

/**
 * Convert string to underscore/snake_case
 */
function underscore(str) {
    if (!str) return '';
    return str.replace(/([A-Z])/g, '_$1')
              .replace(/[-\s]+/g, '_')
              .toLowerCase()
              .replace(/^_/, '');
}

/**
 * Format number with thousand separator (Indonesian)
 */
function formatRibuan(num) {
    if (num === null || num === undefined) return '0';
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

/**
 * Debounce function
 */
function debounce(func, wait) {
    var timeout;
    return function() {
        var context = this;
        var args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            func.apply(context, args);
        }, wait || 300);
    };
}

/**
 * Convert relative URL to absolute
 */
function absoluteUrl(path) {
    if (!path) return '';
    if (path.indexOf('http') === 0) return path;
    var base = typeof BASE_URL !== 'undefined' ? BASE_URL : '/';
    return base + path.replace(/^\/+/, '');
}

/**
 * Show SweetAlert notification
 */
function showNotif(type, message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: type || 'info',
            title: message,
            showConfirmButton: false,
            timer: 2500
        });
    } else {
        alert(message);
    }
}
