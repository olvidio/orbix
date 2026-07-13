---
id: "usuarios.usuario_2fa_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario 2fa Info"
capacidad: "usuarios.usuario_2fa_info.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form_2fa"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_2fa_info"]
estado_revision: "revisado"
---

# Flujo - Usuario 2fa Info

## Objetivo De Usuario

Estado 2FA del usuario para formulario configuración.

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form_2fa`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
