---
id: "usuarios.usuario_preferencias.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Preferencias"
capacidad: "usuarios.usuario_preferencias.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.preferencias"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_preferencias"]
estado_revision: "revisado"
---

# Flujo - Usuario Preferencias

## Objetivo De Usuario

Carga datos iniciales de la pantalla preferencias.

## Punto De Entrada

Menú Legacy: menú usuario > preferencias. Pills2: menú usuario > preferencias.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.preferencias`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
