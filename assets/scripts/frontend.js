/**
 * PWA — easy way to Progressive Web App - v1.4.1
 * http://iworks.pl/en/plugins/iworks-pwa/
 * Copyright (c) 2022; * Licensed GPLv2+ */
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register(window.iworks_pwa.serviceWorkerUri)
        .then(function(reg) {})
        .catch(function(err) {});
}