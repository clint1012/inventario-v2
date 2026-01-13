/**
 * Configuración global de la aplicación
 * Inventario OTI - Tribunal Constitucional
 */

const APP = {
    BASE_URL: '',
    
    init: function(baseUrl) {
        this.BASE_URL = baseUrl;
        console.log('✓ APP inicializado');
    },

    url: function(path) {
        return this.BASE_URL + '/' + path.replace(/^\//, '');
    }
};

// Configuración jQuery global
$(document).ready(function() {
    // Desactivar cache de AJAX
    $.ajaxSetup({
        cache: false
    });
});
