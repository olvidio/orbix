---
id: "usuarios.check_first_login_2fa.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Primer acceso sin 2FA"
capacidad: "usuarios.check_first_login_2fa.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/check_first_login_2fa"]
estado_revision: "revisado"
---

# Flujo - Check First Login 2fa

## Objetivo De Usuario

Tras iniciar sesión, llevar al usuario a configurar la verificación en dos pasos si aún no la tiene.

## Punto De Entrada

Automático después de iniciar sesión.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Primer acceso sin 2FA

Pasos propuestos:
1. Iniciar sesión con login y contraseña.
2. Si el usuario no tiene la verificación en dos pasos activa, se le lleva a la pantalla de configuración.
3. Si ya la tiene, entra directamente a la página de inicio.

Endpoints asociados:
- `/src/usuarios/check_first_login_2fa`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/check_first_login_2fa`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
