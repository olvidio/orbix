---
id: "usuarios.usuario_ayuda_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Consultar el correo asociado"
capacidad: "usuarios.usuario_ayuda_info.gestionar"
pantallas_principales: ["usuarios.pantalla.ayuda_2fa_reset", "usuarios.pantalla.ayuda_acceso"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_ayuda_info"]
estado_revision: "revisado"
---

# Flujo - Usuario Ayuda Info

## Objetivo De Usuario

En la pantalla de ayuda de acceso, mostrar el correo (ofuscado) asociado al login y el contacto del administrador regional.

## Punto De Entrada

Pantalla de ayuda de acceso, antes de iniciar sesión.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Consultar el correo asociado

Pasos propuestos:
1. Abrir la ayuda de acceso.
2. Escribir el login válido.
3. Se muestra el correo parcialmente oculto al que llegarán los avisos y el contacto del administrador.
4. Si el login no es válido aparece «Debe ingresar un nombre de usuario válido».

Endpoints asociados:
- `/src/usuarios/usuario_ayuda_info`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/usuario_ayuda_info`

## Errores Conocidos

- `Esquema no válido`
- `Debe ingresar un nombre de usuario válido`
- `No hay email asociado a este usuario`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
