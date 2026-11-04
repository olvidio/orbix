---
id: "usuarios.recuperar_password_mail.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Recuperar la contraseña por correo"
capacidad: "usuarios.recuperar_password_mail.gestionar"
pantallas_principales: ["usuarios.pantalla.recuperar_password"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/recuperar_password_mail"]
estado_revision: "revisado"
---

# Flujo - Recuperar Password Mail

## Objetivo De Usuario

Recuperar el acceso cuando se ha olvidado la contraseña: envía por correo una contraseña temporal.

## Punto De Entrada

Enlace de ayuda de la pantalla de acceso.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Recuperar la contraseña por correo

Pasos propuestos:
1. En la pantalla de acceso, pulsar el enlace de ayuda para recuperar la contraseña.
2. Indicar el login.
3. El sistema envía al correo del usuario una contraseña temporal y marca que debe cambiarla al entrar.
4. Con la temporal, iniciar sesión y elegir una contraseña nueva.

Endpoints asociados:
- `/src/usuarios/recuperar_password_mail`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/recuperar_password_mail`

## Errores Conocidos

- `Esquema no válido`
- `Error al preparar la consulta`
- `Error al ejecutar la consulta`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `Error al actualizar la contraseña`
- `No se encontró ningún usuario con ese nombre`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
