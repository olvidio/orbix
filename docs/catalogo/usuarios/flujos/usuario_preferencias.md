---
id: "usuarios.usuario_preferencias.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Abrir las preferencias"
capacidad: "usuarios.usuario_preferencias.gestionar"
pantallas_principales: ["usuarios.pantalla.preferencias"]
fragmentos: ["usuarios.pantalla.preferencias"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_preferencias"]
estado_revision: "revisado"
---

# Flujo - Usuario Preferencias

## Objetivo De Usuario

Cargar los valores actuales de las preferencias personales al abrir la pantalla.

## Punto De Entrada

Menú de usuario > **preferencias**.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.preferencias`

## Escenarios Inferidos

### Abrir las preferencias

Pasos propuestos:
1. Abrir **preferencias** en el menú de usuario.
2. La pantalla muestra los valores que ya tiene guardados el usuario.

Endpoints asociados:
- `/src/usuarios/usuario_preferencias`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.estilo_color`
- `form.idioma_nou`
- `form.inicio`
- `form.layout`
- `form.oficina`
- `form.ordenApellidos`
- `form.tipo_menu`
- `form.tipo_tabla`
- `form.zona_horaria_nou`

Acciones JavaScript:
- `button:guardar preferencias`
- `fnjs_guardar_preferencias`
- `fnjs_left_side_hide`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/usuarios/usuario_preferencias`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias
