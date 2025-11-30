// Search functionality
document.addEventListener('DOMContentLoaded', function () {
        const searchResultsGrid = document.getElementById('searchResultsGrid');
        const resultsCount = document.getElementById('resultsCount');
        const filterBtns = document.querySelectorAll('.search-filter-btn');
        const sortSelect = document.getElementById('sortSelect');

        // Get initial data from data attributes
        const container = document.querySelector('.search-results-container');
        const initialQuery = container?.dataset.query || '';
        const initialCategory = container?.dataset.category || 'all';

        let currentQuery = initialQuery;
        let currentCategory = initialCategory;
        let currentSort = 'featured';

        // Load initial results
        loadResults();

        // Category filter click handlers
        filterBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                        filterBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        currentCategory = this.dataset.category;
                        loadResults();
                });
        });

        // Sort change handler
        if (sortSelect) {
                sortSelect.addEventListener('change', function () {
                        currentSort = this.value;
                        loadResults();
                });
        }

        function loadResults() {
                searchResultsGrid.innerHTML = `
            <div class="search-loading-spinner">
                <div class="search-spinner"></div>
                <p>Buscando productos...</p>
            </div>
        `;

                // Build query parameters
                const params = new URLSearchParams({
                        q: currentQuery,
                        category: currentCategory !== 'all' ? currentCategory : '',
                        sort: currentSort
                });

                // Fetch search results
                fetch(`/api/search?${params.toString()}`)
                        .then(response => response.json())
                        .then(data => {
                                displayResults(data.products || []);
                                resultsCount.textContent = (data.products || []).length;
                        })
                        .catch(error => {
                                console.error('Error loading results:', error);
                                searchResultsGrid.innerHTML = `
                    <div class="search-error-message">
                        <p>Error al cargar los resultados. Por favor, intenta de nuevo.</p>
                    </div>
                `;
                        });
        }

        function displayResults(products) {
                if (!products || products.length === 0) {
                        searchResultsGrid.innerHTML = `
                <div class="search-no-results">
                    <p>No se encontraron productos que coincidan con tu búsqueda.</p>
                </div>
            `;
                        return;
                }

                searchResultsGrid.innerHTML = products.map(product => {
                        const imageSrc = product.image_path || product.image_url || '/img/placeholder.png';
                        const productUrl = `/producto/${product.slug}`;

                        return `
                <div class="search-product-card">
                    <div class="search-product-image-container">
                        <img src="${imageSrc}" alt="${product.name}" class="search-product-image" onerror="this.src='/img/placeholder.png'">
                    </div>
                    <div class="search-product-info">
                        ${product.category_name ? `<p class="search-product-category">PRODUCTOS</p>` : ''}
                        <h3 class="search-product-title">${product.name}</h3>
                        <div class="search-product-footer">
                            <div class="search-product-price">S/. ${parseFloat(product.price).toFixed(2)}</div>
                            <a href="${productUrl}" class="search-btn-add-cart" title="Ver Producto"></a>
                        </div>
                    </div>
                </div>
            `;
                }).join('');
        }
});
