// Workshop Nearby Map Page Script
document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('map');
    if (!mapElement) return;

    // Default coordinates (Jakarta)
    let userLat = -6.2088;
    let userLng = 106.8456;
    let map = null;
    let userMarker = null;
    let markersLayer = L.layerGroup();
    
    // UI Elements
    const radiusInput = document.getElementById('filter-radius');
    const ratingInput = document.getElementById('filter-rating');
    const serviceTypeInput = document.getElementById('filter-service-type');
    const workshopList = document.getElementById('workshop-list');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');

    // Initialize Map
    function initMap(lat, lng, zoom = 13) {
        map = L.map('map', {
            zoomControl: false
        }).setView([lat, lng], zoom);

        // Dark theme tiles (CartoDB Dark Matter)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);

        markersLayer.addTo(map);

        // Place User Marker
        const userIcon = L.divIcon({
            html: '<div class="w-4 h-4 bg-blue-500 border-2 border-white rounded-full shadow-lg pulse"></div>',
            className: 'custom-user-icon',
            iconSize: [16, 16],
            iconAnchor: [8, 8]
        });

        userMarker = L.marker([lat, lng], { icon: userIcon })
            .addTo(map)
            .bindPopup('Lokasi Anda')
            .openPopup();
    }

    // Request Location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function (position) {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;
                initMap(userLat, userLng);
                fetchWorkshops();
            },
            function (error) {
                console.warn('Geolocation permission denied or error. Using default coordinates.', error);
                initMap(userLat, userLng);
                fetchWorkshops();
            }
        );
    } else {
        initMap(userLat, userLng);
        fetchWorkshops();
    }

    // Fetch Workshops
    window.fetchWorkshops = function () {
        if (!map) return;

        // Show loading state
        loadingState.classList.remove('hidden');
        workshopList.classList.add('hidden');
        emptyState.classList.add('hidden');

        // Clear existing markers
        markersLayer.clearLayers();

        const radius = radiusInput.value;
        const rating = ratingInput.value;
        const serviceType = serviceTypeInput.value;

        // Build Query URL
        let url = `/api/workshops/nearby?latitude=${userLat}&longitude=${userLng}&radius=${radius}`;
        if (rating) url += `&rating=${rating}`;
        if (serviceType) url += `&service_type=${serviceType}`;

        fetch(url)
            .then(response => response.json())
            .then(res => {
                loadingState.classList.add('hidden');
                
                if (res.success && res.data && res.data.length > 0) {
                    workshopList.classList.remove('hidden');
                    renderWorkshopList(res.data);
                    renderWorkshopMarkers(res.data);
                } else {
                    emptyState.classList.remove('hidden');
                }
            })
            .catch(err => {
                console.error('Error fetching workshops:', err);
                loadingState.classList.add('hidden');
                emptyState.classList.remove('hidden');
            });
    };

    // Render List Cards
    function renderWorkshopList(workshops) {
        workshopList.innerHTML = '';

        workshops.forEach(workshop => {
            const distanceText = workshop.distance ? `${workshop.distance.toFixed(1)} km` : '-';
            const ratingStars = '★'.repeat(Math.round(workshop.rating_average)) + '☆'.repeat(5 - Math.round(workshop.rating_average));
            
            const card = document.createElement('div');
            card.className = 'bg-zinc-900 border border-zinc-800 rounded-xl p-4 hover:border-red-500 transition-all cursor-pointer workshop-card';
            card.dataset.id = workshop.id;
            card.dataset.lat = workshop.latitude;
            card.dataset.lng = workshop.longitude;

            card.innerHTML = `
                <div class="flex justify-between items-start gap-2 mb-2">
                    <div>
                        <h3 class="font-bold text-zinc-100 text-sm hover:text-red-400 transition-colors">${workshop.name}</h3>
                        <p class="text-xs text-zinc-500 mt-0.5">${workshop.address || ''}</p>
                    </div>
                    ${workshop.status === 'approved' ? `
                        <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-red-950/40 border border-red-900/50 text-red-400 uppercase tracking-wider">
                            Verified Partner
                        </span>
                    ` : ''}
                </div>

                <div class="flex items-center gap-3 text-xs text-zinc-400 mb-3">
                    <span class="flex items-center gap-1">
                        <span class="text-amber-500 font-bold">${workshop.rating_average.toFixed(1)}</span>
                        <span class="text-amber-500/80">${ratingStars}</span>
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="font-semibold text-zinc-300">${distanceText}</span>
                </div>

                <div class="flex items-center justify-between gap-3 border-t border-zinc-800 pt-3 mt-3">
                    <button type="button" 
                            onclick="focusWorkshop(${workshop.id}, ${workshop.latitude}, ${workshop.longitude})"
                            class="text-xs font-semibold text-zinc-400 hover:text-zinc-100 transition-colors">
                        Lihat di Peta
                    </button>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${workshop.latitude},${workshop.longitude}" 
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Petunjuk Arah
                    </a>
                </div>
            `;

            // Click card to pan map
            card.addEventListener('click', function(e) {
                if (e.target.tagName !== 'A' && e.target.tagName !== 'BUTTON') {
                    focusWorkshop(workshop.id, workshop.latitude, workshop.longitude);
                }
            });

            workshopList.appendChild(card);
        });
    }

    // Render Markers
    function renderWorkshopMarkers(workshops) {
        workshops.forEach(workshop => {
            const ratingStars = '★'.repeat(Math.round(workshop.rating_average)) + '☆'.repeat(5 - Math.round(workshop.rating_average));
            const distanceText = workshop.distance ? `${workshop.distance.toFixed(1)} km` : '';

            const popupContent = `
                <div class="text-zinc-100 p-1">
                    <h4 class="font-bold text-sm text-zinc-900">${workshop.name}</h4>
                    <p class="text-xs text-zinc-650 mt-1">${workshop.address || ''}</p>
                    <div class="flex items-center gap-2 mt-2 text-xs">
                        <span class="text-amber-600 font-bold">${workshop.rating_average.toFixed(1)} ${ratingStars}</span>
                        <span class="text-zinc-500 font-semibold">${distanceText}</span>
                    </div>
                </div>
            `;

            const pinIcon = L.divIcon({
                html: '<div class="w-6 h-6 bg-red-600 border-2 border-white rounded-full shadow-lg flex items-center justify-center text-white font-bold text-[10px] hover:scale-115 transition-transform"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>',
                className: 'custom-pin-icon',
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            const marker = L.marker([workshop.latitude, workshop.longitude], { icon: pinIcon })
                .bindPopup(popupContent)
                .addTo(markersLayer);

            // Bind marker click event
            marker.on('click', function() {
                const card = document.querySelector(`.workshop-card[data-id="${workshop.id}"]`);
                if (card) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    // Add transient highlight effect
                    card.classList.add('border-red-500', 'bg-zinc-850');
                    setTimeout(() => {
                        card.classList.remove('bg-zinc-850');
                    }, 1000);
                }
            });
        });
    }

    // Focus Workshop
    window.focusWorkshop = function(id, lat, lng) {
        if (!map) return;
        map.panTo([lat, lng]);
        
        // Find marker and open popup
        markersLayer.eachLayer(layer => {
            const latLng = layer.getLatLng();
            if (latLng.lat === lat && latLng.lng === lng) {
                layer.openPopup();
            }
        });
    };

    // Filters Change Listeners
    radiusInput.addEventListener('change', fetchWorkshops);
    ratingInput.addEventListener('change', fetchWorkshops);
    serviceTypeInput.addEventListener('change', fetchWorkshops);
});
