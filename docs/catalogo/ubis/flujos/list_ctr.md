---
id: "ubis.list_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar List Ctr"
capacidad: "ubis.list_ctr.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.list_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/list_ctr_data"]
estado_revision: "revisado"
---

# Flujo - List Ctr

## Objetivo De Usuario

Lista centros y casas filtrados por delegación/exterior y tipo, con teléfonos y enlaces a ficha.

## Punto De Entrada

Menú Legacy: scdl > direcciones > listados. Pills2: Calendario > centros y casas > listados.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.list_ctr`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.loc`
- `form.que_lista`
- `form.sel`
- `post.loc`
- `post.que_lista`
- `post.stack`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_cerrar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_limpiar`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_trasladar`
- `fnjs_update_div`
- `fnjs_ver_dl`

## Endpoints Del Flujo

- `/src/ubis/list_ctr_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados
