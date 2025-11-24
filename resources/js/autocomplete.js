// Autocomplete search functionality
document.addEventListener('DOMContentLoaded', () => {
   const searchInput = document.getElementById('searchInput');
   const searchSuggestions = document.getElementById('searchSuggestions');
   const suggestionsList = searchSuggestions?.querySelector('.suggestions-list');

   if (!searchInput || !searchSuggestions) return;

   let debounceTimer;

   searchInput.addEventListener('input', function () {
      const query = this.value.trim();

      clearTimeout(debounceTimer);

      if (query.length < 2) {
         searchSuggestions.style.display = 'none';
         return;
      }

      debounceTimer = setTimeout(async () => {
         try {
            const response = await fetch(`/api/suggestions?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            displaySuggestions(data.suggestions || []);
         } catch (error) {
            console.error('Error fetching suggestions:', error);
         }
      }, 300);
   });

   function displaySuggestions(suggestions) {
      if (suggestions.length === 0) {
         searchSuggestions.style.display = 'none';
         return;
      }

      suggestionsList.innerHTML = suggestions.map(product => {
         let image = '/img/placeholder.png';
         if (product.image_path) {
            image = `/storage/${product.image_path}`;
         }

         const price = Number(product.price || 0).toFixed(2);

         return `
                <a href="/producto/${product.slug}" class="suggestion-item">
                    <img src="${image}" alt="${product.name}">
                    <div class="suggestion-info">
                        <div class="suggestion-name">${product.name}</div>
                        <div class="suggestion-price">S/. ${price}</div>
                    </div>
                </a>
            `;
      }).join('');

      searchSuggestions.style.display = 'block';
   }

   // Close suggestions when clicking outside
   document.addEventListener('click', function (e) {
      if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
         searchSuggestions.style.display = 'none';
      }
   });
});
