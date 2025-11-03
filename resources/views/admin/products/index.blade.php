<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - AgroMercado</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="CRUD.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar superior -->
    <nav class="floating-navbar">
        <div class="logo-container">
            <a href="index.html">
               <img src="img/Logo_Claro.png" alt="Logo" class="logo logo-claro">
               <img src="img/Logo_Oscuro.png" alt="Logo" class="logo logo-oscuro">
            </a>
        </div>
        
        <div class="nav-links">
            <a href="index.html">Inicio</a>
            <a href="categorias.html">Productos</a>
            <a href="contacto.html">Contacto</a>
            <a href="nosotros.html">Nosotros</a>
        </div>

        <div class="nav-right">
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Buscar productos...">
                <button class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            
            <button id="themeToggle" class="theme-toggle">
                <i class="fas fa-sun"></i>
            </button>
        </div>
    </nav>

    <div class="crud-container">
        <div class="crud-header">
            <h2>Gestión de Productos</h2>
            <button class="add-product-btn" id="showFormBtn">
                <i class="fas fa-plus"></i>
                Nuevo Producto
            </button>
        </div>

        <!-- Formulario de producto -->
        <div class="product-form-container" id="productForm">
            <form class="product-form">
                <div class="form-group">
                    <label for="productName">Nombre del Producto *</label>
                    <input type="text" id="productName" name="productName" required>
                </div>

                <div class="form-group">
                    <label>Unidad de Medida y Cantidad *</label>
                    <div class="quantity-container">
                        <input type="number" id="quantity" name="quantity" min="1" required>
                        <div class="unit-buttons">
                            <button type="button" class="unit-btn active" data-unit="kg">Kg</button>
                            <button type="button" class="unit-btn" data-unit="arroba">Arroba</button>
                            <button type="button" class="unit-btn" data-unit="quintal">Quintal</button>
                            <button type="button" class="unit-btn" data-unit="saco">Saco (60kg)</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="category">Categoría *</label>
                    <select id="category" name="category" required>
                        <option value="">Selecciona una categoría</option>
                        <option value="frutas">Frutas</option>
                        <option value="verduras">Verduras y Hortalizas</option>
                        <option value="tuberculos">Tubérculos y Raíces</option>
                        <option value="granos">Granos y Legumbres</option>
                        <option value="hierbas">Hierbas y Aromáticas</option>
                        <option value="semillas">Semillas y Plantones</option>
                        <option value="insumos">Insumos Agrícolas</option>
                        <option value="herramientas">Herramientas Manuales</option>
                        <option value="maquinaria">Maquinaria Agrícola</option>
                        <option value="riego">Sistemas de Riego</option>
                        <option value="tecnologia">Tecnología Agrícola</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Descripción *</label>
                    <textarea id="description" name="description" maxlength="1000" rows="4" required></textarea>
                    <div class="char-counter">
                        <span id="charCount">0</span>/1000 caracteres
                    </div>
                </div>

                <div class="form-group">
                    <label>Imágenes del Producto *</label>
                    <div class="dropzone" id="dropzone">
                        <div class="dropzone-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Arrastra y suelta tus imágenes aquí</p>
                            <span>o</span>
                            <button type="button" class="browse-btn">Seleccionar Archivos</button>
                            <input type="file" hidden id="fileInput" multiple accept="image/*">
                        </div>
                        <div class="preview-container" id="previewContainer"></div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="cancel-btn" id="cancelBtn">Cancelar</button>
                    <button type="submit" class="save-btn">Guardar Producto</button>
                </div>
            </form>
        </div>

        <!-- Lista de productos -->
        <div class="products-list" id="productsList">
            <!-- Los productos se agregarán dinámicamente aquí -->
            <div class="product-card">
                <div class="product-image">
                    <img src="img/producto1.jpg" alt="Producto">
                </div>
                <div class="product-info">
                    <h3>Papa Amarilla</h3>
                    <p class="quantity">Cantidad: 100 Kg</p>
                    <p class="category">Categoría: Tubérculos y Raíces</p>
                </div>
                <div class="product-actions">
                    <button class="edit-btn">
                        <i class="fas fa-edit"></i>
                        Editar
                    </button>
                    <button class="delete-btn">
                        <i class="fas fa-trash"></i>
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar inferior -->
    <nav class="bottom-navbar">
        <a href="index.html" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Inicio</span>
        </a>
        <a href="pedidos.html" class="nav-item">
            <i class="fas fa-box"></i>
            <span>Pedidos</span>
        </a>
        <a href="CRUD.html" class="nav-item active">
            <i class="fas fa-plus-circle"></i>
            <span>Productos</span>
        </a>
        <a href="perfil.html" class="nav-item">
            <i class="fas fa-user"></i>
            <span>Perfil</span>
        </a>
    </nav>

    <script src="CRUD.js"></script>
</body>
</html>