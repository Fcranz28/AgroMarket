document.addEventListener('DOMContentLoaded', () => {
   const resultsGrid = document.getElementById('searchResultsGrid');
   const resultsCount = document.getElementById('resultsCount');
   const filterButtons = document.querySelectorAll('.filter-btn');
   const sortSelect = document.getElementById('sortSelect');

   // Check if we're on the search page
   if (!resultsGrid) return;

   let currentQuery = initialQuery || '';
   let currentCategory = initialCategory || 'all';
   let currentSort = 'featured';

   function showLoading() {
      resultsGrid.innerHTML = `
            <div class="loading-spinner" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <div class="spinner" style="margin: 0 auto 15px;"></div>
                <p>Buscando productos...</p>
            </div>
        `;
   }

   async function loadSearchResults() {
      showLoading();

      try {
         const url = `/api/search?q=${encodeURIComponent(currentQuery)}&category=${currentCategory}&sort=${currentSort}`;
         const response = await fetch(url);

         if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
         }

         const data = await response.json();
         const products = Array.isArray(data.products) ? data.products : [];

         renderProducts(products);
         updateResultsCount(data.total || products.length);

      } catch (error) {
         console.error('Error:', error);
         resultsGrid.innerHTML = `
                <div class="error-message" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                    <p style="color: #dc3545; margin-bottom: 15px;">Lo sentimos, ha ocurrido un error al buscar productos.</p>
                    <button id="retrySearch" class="btn" style="background: var(--primary-color); color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Intentar de nuevo</button>
                </div>
            `;
         const retry = document.getElementById('retrySearch');
         if (retry) retry.addEventListener('click', loadSearchResults);
      }
   }

   function renderProducts(products) {
      if (!products || products.length === 0) {
         resultsGrid.innerHTML = `
                <div class="no-results" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                    <i class="fas fa-search" style="font-size: 4rem; color: #ccc; margin-bottom: 20px; display: block;"></i>
                    <h3 style="margin-bottom: 10px; color: #666;">No se encontraron productos</h3>
                    <p style="color: #888; margin-bottom: 20px;">Intenta con otros términos de búsqueda o categorías.</p>
                    <a href="/productos" class="btn" style="background: var(--primary-color); color: white; padding: 10px 25px; border-radius: 5px; text-decoration: none; display: inline-block;">Ver todos los productos</a>
                </div>
            `;
         return;
      }

      resultsGrid.innerHTML = products.map(product => {
         let image = '/img/placeholder.png';
         if (product.image_path) {
            image = `/storage/${product.image_path}`;
         } else if (product.image_url) {
            image = product.image_url;
         }

         const price = Number(product.price || 0).toFixed(2);

         return `
                <article class="product-card" data-product-id="${product.id}">
                    <a href="/producto/${product.slug}">
                        <img src="${image}" alt="${product.name}" loading="lazy">
                    </a>
                    <div class="product-info">
                        <a href="/producto/${product.slug}" style="text-decoration: none; color: inherit;">
                            <h3>${product.name}</h3>
                        </a>
                        <p class="price">S/. ${price}</p>
                        <button class="add-to-cart" data-id="${product.id}">Agregar al carrito</button>
                    </div>
                </article>
            `;
      }).join('');
   }

   function updateResultsCount(count) {
      if (resultsCount) {
         resultsCount.textContent = count;
      }
   }

   function updateActiveFilter() {
      filterButtons.forEach(button => {
         button.classList.toggle('active', button.dataset.category === currentCategory);
      });
   }

   // Event listeners for category filter buttons
   filterButtons.forEach(button => {
      button.addEventListener('click', () => {
         currentCategory = button.dataset.category;
         updateActiveFilter();
         loadSearchResults();
      });
   });

   // Event listener for sort select
   if (sortSelect) {
      sortSelect.addEventListener('change', () => {
         currentSort = sortSelect.value;
         loadSearchResults();
      });
   }

   // Initial load
   loadSearchResults();
});
