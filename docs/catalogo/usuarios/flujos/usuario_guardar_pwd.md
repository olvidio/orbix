---
id: "usuarios.usuario_guardar_pwd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Guardar Pwd"
capacidad: "usuarios.usuario_guardar_pwd.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form_pwd"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_guardar_pwd"]
estado_revision: "revisado"
---

# Flujo - Usuario Guardar Pwd

## Objetivo De Usuario

Cambia contraseña tras validar fortaleza; limpia flag cambio_password.

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form_pwd`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_usuario`
- `form.password`
- `form.password1`
- `html.password`
- `html.password1`

Acciones JavaScript:
- `fnjs_chk_passwd`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`

## Endpoints Del Flujo

- `/src/usuarios/usuario_guardar_pwd`

## Errores Conocidos

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
