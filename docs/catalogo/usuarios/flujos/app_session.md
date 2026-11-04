---
id: "usuarios.app_session.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Reabrir la app con sesión activa"
capacidad: "usuarios.app_session.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/app_session"]
estado_revision: "revisado"
---

# Flujo - App Session

## Objetivo De Usuario

Al arrancar la app móvil, comprobar si ya hay una sesión abierta para no pedir de nuevo las credenciales.

## Punto De Entrada

Arranque de la aplicación móvil.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Reabrir la app con sesión activa

Pasos propuestos:
1. Abrir la app.
2. Si la sesión sigue vigente entra directamente; si no, muestra la pantalla de acceso.

Endpoints asociados:
- `/src/usuarios/app_session`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/app_session`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
