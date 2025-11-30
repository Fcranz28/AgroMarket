let map;
let marker;
let autocomplete;
let selectedLocation = null;

const initAddressMap = () => {
   console.log('Initializing Address Map script...');
   try {
      loadAddresses();
   } catch (e) {
      console.error('Error calling loadAddresses:', e);
   }

   const btnAdd = document.getElementById('btn-add-address');
   if (btnAdd) {
      btnAdd.addEventListener('click', function () {
         document.getElementById('map-container').style.display = 'block';
         btnAdd.style.display = 'none';

         // Initialize map if not already done (or resize trigger)
         if (!map) {
            // Wait for API to load if it hasn't yet (callback handles initMap)
            if (typeof google !== 'undefined' && typeof initMap === 'function') {
               try {
                  initMap();
               } catch (e) {
                  console.error('Error initializing map:', e);
               }
            }
         }
      });
   }

   const btnCancel = document.getElementById('btn-cancel-map');
   if (btnCancel) {
      btnCancel.addEventListener('click', function () {
         document.getElementById('map-container').style.display = 'none';
         document.getElementById('btn-add-address').style.display = 'block';
      });
   }

   const btnConfirm = document.getElementById('btn-confirm-address');
   if (btnConfirm) {
      btnConfirm.addEventListener('click', saveAddress);
   }
};

// Try to run on DOMContentLoaded
if (document.readyState === 'loading') {
   document.addEventListener('DOMContentLoaded', initAddressMap);
} else {
   initAddressMap();
}

// Fallback: also run on window.onload just in case
window.addEventListener('load', () => {
   console.log('Window loaded, checking if addresses loaded...');
   const list = document.getElementById('address-list');
   if (list && list.children.length === 0) {
      console.log('Address list empty on load, retrying loadAddresses...');
      loadAddresses();
   }
});

window.initMap = function () {
   console.log('initMap called');
   // Default to Cusco, Peru
   const defaultLocation = { lat: -13.5319, lng: -71.9675 };

   try {
      map = new google.maps.Map(document.getElementById("google-map"), {
         center: defaultLocation,
         zoom: 13,
      });

      const input = document.getElementById("map-search-input");
      autocomplete = new google.maps.places.Autocomplete(input);
      autocomplete.bindTo("bounds", map);

      marker = new google.maps.Marker({
         map: map,
         anchorPoint: new google.maps.Point(0, -29),
         draggable: true
      });

      autocomplete.addListener("place_changed", () => {
         marker.setVisible(false);
         const place = autocomplete.getPlace();

         if (!place.geometry || !place.geometry.location) {
            window.alert("No details available for input: '" + place.name + "'");
            return;
         }

         if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
         } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
         }

         marker.setPosition(place.geometry.location);
         marker.setVisible(true);
         selectedLocation = place.geometry.location;
      });

      map.addListener("click", (e) => {
         placeMarkerAndPanTo(e.latLng);
      });

      marker.addListener("dragend", (e) => {
         selectedLocation = e.latLng;
         geocodePosition(marker.getPosition());
      });
   } catch (e) {
      console.error('Error in initMap execution:', e);
   }
};

function placeMarkerAndPanTo(latLng) {
   marker.setPosition(latLng);
   map.panTo(latLng);
   selectedLocation = latLng;
   geocodePosition(latLng);
}

function geocodePosition(pos) {
   const geocoder = new google.maps.Geocoder();
   geocoder.geocode({
      latLng: pos
   }, function (responses) {
      if (responses && responses.length > 0) {
         document.getElementById('map-search-input').value = responses[0].formatted_address;
      } else {
         document.getElementById('map-search-input').value = 'Cannot determine address at this location.';
      }
   });
}

function loadAddresses() {
   if (!window.isUserLoggedIn) {
      console.log('Guest user, skipping loadAddresses fetch.');
      const list = document.getElementById('address-list');
      if (list) {
         list.innerHTML = '<p>Selecciona una dirección usando el mapa.</p>';
      }
      // Ensure button is visible
      const btnAdd = document.getElementById('btn-add-address');
      if (btnAdd) btnAdd.style.display = 'block';
      return;
   }

   console.log('Loading addresses...');
   fetch('/addresses')
      .then(response => {
         if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
         }
         return response.json();
      })
      .then(addresses => {
         console.log('Addresses loaded:', addresses);
         const list = document.getElementById('address-list');
         if (!list) {
            console.error('Address list element not found!');
            return;
         }

         if (!Array.isArray(addresses)) {
            console.error('Addresses is not an array:', addresses);
            if (addresses.data && Array.isArray(addresses.data)) {
               addresses = addresses.data;
            } else {
               list.innerHTML = '<p class="text-danger">Error: Formato de datos incorrecto.</p>';
               return;
            }
         }

         list.innerHTML = '';

         if (addresses.length === 0) {
            list.innerHTML = '<p>No tienes direcciones guardadas.</p>';
            const btnAdd = document.getElementById('btn-add-address');
            if (btnAdd) btnAdd.style.display = 'block';
            return;
         }

         if (addresses.length >= 3) {
            document.getElementById('btn-add-address').style.display = 'none';
         } else {
            document.getElementById('btn-add-address').style.display = 'block';
         }

         addresses.forEach((addr, index) => {
            renderAddressCard(addr, index === 0);
         });
      })
      .catch(error => {
         console.error('Error loading addresses:', error);
         const list = document.getElementById('address-list');
         if (list) list.innerHTML = `<p class="text-danger">Error al cargar direcciones: ${error.message}</p>`;
      });
}

function renderAddressCard(addr, isSelected = false) {
   const list = document.getElementById('address-list');
   const div = document.createElement('div');
   div.className = `checkout-address-card ${isSelected ? 'selected' : ''}`;
   div.onclick = () => {
      const radio = div.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
      document.querySelectorAll('.checkout-address-card').forEach(c => c.classList.remove('selected'));
      div.classList.add('selected');
      selectAddress(addr.address, addr.latitude, addr.longitude);
   };

   // For guests, we might not have an ID, so use a random one or 0
   const addrId = addr.id || 'guest_' + Math.random().toString(36).substr(2, 9);
   const isGuest = !window.isUserLoggedIn;

   div.innerHTML = `
         <div class="checkout-address-content">
             <div class="checkout-address-radio">
                 <input type="radio" name="selected_address_id" value="${addrId}" ${isSelected ? 'checked' : ''}>
             </div>
             <div class="checkout-address-details">
                 <span class="checkout-address-text">${addr.address}</span>
                 ${addr.is_default ? '<span class="checkout-default-badge">Principal</span>' : ''}
             </div>
         </div>
         ${!isGuest ? `
         <button type="button" class="checkout-btn-delete" onclick="event.stopPropagation(); deleteAddress(${addrId})">
             <i class="fas fa-trash"></i>
         </button>` : ''}
     `;
   list.appendChild(div);

   if (isSelected) {
      selectAddress(addr.address, addr.latitude, addr.longitude);
   }
}

window.selectAddress = function (address, lat, lng) {
   console.log('Selecting address:', address);
   const addrInput = document.getElementById('shipping_address');
   const latInput = document.getElementById('latitude');
   const lngInput = document.getElementById('longitude');

   if (addrInput) addrInput.value = address;
   if (latInput) latInput.value = lat;
   if (lngInput) lngInput.value = lng;
};

function saveAddress() {
   const address = document.getElementById('map-search-input').value;

   if (!address || !selectedLocation) {
      alert('Por favor selecciona una ubicación en el mapa.');
      return;
   }

   const lat = selectedLocation.lat();
   const lng = selectedLocation.lng();

   // Handle Guest
   if (!window.isUserLoggedIn) {
      console.log('Guest user, using selected address locally.');

      // Clear list first (since guests only pick one for now, or we can append)
      const list = document.getElementById('address-list');
      list.innerHTML = '';

      const guestAddr = {
         address: address,
         latitude: lat,
         longitude: lng,
         is_default: true,
         id: null
      };

      renderAddressCard(guestAddr, true);

      document.getElementById('map-container').style.display = 'none';
      document.getElementById('map-search-input').value = '';
      return;
   }

   // Handle Authenticated User
   const data = {
      address: address,
      latitude: lat,
      longitude: lng
   };

   fetch('/addresses', {
      method: 'POST',
      headers: {
         'Content-Type': 'application/json',
         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(data)
   })
      .then(response => {
         if (!response.ok) {
            return response.json().then(err => { throw err; });
         }
         return response.json();
      })
      .then(data => {
         document.getElementById('map-container').style.display = 'none';
         document.getElementById('map-search-input').value = '';
         loadAddresses();
      })
      .catch(error => {
         alert(error.message || 'Error al guardar la dirección');
      });
}

window.deleteAddress = function (id) {
   if (!confirm('¿Estás seguro de eliminar esta dirección?')) return;

   fetch(`/addresses/${id}`, {
      method: 'DELETE',
      headers: {
         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
   })
      .then(response => {
         if (response.ok) {
            loadAddresses();
         } else {
            alert('Error al eliminar la dirección');
         }
      });
};
