#!/bin/bash
# Script para desplegar la corrección de emails en producción

echo "🚀 Desplegando corrección de emails a producción..."
echo "=================================================="
echo ""

PROJECT_PATH="/home/marketclub-admin-dev/htdocs/admin-dev.marketclub.com.co"

# 1. Ir al directorio del proyecto
cd $PROJECT_PATH

# 2. Git pull (si estás usando git)
echo "📥 Obteniendo últimos cambios..."
git pull origin main 2>/dev/null || echo "⚠️  No se pudo hacer git pull (verifica si usas git)"
echo ""

# 3. Limpiar cache
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
echo "✅ Cache limpiado"
echo ""

# 4. Verificar que los archivos fueron actualizados
echo "📄 Verificando archivos actualizados..."
echo ""
echo "1. Template order-confirmation.blade.php:"
grep -n "{{ \$user->name ?? 'Cliente' }}" resources/views/emails/order-confirmation.blade.php && echo "   ✅ Actualizado" || echo "   ❌ NO actualizado"
echo ""
echo "2. Template payment-confirmation.blade.php:"
grep -n "{{ \$user->name ?? 'Cliente' }}" resources/views/emails/payment-confirmation.blade.php && echo "   ✅ Actualizado" || echo "   ❌ NO actualizado"
echo ""
echo "3. PaymentController.php (webhook):"
grep -n "Order::with(\['user', 'orderItems.product'\])" app/Http/Controllers/Api/PaymentController.php && echo "   ✅ Actualizado" || echo "   ❌ NO actualizado"
echo ""

# 5. Verificar configuración de Brevo
echo "⚙️  Verificando configuración de Brevo..."
php artisan tinker --execute="
echo 'BREVO_API_KEY: ' . (env('BREVO_API_KEY') ? '✓ Configurado' : '✗ NO configurado');
echo '\nBREVO_SENDER_EMAIL: ' . (env('BREVO_SENDER_EMAIL') ?: '✗ NO configurado');
"
echo ""

# 6. Mensaje final
echo "=================================================="
echo "✅ Despliegue completado"
echo ""
echo "📝 Próximos pasos:"
echo "  1. Hacer una compra de prueba"
echo "  2. Monitorear logs en tiempo real:"
echo "     tail -f storage/logs/laravel.log | grep -i email"
echo ""
echo "🔍 Si sigue sin funcionar, ejecuta el diagnóstico:"
echo "  /home/marketclub-admin-dev/diagnostico-email.sh"
echo "=================================================="

