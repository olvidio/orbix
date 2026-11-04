---
id: "ubis.list_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Listar centros o casas"
capacidad: "ubis.list_ctr.gestionar"
pantallas_principales: ["ubis.pantalla.list_ctr"]
fragmentos: ["ubis.pantalla.list_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/list_ctr_data"]
estado_revision: "revisado"
---

# Flujo - List Ctr

## Objetivo De Usuario

Sacar un listado de centros o casas de una delegación o del exterior, con teléfonos y enlaces a la ficha.

## Punto De Entrada

Menú **scdl > direcciones > listados** (también Calendario o dre > centros y casas > listados).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.list_ctr`

## Escenarios Inferidos

### Listar centros o casas

Pasos propuestos:
1. Entrar en **listados**.
2. Elegir **Localización** (la propia dl, otras o «otras») y el **Tipo de ubi**: solo centros de n, todos los ctr de la dl, solo centros de agd, de s, de sss+, obras corporativas, casas de retiros y de cv, colegios o resto de casas cdc.
3. Pulsar **listar**.
4. Pulsar un nombre para abrir su ficha; la tabla admite **Buscar en todas las columnas**.

Endpoints asociados:
- `/src/ubis/list_ctr_data`

### Trasladar centros o casas a otra delegación

Pasos propuestos:
1. En el listado, marcar los centros o casas.
2. Pulsar el botón de trasladar y elegir la delegación destino.
3. Confirmar con **Trasladar** (o **Cancelar**).

Endpoints asociados:
- `/src/ubis/list_ctr_data`

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
