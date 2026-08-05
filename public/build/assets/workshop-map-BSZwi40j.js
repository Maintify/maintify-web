document.addEventListener("DOMContentLoaded",function(){if(!document.getElementById("map"))return;let o=-6.2088,r=106.8456,i=null,d=L.layerGroup();const h=document.getElementById("filter-radius"),f=document.getElementById("filter-rating"),v=document.getElementById("filter-service-type"),c=document.getElementById("workshop-list"),m=document.getElementById("loading-state"),p=document.getElementById("empty-state");function g(n,e,s=13){i=L.map("map",{zoomControl:!1}).setView([n,e],s),L.tileLayer("https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png",{attribution:'&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',subdomains:"abcd",maxZoom:20}).addTo(i),L.control.zoom({position:"bottomright"}).addTo(i),d.addTo(i);const a=L.divIcon({html:'<div class="w-4 h-4 bg-blue-500 border-2 border-white rounded-full shadow-lg pulse"></div>',className:"custom-user-icon",iconSize:[16,16],iconAnchor:[8,8]});L.marker([n,e],{icon:a}).addTo(i).bindPopup("Lokasi Anda").openPopup()}navigator.geolocation?navigator.geolocation.getCurrentPosition(function(n){o=n.coords.latitude,r=n.coords.longitude,g(o,r),fetchWorkshops()},function(n){console.warn("Geolocation permission denied or error. Using default coordinates.",n),g(o,r),fetchWorkshops()}):(g(o,r),fetchWorkshops()),window.fetchWorkshops=function(){if(!i)return;m.classList.remove("hidden"),c.classList.add("hidden"),p.classList.add("hidden"),d.clearLayers();const n=h.value,e=f.value,s=v.value;let a=`/api/workshops/nearby?latitude=${o}&longitude=${r}&radius=${n}`;e&&(a+=`&rating=${e}`),s&&(a+=`&service_type=${s}`),fetch(a).then(t=>t.json()).then(t=>{m.classList.add("hidden"),t.success&&t.data&&t.data.length>0?(c.classList.remove("hidden"),x(t.data),b(t.data)):p.classList.remove("hidden")}).catch(t=>{console.error("Error fetching workshops:",t),m.classList.add("hidden"),p.classList.remove("hidden")})};function x(n){c.innerHTML="",n.forEach(e=>{const s=e.distance?`${e.distance.toFixed(1)} km`:"-",a="★".repeat(Math.round(e.rating_average))+"☆".repeat(5-Math.round(e.rating_average)),t=document.createElement("div");t.className="bg-zinc-900 border border-zinc-800 rounded-xl p-4 hover:border-red-500 transition-all cursor-pointer workshop-card",t.dataset.id=e.id,t.dataset.lat=e.latitude,t.dataset.lng=e.longitude,t.innerHTML=`
                <div class="flex justify-between items-start gap-2 mb-2">
                    <div>
                        <h3 class="font-bold text-zinc-100 text-sm hover:text-red-400 transition-colors">${e.name}</h3>
                        <p class="text-xs text-zinc-500 mt-0.5">${e.address||""}</p>
                    </div>
                    ${e.status==="approved"?`
                        <span class="flex-shrink-0 inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold bg-red-950/40 border border-red-900/50 text-red-400 uppercase tracking-wider">
                            Verified Partner
                        </span>
                    `:""}
                </div>

                <div class="flex items-center gap-3 text-xs text-zinc-400 mb-3">
                    <span class="flex items-center gap-1">
                        <span class="text-amber-500 font-bold">${e.rating_average.toFixed(1)}</span>
                        <span class="text-amber-500/80">${a}</span>
                    </span>
                    <span class="text-zinc-600">•</span>
                    <span class="font-semibold text-zinc-300">${s}</span>
                </div>

                <div class="flex items-center justify-between gap-3 border-t border-zinc-800 pt-3 mt-3">
                    <button type="button" 
                            onclick="focusWorkshop(${e.id}, ${e.latitude}, ${e.longitude})"
                            class="text-xs font-semibold text-zinc-400 hover:text-zinc-100 transition-colors">
                        Lihat di Peta
                    </button>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=${e.latitude},${e.longitude}" 
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold rounded-lg transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Petunjuk Arah
                    </a>
                </div>
            `,t.addEventListener("click",function(l){l.target.tagName!=="A"&&l.target.tagName!=="BUTTON"&&focusWorkshop(e.id,e.latitude,e.longitude)}),c.appendChild(t)})}function b(n){n.forEach(e=>{const s="★".repeat(Math.round(e.rating_average))+"☆".repeat(5-Math.round(e.rating_average)),a=e.distance?`${e.distance.toFixed(1)} km`:"",t=`
                <div class="text-zinc-100 p-1">
                    <h4 class="font-bold text-sm text-zinc-900">${e.name}</h4>
                    <p class="text-xs text-zinc-650 mt-1">${e.address||""}</p>
                    <div class="flex items-center gap-2 mt-2 text-xs">
                        <span class="text-amber-600 font-bold">${e.rating_average.toFixed(1)} ${s}</span>
                        <span class="text-zinc-500 font-semibold">${a}</span>
                    </div>
                </div>
            `,l=L.divIcon({html:'<div class="w-6 h-6 bg-red-600 border-2 border-white rounded-full shadow-lg flex items-center justify-center text-white font-bold text-[10px] hover:scale-115 transition-transform"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg></div>',className:"custom-pin-icon",iconSize:[24,24],iconAnchor:[12,12]});L.marker([e.latitude,e.longitude],{icon:l}).bindPopup(t).addTo(d).on("click",function(){const u=document.querySelector(`.workshop-card[data-id="${e.id}"]`);u&&(u.scrollIntoView({behavior:"smooth",block:"nearest"}),u.classList.add("border-red-500","bg-zinc-850"),setTimeout(()=>{u.classList.remove("bg-zinc-850")},1e3))})})}window.focusWorkshop=function(n,e,s){i&&(i.panTo([e,s]),d.eachLayer(a=>{const t=a.getLatLng();t.lat===e&&t.lng===s&&a.openPopup()}))},h.addEventListener("change",fetchWorkshops),f.addEventListener("change",fetchWorkshops),v.addEventListener("change",fetchWorkshops)});
