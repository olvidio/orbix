---
id: "usuarios.app_login.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Entrar desde la app móvil"
capacidad: "usuarios.app_login.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/app_login"]
estado_revision: "revisado"
---

# Flujo - App Login

## Objetivo De Usuario

Iniciar sesión desde la aplicación móvil con usuario, contraseña y, si procede, código 2FA.

## Punto De Entrada

Pantalla de acceso de la aplicación móvil.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Entrar desde la app móvil

Pasos propuestos:
1. Abrir la app e indicar usuario, contraseña y esquema.
2. Si el usuario tiene 2FA, escribir el código de verificación.
3. Si falta usuario o contraseña aparece «Usuario y contraseña obligatorios».

Endpoints asociados:
- `/src/usuarios/app_login`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/app_login`

## Errores Conocidos

- `Usuario y contraseña obligatorios`
- `Esquema no indicado`
- `Esquema no válido`
- `Error de autenticación`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
