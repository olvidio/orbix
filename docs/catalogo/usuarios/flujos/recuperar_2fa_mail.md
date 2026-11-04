---
id: "usuarios.recuperar_2fa_mail.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Reiniciar el 2FA por correo"
capacidad: "usuarios.recuperar_2fa_mail.gestionar"
pantallas_principales: ["usuarios.pantalla.recuperar_2fa"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/recuperar_2fa_mail"]
estado_revision: "revisado"
---

# Flujo - Recuperar 2fa Mail

## Objetivo De Usuario

Recuperar el acceso cuando se ha perdido la aplicación de autenticación: envía un enlace por correo para reiniciar el 2FA.

## Punto De Entrada

Enlace «¿Ha perdido acceso a su aplicación de autenticación?» de la pantalla de acceso.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Reiniciar el 2FA por correo

Pasos propuestos:
1. En la pantalla de acceso, pulsar el enlace de ayuda del 2FA.
2. Indicar el login y el esquema.
3. Se envía un correo con un enlace; al usarlo se desactiva el 2FA y hay que configurarlo de nuevo.
4. Si el usuario no tiene correo registrado aparece «No hay email asociado a este usuario»; hay que pedir al administrador que lo restablezca.

Endpoints asociados:
- `/src/usuarios/recuperar_2fa_mail`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/recuperar_2fa_mail`

## Errores Conocidos

- `Esquema no válido`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `No se encontró ningún usuario con ese nombre`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
