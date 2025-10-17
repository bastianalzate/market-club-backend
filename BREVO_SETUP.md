# Configuración de Brevo para Market Club

## Pasos para configurar Brevo

### 1. Obtener API Key de Brevo

1. Ve a [Brevo Developers](https://developers.brevo.com/)
2. Crea una cuenta o inicia sesión
3. Ve a "My API Keys" en el dashboard
4. Crea una nueva API Key
5. Copia la API Key generada

### 2. Configurar variables de entorno

Agrega las siguientes variables a tu archivo `.env`:

```env
# Configuración de Brevo para envío de emails
BREVO_API_KEY=tu_api_key_aqui

# Configuración del remitente
BREVO_SENDER_NAME="Market Club"
BREVO_SENDER_EMAIL="noreply@marketclub.com"

# URL de la aplicación (para enlaces en emails)
APP_URL=https://marketclub.com
```

### 3. Verificar configuración

Para probar que Brevo está funcionando correctamente, puedes usar el método `sendWholesalerActivationEmail` del `EmailService`.

## Funcionalidades implementadas

### Email de Activación de Mayorista

Cuando se habilita un mayorista, se envía automáticamente un email con:

- ✅ Diseño HTML profesional
- ✅ Versión de texto plano
- ✅ Información personalizada del mayorista
- ✅ Enlaces a la plataforma
- ✅ Logging completo de eventos

### Endpoints disponibles

- `POST /admin/wholesalers-model/{wholesaler}/activate` - Activa mayorista y envía email
- `POST /admin/wholesalers-model/{wholesaler}/deactivate` - Desactiva mayorista

### Servicios creados

- `BrevoService` - Manejo directo de la API de Brevo
- `EmailService` - Servicio principal de emails (actualizado)

## Próximos pasos

1. Configurar las variables de entorno
2. Probar el envío de emails
3. Personalizar templates según necesidades
4. Agregar más tipos de emails (bienvenida, recordatorios, etc.)
