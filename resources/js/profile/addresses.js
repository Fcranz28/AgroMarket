let map;
let marker;
let autocomplete;
let selectedLocation = null;

document.addEventListener('DOMContentLoaded', function () {
   loadAddresses();

   // Close modal when clicking outside
   window.onclick = function (event) {
      const modal = document.getElementById('addressModal');
      if (event.target == modal) {
         closeAddressModal();
      }
   }
});

function loadAddresses() {
   fetch('/addresses')
      .then(response => response.json())
      .then(addresses => {
         const container = document.getElementById('addresses-grid');
         container.innerHTML = '';

         // Render existing addresses
         addresses.forEach(addr => {
            const card = document.createElement('div');
            card.className = 'address-card';
            card.innerHTML = `
                    <div class="address-header">
                        <h3>Dirección</h3>
                        ${addr.is_default ? '<span class="default-badge">Principal</span>' : ''}
                    </div>
                    <p class="address-text">${addr.address}</p>
                    <div class="address-actions">
                        <button class="btn-link delete" onclick="deleteAddress(${addr.id})">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                `;
            container.appendChild(card);
         });

         // Render "Add Address" button if limit not reached
         if (addresses.length < 3) {
            const addBtn = document.createElement('button');
            addBtn.className = 'add-address-card';
            addBtn.onclick = openAddressModal;
            addBtn.innerHTML = `
                    <i class="fas fa-plus-circle"></i>
                    <span>Agregar Nueva Dirección</span>
                `;
            container.appendChild(addBtn);
         }
      });
}

function openAddressModal() {
   document.getElementById('addressModal').style.display = 'flex';
   if (!map) {
      initMap();
   }
}

function closeAddressModal() {
   document.getElementById('addressModal').style.display = 'none';
   document.getElementById('map-search-input').value = '';
   selectedLocation = null;
   if (marker) marker.setVisible(false);
}

function initMap() {
   // Default to Cusco, Peru
   const defaultLocation = { lat: -13.5319, lng: -71.9675 };

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
}

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
      }
   });
}

function saveAddress() {
   const address = document.getElementById('map-search-input').value;

   if (!address || !selectedLocation) {
      Swal.fire('Error', 'Por favor selecciona una ubicación en el mapa.', 'error');
      return;
   }

   const data = {
      address: address,
      latitude: selectedLocation.lat(),
      longitude: selectedLocation.lng()
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
         closeAddressModal();
         loadAddresses();
         Swal.fire('¡Éxito!', 'Dirección guardada correctamente', 'success');
      })
      .catch(error => {
         Swal.fire('Error', error.message || 'Error al guardar la dirección', 'error');
      });
}

function deleteAddress(id) {
   Swal.fire({
      title: '¿Estás seguro?',
      text: "No podrás revertir esto",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
   }).then((result) => {
      if (result.isConfirmed) {
         fetch(`/addresses/${id}`, {
            method: 'DELETE',
            headers: {
               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
         })
            .then(response => {
               if (response.ok) {
                  loadAddresses();
                  Swal.fire('¡Eliminado!', 'La dirección ha sido eliminada.', 'success');
               } else {
                  Swal.fire('Error', 'No se pudo eliminar la dirección', 'error');
               }
            });
      }
   });
}

// Expose functions to window
window.openAddressModal = openAddressModal;
window.closeAddressModal = closeAddressModal;
window.saveAddress = saveAddress;
window.deleteAddress = deleteAddress;
