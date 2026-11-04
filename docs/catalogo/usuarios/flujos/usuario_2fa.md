---
id: "usuarios.usuario_2fa.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Activar la verificación en dos pasos"
capacidad: "usuarios.usuario_2fa.gestionar"
pantallas_principales: ["usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_reset_2fa"]
fragmentos: ["usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_reset_2fa"]
acciones: ["crear_actualizar"]
endpoints: ["/src/usuarios/usuario_2fa_update"]
estado_revision: "revisado"
---

# Flujo - Usuario 2fa

## Objetivo De Usuario

Activar o desactivar la verificación en dos pasos (2FA) de un usuario.

## Punto De Entrada

Ficha del usuario (¿tiene doble factor activado?) o preferencias personales.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_reset_2fa`

## Escenarios Inferidos

### Activar la verificación en dos pasos

Pasos propuestos:
1. Descargar una aplicación de autenticación (Google Authenticator, Microsoft Authenticator o Authy).
2. Escanear el **Código QR** o escribir la **Clave secreta**.
3. Escribir el **Código de verificación** que muestra la aplicación y marcar **Activar autenticación de dos factores**.
4. Guardar. Desde ese momento, en cada acceso se pide contraseña y código.
5. Si falta el código aparece «Se requiere un código de verificación para activar 2FA».

Endpoints asociados:
- `/src/usuarios/usuario_2fa_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.enable_2fa`
- `form.secret_2fa`
- `form.verification_code`
- `html.btn_ok`
- `html.enable_2fa`
- `html.id_usuario`
- `html.verification_code`
- `post.id_usuario`

Acciones JavaScript:
- `fnjs_enviar`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`

## Endpoints Del Flujo

- `/src/usuarios/usuario_2fa_update`

## Errores Conocidos

- `Usuario no encontrado`
- `Se requiere un código de verificación para activar 2FA`
- `Código de verificación inválido`
- `Hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
