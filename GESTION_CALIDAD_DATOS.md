# Gestión de Calidad de Datos - Market Club

## Problema Identificado

En producción se detectaron dos tipos de datos incompletos en las órdenes:

1. **Clientes no registrados**: Órdenes con `user_id = NULL`
2. **Productos eliminados**: Órdenes con referencias a productos que ya no existen en la BD

## Solución Implementada

### 1. Exportación CSV Mejorada

El reporte de ventas ahora incluye:

#### Columnas Adicionales:

-   **EMAIL**: Email del cliente (recuperado de usuario o dirección de envío)
-   **TELÉFONO**: Teléfono del cliente (recuperado de usuario o dirección de envío)
-   **ALERTAS**: Columna que indica problemas de calidad de datos

#### Sistema de Alertas:

-   `OK`: La orden tiene todos los datos completos
-   `Usuario no registrado o eliminado`: La orden no tiene usuario asociado
-   `Contiene productos eliminados`: Uno o más productos de la orden fueron eliminados
-   `Orden sin productos`: La orden no tiene items asociados

### 2. Recuperación de Datos

Cuando una orden no tiene usuario registrado, el sistema intenta recuperar información de:

-   **shipping_address**: Dirección de envío
    -   name / full_name
    -   email
    -   phone

Esto permite tener información de contacto incluso sin usuario registrado.

### 3. Estadísticas en Dashboard

El dashboard ahora muestra:

-   Órdenes sin usuarios (últimos 30 días)
-   Órdenes con productos eliminados (últimos 30 días)

## Casos de Uso

### Ejemplo de CSV Exportado:

```csv
# DE ORDEN,CLIENTE,EMAIL,TELEFONO,MONTO,PRODUCTOS,ESTADO,ALERTAS
"ORD-68F9624CE2AB9","Juan Pérez","juan@example.com","3001234567","20,472.00","CERVEZA POKER LATA X 330 ML (x1), CERVEZA REDDS LATA X 269 ML (x1)","Procesando","OK"
"ORD-68F95ADABBCBF","Cliente no registrado","","","52,840.00","CERVEZA HOLLANDIA PREMIUM (x1)","Pendiente","Usuario no registrado o eliminado"
"ORD-68F95A9C830A9","María García","maria@example.com","3007654321","74,260.00","CERVEZA ESTRELLA GALICIA LATA (x1), Producto eliminado (x2)","Procesando","Contiene productos eliminados"
```

## Recomendaciones para Prevenir Estos Problemas

### 1. Usuarios No Registrados

**Problema**: Órdenes con `user_id = NULL`

**Causas posibles**:

-   Checkout de invitado (guest checkout)
-   Eliminación de usuarios después de crear la orden
-   Importación de datos antiguos

**Soluciones**:

-   ✅ Siempre capturar información de contacto en `shipping_address`
-   ✅ Considerar crear automáticamente un usuario "temporal" con la información del checkout
-   ✅ Implementar soft deletes para usuarios (no eliminar físicamente)

### 2. Productos Eliminados

**Problema**: Referencias a productos que ya no existen

**Causas posibles**:

-   Eliminación de productos con órdenes existentes
-   Importación de datos sin validación

**Soluciones**:

-   ✅ Implementar soft deletes para productos
-   ✅ Validar que un producto no tenga órdenes activas antes de eliminarlo
-   ✅ Archivar productos en lugar de eliminarlos

### 3. Implementar Soft Deletes

#### Para Usuarios (User.php):

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
```

#### Para Productos (Product.php):

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
}
```

#### Migración:

```bash
php artisan make:migration add_soft_deletes_to_users_table
php artisan make:migration add_soft_deletes_to_products_table
```

```php
// En las migraciones
Schema::table('users', function (Blueprint $table) {
    $table->softDeletes();
});

Schema::table('products', function (Blueprint $table) {
    $table->softDeletes();
});
```

## Monitoreo Continuo

### Queries Útiles para Auditoría:

```sql
-- Órdenes sin usuario
SELECT COUNT(*)
FROM orders
WHERE user_id IS NULL;

-- Órdenes con productos eliminados
SELECT DISTINCT o.id, o.order_number, o.created_at
FROM orders o
JOIN order_items oi ON o.id = oi.order_id
LEFT JOIN products p ON oi.product_id = p.id
WHERE p.id IS NULL;

-- Recuperar información de contacto de órdenes sin usuario
SELECT
    order_number,
    JSON_EXTRACT(shipping_address, '$.name') as name,
    JSON_EXTRACT(shipping_address, '$.email') as email,
    JSON_EXTRACT(shipping_address, '$.phone') as phone
FROM orders
WHERE user_id IS NULL;
```

## Mantenimiento

### Limpieza de Datos Históricos:

Si necesitas limpiar datos históricos problemáticos:

```php
// Script de limpieza (ejecutar con cuidado)
use App\Models\Order;
use App\Models\User;

// Crear usuarios temporales para órdenes sin usuario
$ordersWithoutUser = Order::whereNull('user_id')->get();

foreach ($ordersWithoutUser as $order) {
    $shippingAddress = $order->shipping_address;
    if (is_array($shippingAddress) && isset($shippingAddress['email'])) {
        // Buscar o crear usuario
        $user = User::firstOrCreate(
            ['email' => $shippingAddress['email']],
            [
                'name' => $shippingAddress['name'] ?? 'Cliente Importado',
                'phone' => $shippingAddress['phone'] ?? null,
                'password' => bcrypt(Str::random(32)), // Password temporal
            ]
        );

        $order->update(['user_id' => $user->id]);
    }
}
```

## Próximos Pasos

1. ✅ Implementar soft deletes en User y Product
2. ⏳ Crear validación antes de eliminar productos
3. ⏳ Implementar panel de auditoría de calidad de datos
4. ⏳ Crear alertas automáticas cuando se detecten datos incompletos
5. ⏳ Implementar backup automático antes de eliminaciones

## Contacto y Soporte

Si encuentras más problemas de calidad de datos, documéntalos en este archivo para futuras referencias.
