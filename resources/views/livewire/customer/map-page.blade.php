<div id="map-wrapper" style="position:relative;width:100%;height:calc(100vh - 58px - 66px);">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    {{-- Map container --}}
    <div id="leaflet-map" style="width:100%;height:100%;z-index:1;"></div>

    {{-- GPS Status Banner --}}
    <div id="gps-banner" style="position:absolute;top:12px;right:12px;left:12px;z-index:10;display:none;">
        <div id="gps-denied-msg" style="display:none;background:rgba(239,68,68,0.92);backdrop-filter:blur(8px);border-radius:14px;padding:12px 16px;color:white;font-size:0.84rem;font-weight:600;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.4);">
            📍 يرجى تفعيل GPS للاستفادة من ميزة الخريطة الكاملة
        </div>
    </div>

    {{-- Track Button --}}
    <button id="track-btn"
            style="position:absolute;bottom:80px;left:16px;z-index:10;background:rgba(10,15,30,0.9);border:1.5px solid rgba(249,115,22,0.5);color:#f97316;border-radius:14px;padding:10px 16px;font-size:0.82rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;font-family:'Tajawal',sans-serif;box-shadow:0 4px 16px rgba(0,0,0,0.4);backdrop-filter:blur(8px);transition:.2s;">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span id="track-label">تتبع موقعي</span>
    </button>

    {{-- Nearest Stations Panel --}}
    <div id="nearest-panel" style="position:absolute;bottom:0;right:0;left:0;z-index:10;background:rgba(10,15,30,0.95);backdrop-filter:blur(16px);border-top:1px solid rgba(255,255,255,0.08);padding:14px 16px 16px;max-height:220px;overflow-y:auto;display:none;">
        <p style="color:#94a3b8;font-size:0.74rem;font-weight:700;margin:0 0 10px;letter-spacing:.05em;">أقرب 4 محطات</p>
        <div id="nearest-list" style="display:flex;flex-direction:column;gap:8px;"></div>
    </div>

</div>

<style>
    .leaflet-container { background:#0f172a !important; font-family:'Tajawal',sans-serif; }
    .custom-station-marker { display:flex;align-items:center;justify-content:center; }
    .station-dot {
        width:12px;height:12px;border-radius:50%;
        background:#f97316;border:2px solid white;
        box-shadow:0 0 8px rgba(249,115,22,0.8);
    }
    .station-dot.closed { background:#ef4444;box-shadow:0 0 8px rgba(239,68,68,0.8); }
    .user-dot-wrapper { position:relative; }
    .user-dot {
        width:16px;height:16px;border-radius:50%;
        background:#3b82f6;border:2px solid white;
        box-shadow:0 0 10px rgba(59,130,246,0.9);
    }
    .user-pulse {
        position:absolute;inset:-8px;border-radius:50%;
        background:rgba(59,130,246,0.3);
        animation:pulse-ring 1.8s ease-out infinite;
    }
    @keyframes pulse-ring {
        0%   { transform:scale(.6); opacity:.8; }
        100% { transform:scale(1.8); opacity:0; }
    }
    .leaflet-popup-content-wrapper {
        background:#1e293b !important; color:#f8fafc !important;
        border:1px solid rgba(255,255,255,0.1) !important;
        border-radius:12px !important; box-shadow:0 8px 32px rgba(0,0,0,0.5) !important;
    }
    .leaflet-popup-tip { background:#1e293b !important; }
    .leaflet-popup-content { margin:12px 16px !important; font-family:'Tajawal',sans-serif; }
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    const stations = @json($stations);
    let map, userMarker, watchId, tracking = false;

    function initMap() {
        map = L.map('leaflet-map', {
            center: [15.5527, 48.5164],
            zoom: 7,
            zoomControl: true,
            attributionControl: false,
        });

        // Dark tile layer
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
        }).addTo(map);

        // Add station markers
        stations.forEach(function(st) {
            if (!st.latitude || !st.longitude) return;

            var isOpen  = st.is_open;
            var dotHtml = '<div class="custom-station-marker"><div class="station-dot' + (isOpen ? '' : ' closed') + '"></div></div>';
            var icon    = L.divIcon({ html: dotHtml, className: '', iconSize: [12, 12], iconAnchor: [6, 6] });

            var marker = L.marker([st.latitude, st.longitude], { icon: icon }).addTo(map);
            var popup  = '<div style="font-family:Tajawal,sans-serif;">'
                       + '<p style="font-weight:800;font-size:0.92rem;margin:0 0 4px;color:#f8fafc;">' + st.station_name + '</p>'
                       + '<p style="font-size:0.75rem;margin:0 0 4px;color:#94a3b8;">' + (st.location || '') + '</p>'
                       + '<span style="font-size:0.72rem;padding:2px 8px;border-radius:999px;background:' + (isOpen ? 'rgba(34,197,94,0.2)' : 'rgba(239,68,68,0.2)') + ';color:' + (isOpen ? '#86efac' : '#fca5a5') + ';">'
                       + (isOpen ? 'مفتوحة' : 'مغلقة') + '</span>'
                       + (st.rating ? '<span style="color:#fbbf24;font-size:0.78rem;margin-right:8px;">★ ' + st.rating + '</span>' : '')
                       + '</div>';
            marker.bindPopup(popup);
            marker._stationData = st;
        });

        // Request GPS
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(onGpsSuccess, onGpsDenied, { timeout: 8000 });
        } else {
            showDenied();
        }

        // Track button
        document.getElementById('track-btn').addEventListener('click', toggleTracking);
    }

    function onGpsSuccess(pos) {
        var lat = pos.coords.latitude, lng = pos.coords.longitude;
        placeUserDot(lat, lng);
        map.flyTo([lat, lng], 13, { duration: 1.5 });
        showNearestStations(lat, lng);
    }

    function onGpsDenied() {
        showDenied();
    }

    function showDenied() {
        var banner = document.getElementById('gps-banner');
        var msg    = document.getElementById('gps-denied-msg');
        banner.style.display = 'block';
        msg.style.display    = 'block';
    }

    function placeUserDot(lat, lng) {
        if (userMarker) { map.removeLayer(userMarker); }
        var html = '<div class="user-dot-wrapper"><div class="user-pulse"></div><div class="user-dot"></div></div>';
        var icon = L.divIcon({ html: html, className: '', iconSize: [16, 16], iconAnchor: [8, 8] });
        userMarker = L.marker([lat, lng], { icon: icon, zIndexOffset: 1000 }).addTo(map);
        userMarker.bindPopup('<span style="font-family:Tajawal,sans-serif;color:#f8fafc;font-size:0.85rem;">موقعك الحالي</span>');
    }

    function toggleTracking() {
        tracking = !tracking;
        var btn   = document.getElementById('track-btn');
        var label = document.getElementById('track-label');

        if (tracking) {
            btn.style.background   = 'rgba(249,115,22,0.2)';
            btn.style.borderColor  = '#f97316';
            label.textContent      = 'إيقاف التتبع';

            if (navigator.geolocation) {
                watchId = navigator.geolocation.watchPosition(function(pos) {
                    var lat = pos.coords.latitude, lng = pos.coords.longitude;
                    placeUserDot(lat, lng);
                    map.setView([lat, lng], map.getZoom());
                }, onGpsDenied, { enableHighAccuracy: true });
            }
        } else {
            btn.style.background  = 'rgba(10,15,30,0.9)';
            btn.style.borderColor = 'rgba(249,115,22,0.5)';
            label.textContent     = 'تتبع موقعي';
            if (watchId) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        }
    }

    function showNearestStations(userLat, userLng) {
        var withDist = stations
            .filter(function(st) { return st.latitude && st.longitude; })
            .map(function(st) {
                var d = haversine(userLat, userLng, parseFloat(st.latitude), parseFloat(st.longitude));
                return Object.assign({}, st, { dist: d });
            })
            .sort(function(a, b) { return a.dist - b.dist })
            .slice(0, 4);

        if (!withDist.length) return;

        var list = document.getElementById('nearest-list');
        list.innerHTML = '';

        withDist.forEach(function(st) {
            var km    = st.dist < 1 ? Math.round(st.dist * 1000) + ' م' : st.dist.toFixed(1) + ' كم';
            var el    = document.createElement('div');
            el.style.cssText = 'display:flex;align-items:center;justify-content:space-between;background:rgba(255,255,255,0.04);border-radius:10px;padding:10px 14px;cursor:pointer;';
            el.innerHTML     = '<div style="display:flex;align-items:center;gap:10px;">'
                + '<div style="width:8px;height:8px;border-radius:50%;background:' + (st.is_open ? '#22c55e' : '#ef4444') + ';flex-shrink:0;"></div>'
                + '<span style="color:#f8fafc;font-size:0.84rem;font-weight:600;">' + st.station_name + '</span>'
                + '</div>'
                + '<span style="color:#f97316;font-size:0.78rem;font-weight:700;">' + km + '</span>';
            el.addEventListener('click', function() {
                map.flyTo([st.latitude, st.longitude], 15, { duration: 1 });
            });
            list.appendChild(el);
        });

        document.getElementById('nearest-panel').style.display = 'block';
    }

    function haversine(lat1, lon1, lat2, lon2) {
        var R = 6371, dLat = deg2rad(lat2 - lat1), dLon = deg2rad(lon2 - lon1);
        var a = Math.sin(dLat/2)*Math.sin(dLat/2)
              + Math.cos(deg2rad(lat1))*Math.cos(deg2rad(lat2))*Math.sin(dLon/2)*Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
    function deg2rad(d) { return d * (Math.PI/180); }

    // Init after DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }
})();
</script>
