---
id: "usuarios.usuario_2fa_verify.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Verificar el código"
capacidad: "usuarios.usuario_2fa_verify.gestionar"
pantallas_principales: ["usuarios.pantalla.usuario_form_2fa"]
fragmentos: ["usuarios.pantalla.usuario_form_2fa"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_2fa_verify"]
estado_revision: "revisado"
---

# Flujo - Usuario 2fa Verify

## Objetivo De Usuario

Comprobar el código de la aplicación de autenticación antes de activar la verificación en dos pasos.

## Punto De Entrada

Formulario 2FA, paso **Verificación**.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form_2fa`

## Escenarios Inferidos

### Verificar el código

Pasos propuestos:
1. Escanear el QR con la aplicación.
2. Escribir en **Código de verificación** el número que muestra.
3. Si es correcto se permite activar 2FA; si no, aparece «Código de verificación inválido».

Endpoints asociados:
- `/src/usuarios/usuario_2fa_verify`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.enable_2fa`
- `form.secret_2fa`
- `form.verification_code`
- `html.btn_ok`
- `html.enable_2fa`
- `html.id_usuario`
- `html.verification_code`

Acciones JavaScript:
- `fnjs_enviar`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`

## Endpoints Del Flujo

- `/src/usuarios/usuario_2fa_verify`

## Errores Conocidos

- `Código de verificación o clave secreta no válidos`
- `Código de verificación inválido`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
