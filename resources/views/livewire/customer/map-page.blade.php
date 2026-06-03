<div id="map-wrapper" style="position:relative;width:100%;height:calc(100vh - 58px - 66px);">

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>

    {{-- Map container --}}
    <div id="leaflet-map" style="width:100%;height:100%;z-index:1;"></div>

    {{-- Search & Filter Bar --}}
    <div id="search-bar" style="position:absolute;top:12px;right:12px;left:12px;z-index:20;display:flex;flex-direction:column;gap:8px;">

        {{-- Search input --}}
        <div style="display:flex;align-items:center;gap:8px;background:rgba(10,15,30,0.92);border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:0 14px;backdrop-filter:blur(12px);box-shadow:0 4px 20px rgba(0,0,0,0.5);">
            <svg width="16" height="16" fill="none" stroke="#64748b" viewBox="0 0 24 24" style="flex-shrink:0;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
            </svg>
            <input id="station-search"
                   type="text"
                   placeholder="ابحث عن محطة..."
                   style="flex:1;background:transparent;border:none;outline:none;color:#f1f5f9;font-size:0.88rem;padding:12px 4px;font-family:'Tajawal',sans-serif;"
                   autocomplete="off"/>
            <button id="clear-search"
                    style="display:none;background:none;border:none;color:#64748b;cursor:pointer;padding:4px;line-height:1;font-size:1rem;"
                    title="مسح">✕</button>
        </div>

        {{-- Filter buttons --}}
        <div style="display:flex;gap:8px;">
            <button data-filter="all"
                    class="filter-btn active-filter"
                    style="flex:1;background:rgba(249,115,22,0.85);border:1.5px solid #f97316;color:#fff;border-radius:10px;padding:7px 0;font-size:0.78rem;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;backdrop-filter:blur(8px);transition:.2s;">
                الكل
            </button>
            <button data-filter="open"
                    class="filter-btn"
                    style="flex:1;background:rgba(10,15,30,0.88);border:1.5px solid rgba(34,197,94,0.4);color:#86efac;border-radius:10px;padding:7px 0;font-size:0.78rem;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;backdrop-filter:blur(8px);transition:.2s;">
                ● متوفر
            </button>
            <button data-filter="closed"
                    class="filter-btn"
                    style="flex:1;background:rgba(10,15,30,0.88);border:1.5px solid rgba(239,68,68,0.4);color:#fca5a5;border-radius:10px;padding:7px 0;font-size:0.78rem;font-weight:700;cursor:pointer;font-family:'Tajawal',sans-serif;backdrop-filter:blur(8px);transition:.2s;">
                ● غير متوفر
            </button>
        </div>

        {{-- Results count --}}
        <div id="results-count" style="display:none;background:rgba(10,15,30,0.85);border-radius:8px;padding:5px 12px;backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.07);">
            <span id="results-text" style="color:#94a3b8;font-size:0.76rem;font-weight:600;"></span>
        </div>
    </div>

    {{-- GPS denied banner --}}
    <div id="gps-banner" style="position:absolute;bottom:90px;right:12px;left:12px;z-index:10;display:none;">
        <div id="gps-denied-msg" style="display:none;background:rgba(239,68,68,0.92);backdrop-filter:blur(8px);border-radius:14px;padding:12px 16px;color:white;font-size:0.84rem;font-weight:600;text-align:center;box-shadow:0 4px 20px rgba(0,0,0,0.4);">
            📍 يرجى تفعيل GPS للاستفادة من ميزة الخريطة الكاملة
        </div>
    </div>

    {{-- Track Button --}}
    <button id="track-btn"
            style="position:absolute;bottom:{{ session('nearest_open') ? '230px' : '80px' }};left:16px;z-index:10;background:rgba(10,15,30,0.9);border:1.5px solid rgba(249,115,22,0.5);color:#f97316;border-radius:14px;padding:10px 16px;font-size:0.82rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;font-family:'Tajawal',sans-serif;box-shadow:0 4px 16px rgba(0,0,0,0.4);backdrop-filter:blur(8px);transition:.2s;">
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
        width:14px;height:14px;border-radius:50%;
        background:#22c55e;border:2px solid white;
        box-shadow:0 0 8px rgba(34,197,94,0.9);
        cursor:pointer;
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
    .filter-btn.active-filter { opacity:1 !important; }
    .filter-btn:not(.active-filter) { opacity:0.7; }
    #station-search::placeholder { color:#475569; }
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function() {
    const allStations = @json($stations);

    let map, userMarker, watchId, tracking = false;
    let markers = [];        // { marker, station } for each station
    let activeFilter = 'all';
    let searchQuery   = '';
    let userLat = null, userLng = null;

    // ── Init map ─────────────────────────────────────────
    function initMap() {
        map = L.map('leaflet-map', {
            center: [15.5527, 48.5164],
            zoom: 7,
            zoomControl: true,
            attributionControl: false,
        });

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 19,
        }).addTo(map);

        buildMarkers();
        applyFilters();

        // GPS
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(onGpsSuccess, onGpsDenied, { timeout: 8000 });
        } else {
            showDenied();
        }

        // Buttons & inputs
        document.getElementById('track-btn').addEventListener('click', toggleTracking);
        document.getElementById('station-search').addEventListener('input', onSearch);
        document.getElementById('clear-search').addEventListener('click', clearSearch);
        document.querySelectorAll('.filter-btn').forEach(function(btn) {
            btn.addEventListener('click', function() { setFilter(btn.dataset.filter); });
        });
    }

    // ── Build all markers (hidden until applyFilters) ────
    function buildMarkers() {
        allStations.forEach(function(st) {
            if (!st.latitude || !st.longitude) return;

            var isOpen  = !!st.is_open;
            var dotHtml = '<div class="custom-station-marker"><div class="station-dot' + (isOpen ? '' : ' closed') + '"></div></div>';
            var icon    = L.divIcon({ html: dotHtml, className: '', iconSize: [14, 14], iconAnchor: [7, 7] });
            var marker  = L.marker([parseFloat(st.latitude), parseFloat(st.longitude)], { icon: icon });

            var popup = '<div style="font-family:Tajawal,sans-serif;min-width:160px;">'
                + '<p style="font-weight:800;font-size:0.92rem;margin:0 0 4px;color:#f8fafc;">' + st.station_name + '</p>'
                + (st.location ? '<p style="font-size:0.75rem;margin:0 0 6px;color:#94a3b8;">' + st.location + '</p>' : '')
                + '<div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">'
                + '<span style="font-size:0.72rem;padding:3px 10px;border-radius:999px;font-weight:700;background:'
                + (isOpen ? 'rgba(34,197,94,0.2)' : 'rgba(239,68,68,0.2)') + ';color:'
                + (isOpen ? '#86efac' : '#fca5a5') + ';">'
                + (isOpen ? '● متوفر' : '● غير متوفر') + '</span>'
                + (st.rating ? '<span style="color:#fbbf24;font-size:0.78rem;">★ ' + parseFloat(st.rating).toFixed(1) + '</span>' : '')
                + '</div>'
                + '</div>';
            marker.bindPopup(popup);
            marker._stationData = st;
            markers.push({ marker: marker, station: st });
        });
    }

    // ── Apply search + filter to markers ────────────────
    function applyFilters() {
        var q = searchQuery.trim().toLowerCase();
        var shown = 0;

        markers.forEach(function(m) {
            var st     = m.station;
            var isOpen = !!st.is_open;

            // Availability filter
            var passFilter = activeFilter === 'all'
                || (activeFilter === 'open'   &&  isOpen)
                || (activeFilter === 'closed' && !isOpen);

            // Search filter
            var passSearch = !q || st.station_name.toLowerCase().includes(q)
                || (st.location && st.location.toLowerCase().includes(q));

            if (passFilter && passSearch) {
                if (!map.hasLayer(m.marker)) m.marker.addTo(map);
                shown++;
            } else {
                if (map.hasLayer(m.marker)) map.removeLayer(m.marker);
            }
        });

        // Update results count
        var countEl = document.getElementById('results-count');
        var textEl  = document.getElementById('results-text');
        if (q || activeFilter !== 'all') {
            countEl.style.display = 'block';
            textEl.textContent    = 'يظهر ' + shown + ' من أصل ' + markers.length + ' محطة';
        } else {
            countEl.style.display = 'none';
        }

        // If search matches 1 station, fly to it
        if (shown === 1 && q) {
            var found = markers.find(function(m) {
                return map.hasLayer(m.marker);
            });
            if (found) {
                map.flyTo([parseFloat(found.station.latitude), parseFloat(found.station.longitude)], 15, { duration: 1 });
                found.marker.openPopup();
            }
        }
    }

    // ── Search handler ────────────────────────────────────
    function onSearch(e) {
        searchQuery = e.target.value;
        document.getElementById('clear-search').style.display = searchQuery ? 'block' : 'none';
        applyFilters();
    }

    function clearSearch() {
        searchQuery = '';
        document.getElementById('station-search').value = '';
        document.getElementById('clear-search').style.display = 'none';
        applyFilters();
    }

    // ── Filter button handler ─────────────────────────────
    function setFilter(f) {
        activeFilter = f;
        document.querySelectorAll('.filter-btn').forEach(function(btn) {
            var isActive = btn.dataset.filter === f;
            btn.classList.toggle('active-filter', isActive);

            if (f === 'all' && isActive) {
                btn.style.background   = 'rgba(249,115,22,0.85)';
                btn.style.borderColor  = '#f97316';
                btn.style.color        = '#fff';
            } else if (f === 'open' && isActive) {
                btn.style.background   = 'rgba(34,197,94,0.25)';
                btn.style.borderColor  = '#22c55e';
                btn.style.color        = '#86efac';
            } else if (f === 'closed' && isActive) {
                btn.style.background   = 'rgba(239,68,68,0.25)';
                btn.style.borderColor  = '#ef4444';
                btn.style.color        = '#fca5a5';
            } else {
                // inactive
                btn.style.background   = 'rgba(10,15,30,0.88)';
                if (btn.dataset.filter === 'all') {
                    btn.style.borderColor = 'rgba(249,115,22,0.3)';
                    btn.style.color       = '#f97316';
                } else if (btn.dataset.filter === 'open') {
                    btn.style.borderColor = 'rgba(34,197,94,0.3)';
                    btn.style.color       = '#86efac';
                } else {
                    btn.style.borderColor = 'rgba(239,68,68,0.3)';
                    btn.style.color       = '#fca5a5';
                }
            }
        });
        applyFilters();
    }

    // ── GPS ───────────────────────────────────────────────
    function onGpsSuccess(pos) {
        userLat = pos.coords.latitude;
        userLng = pos.coords.longitude;
        placeUserDot(userLat, userLng);
        map.flyTo([userLat, userLng], 13, { duration: 1.5 });
        showNearestStations(userLat, userLng);
    }

    function onGpsDenied() { showDenied(); }

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
            btn.style.background  = 'rgba(249,115,22,0.2)';
            btn.style.borderColor = '#f97316';
            label.textContent     = 'إيقاف التتبع';

            if (navigator.geolocation) {
                watchId = navigator.geolocation.watchPosition(function(pos) {
                    userLat = pos.coords.latitude;
                    userLng = pos.coords.longitude;
                    placeUserDot(userLat, userLng);
                    map.setView([userLat, userLng], map.getZoom());
                }, onGpsDenied, { enableHighAccuracy: true });
            }
        } else {
            btn.style.background  = 'rgba(10,15,30,0.9)';
            btn.style.borderColor = 'rgba(249,115,22,0.5)';
            label.textContent     = 'تتبع موقعي';
            if (watchId) { navigator.geolocation.clearWatch(watchId); watchId = null; }
        }
    }

    // ── Nearest 4 stations panel ──────────────────────────
    function showNearestStations(lat, lng) {
        var withDist = allStations
            .filter(function(st) { return st.latitude && st.longitude; })
            .map(function(st) {
                var d = haversine(lat, lng, parseFloat(st.latitude), parseFloat(st.longitude));
                return Object.assign({}, st, { dist: d });
            })
            .sort(function(a, b) { return a.dist - b.dist; })
            .slice(0, 4);

        if (!withDist.length) return;

        var list = document.getElementById('nearest-list');
        list.innerHTML = '';

        withDist.forEach(function(st) {
            var km   = st.dist < 1 ? Math.round(st.dist * 1000) + ' م' : st.dist.toFixed(1) + ' كم';
            var el   = document.createElement('div');
            el.style.cssText = 'display:flex;align-items:center;justify-content:space-between;background:rgba(255,255,255,0.04);border-radius:10px;padding:10px 14px;cursor:pointer;gap:8px;';
            el.innerHTML = '<div style="display:flex;align-items:center;gap:10px;min-width:0;">'
                + '<div style="width:9px;height:9px;border-radius:50%;flex-shrink:0;background:' + (st.is_open ? '#22c55e' : '#ef4444') + ';"></div>'
                + '<div style="min-width:0;">'
                + '<p style="color:#f8fafc;font-size:0.84rem;font-weight:600;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + st.station_name + '</p>'
                + '<p style="color:#64748b;font-size:0.7rem;margin:2px 0 0;">' + (st.is_open ? 'متوفر' : 'غير متوفر') + '</p>'
                + '</div>'
                + '</div>'
                + '<span style="color:#f97316;font-size:0.78rem;font-weight:700;white-space:nowrap;">' + km + '</span>';
            el.addEventListener('click', function() {
                map.flyTo([parseFloat(st.latitude), parseFloat(st.longitude)], 15, { duration: 1 });
            });
            list.appendChild(el);
        });

        document.getElementById('nearest-panel').style.display = 'block';
        document.getElementById('track-btn').style.bottom      = '230px';
    }

    // ── Haversine ─────────────────────────────────────────
    function haversine(lat1, lon1, lat2, lon2) {
        var R = 6371, dLat = deg2rad(lat2 - lat1), dLon = deg2rad(lon2 - lon1);
        var a = Math.sin(dLat/2)*Math.sin(dLat/2)
              + Math.cos(deg2rad(lat1))*Math.cos(deg2rad(lat2))*Math.sin(dLon/2)*Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }
    function deg2rad(d) { return d * (Math.PI/180); }

    // Init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMap);
    } else {
        initMap();
    }
})();
</script>
