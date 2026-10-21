<p align="center">
  <a href="http://agromarket.test" target="_blank">
    <img src="public/img/Logo_Claro.png" width="300" alt="AgroMarket Logo">
  </a>
</p>

<p align="center">
    <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"></a>
    <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"></a>
    <a href="https://stripe.com"><img src="https://img.shields.io/badge/Stripe-Payments-008CDD?style=for-the-badge&logo=stripe&logoColor=white" alt="Stripe"></a>
</p>

# AgroMarket

**AgroMarket** es una plataforma de comercio electrónico diseñada para conectar directamente a agricultores con consumidores finales, eliminando intermediarios y promoviendo el comercio justo. Nuestra misión es potenciar la economía local y ofrecer productos frescos y de calidad a precios accesibles.

## 🚀 Características Principales

- **Gestión de Usuarios**: Roles diferenciados para Administradores, Agricultores y Clientes.
- **Catálogo de Productos**: Los agricultores pueden gestionar sus propios productos, inventario y precios.
- **Carrito de Compras**: Sistema de carrito intuitivo y persistente.
- **Pagos Seguros**: Integración completa con **Stripe** para procesar pagos con tarjeta de crédito/débito.
- **Facturación Electrónica**: Generación automática de comprobantes de pago.
- **Gestión de Pedidos**: Seguimiento de estados de pedido (Pendiente, Procesando, Enviado, Entregado).
- **Seguridad Avanzada**:
    - Protección contra ataques de fuerza bruta en el login.
    - Prevención de vulnerabilidades IDOR (Insecure Direct Object References).
    - Protección contra Mass Assignment.
    - Cabeceras de seguridad HTTP (HSTS, X-Frame-Options, etc.).
    - Sanitización de logs para prevenir fugas de información.
    - Rate Limiting en formularios de contacto para evitar spam.
    - Gestión atómica de inventario para prevenir sobreventa.

## 🛠️ Stack Tecnológico

- **Backend**: Laravel 12 (PHP 8.2+, extensiones `mbstring`, `pdo_mysql`, `gd`; `pdo_sqlite` para los tests)
- **Frontend**: Blade Templates, Vanilla JS, CSS3 (Diseño Responsivo)
- **Base de Datos**: MySQL / MariaDB
- **Pagos**: Stripe API
- **Servidor**: Apache / Nginx

## 🧩 Arquitectura: monolito modular

El backend está organizado por dominio en `app/Modules/<Módulo>`. Cada módulo tiene sus modelos, controladores, requests, servicios, rutas y factories, y un `ServiceProvider` que extiende `App\Modules\ModuleServiceProvider` (registrado en `bootstrap/providers.php`).

| Módulo | Responsabilidad |
|---|---|
| Identity | Usuarios, login (contraseña y Firebase), onboarding, perfil, direcciones y gestión de usuarios del admin |
| Catalog | Productos, unidades, categorías, búsqueda, reseñas e inventario (`StockService`) |
| Orders | Pedidos, precio del carrito y creación de pedidos |
| Payments | Stripe: intención de pago, confirmación y webhook |
| Invoicing | Comprobantes electrónicos |
| Moderation | Reportes de productos |
| Backoffice | Dashboards del admin y del agricultor |

Las dependencias van en un solo sentido: `Identity ← Catalog ← Orders ← Payments | Invoicing | Moderation`, y nadie depende de `Backoffice`. Un módulo no modifica datos de otro directamente: usa sus acciones o servicios públicos, o reacciona a sus eventos. Estas reglas se verifican en `tests/Arch/ModulesTest.php`.

Las vistas, los assets y las migraciones existentes siguen en `resources/` y `database/migrations`; las migraciones nuevas de un módulo van en `app/Modules/<Módulo>/Database/Migrations`.

## ⚙️ Instalación y Configuración

Sigue estos pasos para levantar el proyecto en tu entorno local:

1.  **Clonar el repositorio**
    ```bash
    git clone https://github.com/tu-usuario/agromarket.git
    cd agromarket
    ```

2.  **Instalar dependencias de PHP**
    ```bash
    composer install
    ```

3.  **Instalar dependencias de Frontend**
    ```bash
    npm install
    npm run build
    ```

4.  **Configurar variables de entorno**
    ```bash
    cp .env.example .env
    ```
    Actualiza el archivo `.env` con tus credenciales de base de datos y estas claves:

    | Variable | Uso |
    |---|---|
    | `STRIPE_KEY`, `STRIPE_SECRET`, `STRIPE_WEBHOOK_SECRET` | Pagos y webhook firmado de Stripe |
    | `FIREBASE_PROJECT_ID` | Verificación de tokens del login con Google/Facebook (sin esta variable el login social se rechaza) |
    | `DECOLECTA_TOKEN` | Consulta de DNI (RENIEC) al verificar agricultores |
    | `GOOGLE_MAPS_API_KEY` | Mapa de direcciones en el checkout |
    | `APIS_PERU_*`, `COMPANY_*`, `INVOICE_SERIE_*` | Datos de facturación electrónica |

5.  **Generar clave de aplicación**
    ```bash
    php artisan key:generate
    ```

6.  **Ejecutar migraciones y seeders**
    ```bash
    php artisan migrate --seed
    php artisan storage:link
    ```
    El seeder crea `admin@agromarket.com` con una contraseña aleatoria que se muestra en la consola.

    Si actualizas una instalación existente, mueve los documentos de verificación y facturas antiguos al disco privado:
    ```bash
    php artisan storage:move-sensitive
    ```

7.  **Iniciar el servidor local**
    ```bash
    php artisan serve
    ```

## 📚 Documentación de la API (Swagger)

Los endpoints que devuelven JSON (catálogo, búsqueda, pagos, login social, direcciones, reportes, facturas y las acciones JSON del agricultor y del admin) están documentados con OpenAPI usando [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger).

1.  **Generar la especificación** (también en cada despliegue):
    ```bash
    php artisan l5-swagger:generate
    ```
2.  **Abrir Swagger UI** en `/api/documentation`. El JSON queda en `/docs?api-docs.json`.

En local la documentación es pública. En cualquier otro entorno solo la ven los administradores con sesión iniciada. Las definiciones están en atributos OpenAPI (`#[OA\Get]`, `#[OA\Post]`, etc.) sobre los métodos de los controladores y las piezas compartidas en `app/OpenApi/OpenApiSpec.php`; al agregar o cambiar un endpoint JSON, actualiza sus atributos (`tests/Feature/ApiDocumentationTest.php` verifica que estén todos).

## 🤝 Cómo Colaborar

¡Agradecemos tu interés en mejorar AgroMarket! Si deseas contribuir, por favor sigue estos pasos:

1.  Haz un **Fork** del repositorio.
2.  Crea una nueva rama para tu funcionalidad o corrección (`git checkout -b feature/nueva-funcionalidad`).
3.  Realiza tus cambios y haz commit (`git commit -m 'Agrega nueva funcionalidad'`).
4.  Haz push a la rama (`git push origin feature/nueva-funcionalidad`).
5.  Abre un **Pull Request** describiendo tus cambios.

### Guía de Estilo
- El código PHP se formatea con Laravel Pint (`vendor/bin/pint`).
- Asegúrate de que tu código pase todas las pruebas (`php artisan test`).

## 📄 Licencia

Este proyecto es software de código abierto licenciado bajo la [MIT license](https://opensource.org/licenses/MIT).

---
<p align="center">Desarrollado con ❤️ para el campo.</p>
