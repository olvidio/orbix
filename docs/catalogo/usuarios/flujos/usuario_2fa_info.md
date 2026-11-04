---
id: "usuarios.usuario_2fa_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Ver el estado y el QR"
capacidad: "usuarios.usuario_2fa_info.gestionar"
pantallas_principales: ["usuarios.pantalla.usuario_form_2fa"]
fragmentos: ["usuarios.pantalla.usuario_form_2fa"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_2fa_info"]
estado_revision: "revisado"
---

# Flujo - Usuario 2fa Info

## Objetivo De Usuario

Mostrar el estado de la verificación en dos pasos del usuario y los datos para configurarla.

## Punto De Entrada

Formulario 2FA de la ficha de usuario o de las preferencias.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form_2fa`

## Escenarios Inferidos

### Ver el estado y el QR

Pasos propuestos:
1. Abrir la configuración de **autenticación de dos factores (2FA)**.
2. Se muestra si está activada y, si no, el **Código QR** y la **Clave secreta** para configurarla.

Endpoints asociados:
- `/src/usuarios/usuario_2fa_info`

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

- `/src/usuarios/usuario_2fa_info`

## Errores Conocidos

- `Id de usuario no válido`
- `Usuario no encontrado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
