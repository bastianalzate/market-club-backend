# Documentación de APIs para el Frontend - Market Club

## 🔐 **Autenticación**

### Registro de Usuario

```http
POST /api/register
Content-Type: application/json

{
    "name": "Juan Pérez",
    "email": "juan@example.com",
    "password": "password123",
    "phone": "+573001234567",
    "country": "Colombia",
    "is_wholesaler": false
}
```

**Campos:**

-   `name` (requerido): Nombre completo del usuario
-   `email` (requerido): Email único del usuario
-   `password` (requerido): Contraseña mínimo 8 caracteres
-   `phone` (opcional): Número de teléfono
-   `country` (opcional): País del usuario
-   `is_wholesaler` (opcional): Boolean - true si es mayorista, false si es cliente regular

### Login

```http
POST /api/login
Content-Type: application/json

{
    "email": "juan@example.com",
    "password": "password123"
}
```

### Logout

```http
POST /api/logout
Authorization: Bearer {token}
```

### Obtener Perfil

```http
GET /api/me
Authorization: Bearer {token}
```

## 🛍️ **Productos**

### Listar Productos

```http
GET /api/products?page=1&per_page=12&category_id=1&country=colombia&beer_style=ipa&price_range=15000_25000&search=cerveza&sort_by=price&sort_order=asc
```

**Parámetros:**

-   `page` (opcional): Número de página (default: 1)
-   `per_page` (opcional): Productos por página (default: 15)
-   `category_id` (opcional): Filtrar por categoría específica
-   `country` (opcional): Filtrar por país de origen (inglaterra, colombia, alemania, italia, escocia, belgica, espana, paises bajos, japon, mexico, peru, republica checa, estados unidos, tailandia)
-   `beer_style` (opcional): Filtrar por estilo de cerveza (ale, blonde, dark, ipa, lager, pale_ale, pilsner, porter, wheat)
-   `price_range` (opcional): Filtrar por rango de precios (less_than_15000, 15000_25000, 25000_35000, 35000_50000, 50000_75000, 75000_100000, more_than_100000)

**Nota:** El frontend puede enviar nombres en minúsculas y sin tildes. El backend los mapea automáticamente a los nombres correctos almacenados en la base de datos.

-   `search` (opcional): Buscar en nombre y descripción
-   `featured` (opcional): true para solo productos destacados
-   `sort_by` (opcional): Campo para ordenar (default: created_at)
-   `sort_order` (opcional): Orden ascendente o descendente (default: desc)

**Ejemplos de uso:**

```http
# Todas las cervezas
GET /api/products

# Cervezas de Inglaterra
GET /api/products?country=inglaterra

# Cervezas de Colombia
GET /api/products?country=colombia

# Cervezas IPA
GET /api/products?beer_style=ipa

# Cervezas Lager de Bélgica
GET /api/products?country=belgica&beer_style=lager

# Cervezas entre $15,000 y $25,000
GET /api/products?price_range=15000_25000

# Cervezas IPA entre $25,000 y $35,000
GET /api/products?beer_style=ipa&price_range=25000_35000

# Cervezas de Alemania con búsqueda
GET /api/products?country=alemania&search=artesanal

# Cervezas Porter de México
GET /api/products?country=mexico&beer_style=porter

# Cervezas de Bélgica en categoría específica
GET /api/products?country=belgica&category_id=1

# Cervezas de México con límite
GET /api/products?country=mexico&per_page=5

# Cervezas premium (más de $100,000)
GET /api/products?price_range=more_than_100000
```

### Productos Destacados

```http
GET /api/products/featured?limit=10&category_id=1&country=colombia&beer_style=ipa&price_range=15000_25000&search=cerveza&sort_by=price&sort_order=asc
```

**Parámetros:**

-   `limit` (opcional): Número máximo de productos a retornar (default: 10)
-   `category_id` (opcional): Filtrar por categoría específica
-   `country` (opcional): Filtrar por país (colombia, alemania, belgica, espana, china, japon, holanda, escocia, reino unido, tailandia, mexico, peru)
-   `beer_style` (opcional): Filtrar por estilo de cerveza (ale, blonde, dark, ipa, lager, pale_ale, pilsner, porter, wheat)
-   `price_range` (opcional): Filtrar por rango de precios (less_than_15000, 15000_25000, 25000_35000, 35000_50000, 50000_75000, 75000_100000, more_than_100000)
-   `search` (opcional): Buscar en nombre y descripción
-   `sort_by` (opcional): Campo para ordenar (default: created_at)
-   `sort_order` (opcional): Orden ascendente o descendente (default: desc)

**Respuesta:**

```json
[
    {
        "id": 1,
        "name": "Cerveza Premium",
        "price": 15000,
        "sale_price": 12000,
        "current_price": 12000,
        "image_url": "http://localhost:8000/storage/products/2024/09/image.jpg",
        "stock_quantity": 50,
        "packaging_type": "botella",
        "volume_ml": 330,
        "is_favorite": false
    },
    {
        "id": 2,
        "name": "Cerveza Artesanal",
        "price": 18000,
        "sale_price": null,
        "current_price": 18000,
        "image_url": "http://localhost:8000/storage/products/2024/09/image2.jpg",
        "stock_quantity": 30,
        "packaging_type": "lata",
        "volume_ml": 355,
        "is_favorite": true
    }
]
```

### Últimas Cervezas Agregadas

```http
GET /api/products/latest-beers?limit=10&country=colombia&beer_style=ipa&price_range=15000_25000&search=cerveza
```

**Parámetros:**

-   `limit` (opcional): Número máximo de cervezas a retornar (default: 10)
-   `country` (opcional): Filtrar por país (colombia, alemania, belgica, espana, china, japon, holanda, escocia, reino unido, tailandia, mexico, peru)
-   `beer_style` (opcional): Filtrar por estilo de cerveza (ale, blonde, dark, ipa, lager, pale_ale, pilsner, porter, wheat)
-   `price_range` (opcional): Filtrar por rango de precios (less_than_15000, 15000_25000, 25000_35000, 35000_50000, 50000_75000, 75000_100000, more_than_100000)
-   `search` (opcional): Buscar en nombre y descripción

**Respuesta:**

```json
[
    {
        "id": 1,
        "name": "Cerveza Premium",
        "price": 15000,
        "sale_price": 12000,
        "current_price": 12000,
        "image_url": "http://localhost:8000/storage/products/2024/09/image.jpg",
        "stock_quantity": 50,
        "packaging_type": "botella",
        "volume_ml": 330,
        "created_at": "2024-09-12 14:30:00",
        "is_favorite": false
    },
    {
        "id": 2,
        "name": "Cerveza Artesanal",
        "price": 18000,
        "sale_price": null,
        "current_price": 18000,
        "image_url": "http://localhost:8000/storage/products/2024/09/image2.jpg",
        "stock_quantity": 30,
        "packaging_type": "lata",
        "volume_ml": 355,
        "created_at": "2024-09-12 14:25:00",
        "is_favorite": true
    }
]
```

### Obtener Producto

```http
GET /api/products/{id}
```

## 🔍 **Búsqueda**

### Búsqueda General

```http
GET /api/search?q=iphone&category=1&min_price=100000&max_price=500000&sort=price&order=asc&page=1&per_page=15
```

### Sugerencias de Búsqueda

```http
GET /api/search/suggestions?q=iph&limit=5
```

### Productos Destacados

```http
GET /api/search/featured?limit=8
```

### Productos Relacionados

```http
GET /api/search/related/{product_id}?limit=4
```

## 🛒 **Carrito de Compras**

### Obtener Carrito

```http
GET /api/cart
Authorization: Bearer {token}
X-Session-ID: {session_id} // Para usuarios no autenticados
```

### Agregar Producto al Carrito

```http
POST /api/cart/add
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 2
}
```

### Actualizar Cantidad

```http
PUT /api/cart/update
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 3
}
```

### Remover Producto

```http
DELETE /api/cart/remove
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1
}
```

### Limpiar Carrito

```http
DELETE /api/cart/clear
Authorization: Bearer {token}
```

### Resumen del Carrito

```http
GET /api/cart/summary
Authorization: Bearer {token}
```

### Sincronizar Carrito (después del login)

```http
POST /api/cart/sync
Authorization: Bearer {token}
Content-Type: application/json
X-Session-ID: {session_id}
```

## ❤️ **Wishlist**

### Obtener Wishlist

```http
GET /api/wishlist
Authorization: Bearer {token}
```

### Agregar a Wishlist

```http
POST /api/wishlist/add
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1
}
```

### Remover de Wishlist

```http
DELETE /api/wishlist/remove
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1
}
```

### Verificar si está en Wishlist

```http
POST /api/wishlist/check
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1
}
```

### Limpiar Wishlist

```http
DELETE /api/wishlist/clear
Authorization: Bearer {token}
```

### Mover a Carrito

```http
POST /api/wishlist/move-to-cart
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 1
}
```

## 🛒 **Checkout**

### Resumen del Checkout

```http
GET /api/checkout/summary
Authorization: Bearer {token}
```

### Validar Dirección de Envío

```http
POST /api/checkout/validate-address
Authorization: Bearer {token}
Content-Type: application/json

{
    "shipping_address": {
        "name": "Juan Pérez",
        "address": "Calle 123 #45-67",
        "city": "Bogotá",
        "state": "Cundinamarca",
        "postal_code": "110111",
        "country": "Colombia",
        "phone": "+573001234567"
    }
}
```

### Calcular Costo de Envío

```http
POST /api/checkout/calculate-shipping
Authorization: Bearer {token}
Content-Type: application/json

{
    "city": "Bogotá",
    "state": "Cundinamarca",
    "postal_code": "110111"
}
```

### Crear Orden

```http
POST /api/checkout/create-order
Authorization: Bearer {token}
Content-Type: application/json

{
    "shipping_address": {
        "name": "Juan Pérez",
        "address": "Calle 123 #45-67",
        "city": "Bogotá",
        "state": "Cundinamarca",
        "postal_code": "110111",
        "country": "Colombia",
        "phone": "+573001234567"
    },
    "billing_address": {
        "name": "Juan Pérez",
        "address": "Calle 123 #45-67",
        "city": "Bogotá",
        "state": "Cundinamarca",
        "postal_code": "110111",
        "country": "Colombia",
        "phone": "+573001234567"
    },
    "notes": "Entregar en horario de oficina"
}
```

## 💳 **Pagos**

### Crear Token de Pago

```http
POST /api/payments/token
Content-Type: application/json

{
    "number": "4242424242424242",
    "cvc": "123",
    "exp_month": "12",
    "exp_year": "2025",
    "card_holder": "Juan Pérez"
}
```

### Procesar Pago

```http
POST /api/payments/process
Authorization: Bearer {token}
Content-Type: application/json

{
    "order_id": 1,
    "payment_method_type": "CARD",
    "payment_token": "tok_test_xxxxxxxx",
    "installments": 1
}
```

### Verificar Pago

```http
POST /api/payments/verify
Authorization: Bearer {token}
Content-Type: application/json

{
    "transaction_id": "12345678-1234-1234-1234-123456789012"
}
```

### Métodos de Pago Disponibles

```http
GET /api/payments/methods
```

## 📦 **Órdenes**

### Listar Órdenes del Usuario

```http
GET /api/orders?page=1&per_page=10&status=pending
Authorization: Bearer {token}
```

### Obtener Orden

```http
GET /api/orders/{id}
Authorization: Bearer {token}
```

### Crear Orden (desde carrito)

```http
POST /api/orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "shipping_address": { ... },
    "billing_address": { ... },
    "notes": "Comentarios adicionales"
}
```

## 📋 **Categorías**

### Listar Categorías

```http
GET /api/categories
```

### Obtener Categoría

```http
GET /api/categories/{id}
```

## 🔧 **Headers Requeridos**

### Para Usuarios Autenticados

```
Authorization: Bearer {token}
Content-Type: application/json
```

### Para Usuarios No Autenticados (Carrito)

```
X-Session-ID: {session_id}
Content-Type: application/json
```

## 📊 **Respuestas de la API**

### Respuesta Exitosa

```json
{
    "success": true,
    "message": "Operación exitosa",
    "data": {
        // Datos de respuesta
    }
}
```

### Respuesta de Error

```json
{
    "success": false,
    "message": "Mensaje de error",
    "errors": {
        "field": ["Error específico"]
    }
}
```

### Respuesta Paginada

```json
{
    "success": true,
    "data": {
        "products": {
            "data": [...],
            "current_page": 1,
            "last_page": 5,
            "per_page": 15,
            "total": 75
        }
    }
}
```

## 🚀 **Flujo de Compra Completo**

### 1. Usuario No Autenticado

1. **Buscar productos**: `GET /api/search`
2. **Agregar al carrito**: `POST /api/cart/add` (con `X-Session-ID`)
3. **Ver carrito**: `GET /api/cart` (con `X-Session-ID`)

### 2. Usuario Autenticado

1. **Login**: `POST /api/login`
2. **Sincronizar carrito**: `POST /api/cart/sync`
3. **Proceder al checkout**: `GET /api/checkout/summary`
4. **Validar dirección**: `POST /api/checkout/validate-address`
5. **Crear orden**: `POST /api/checkout/create-order`
6. **Procesar pago**: `POST /api/payments/process`
7. **Verificar pago**: `POST /api/payments/verify`

## 🔒 **Seguridad**

-   Todas las rutas protegidas requieren token de autenticación
-   Los tokens se obtienen mediante login
-   Los tokens expiran según configuración de Sanctum
-   Las rutas de pago requieren validación adicional
-   Los webhooks de Wompi se verifican con firma HMAC

## 📱 **Consideraciones para el Frontend**

### Manejo de Sesiones

-   Usar `X-Session-ID` para usuarios no autenticados
-   Sincronizar carrito después del login
-   Mantener sesión activa con refresh tokens

### Manejo de Errores

-   Verificar `success` en todas las respuestas
-   Mostrar mensajes de error amigables
-   Manejar errores de validación específicos

### Optimización

-   Usar paginación para listas grandes
-   Implementar cache para productos y categorías
-   Lazy loading para imágenes

### UX/UI

-   Mostrar loading states durante requests
-   Implementar retry automático para errores de red
-   Validación en tiempo real en formularios
