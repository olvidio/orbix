---
id: "usuarios.usuario_guardar_mail.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Cambiar el correo"
capacidad: "usuarios.usuario_guardar_mail.gestionar"
pantallas_principales: ["usuarios.pantalla.usuario_form_mail"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_guardar_mail"]
estado_revision: "revisado"
---

# Flujo - Usuario Guardar Mail

## Objetivo De Usuario

Cambiar el correo electrónico de un usuario.

## Punto De Entrada

Preferencias personales (menú usuario > preferencias) o ficha de usuario en la administración.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Cambiar el correo

Pasos propuestos:
1. Abrir la ficha del usuario (o las preferencias personales).
2. Escribir el nuevo correo en **Email** y pulsar **guardar**.
3. El correo se usa para la recuperación de contraseña y de la verificación en dos pasos.

Endpoints asociados:
- `/src/usuarios/usuario_guardar_mail`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/usuario_guardar_mail`

## Errores Conocidos

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias
