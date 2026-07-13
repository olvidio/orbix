---
id: "asistentes.que_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Que Ctr"
capacidad: "asistentes.que_ctr.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.que_ctr_lista"]
acciones: ["listar"]
endpoints: ["/src/asistentes/que_ctr_lista_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Que Ctr

Flujo revisado contra código en `src/asistentes/` y `frontend/asistentes/`.

## Objetivo De Usuario

Filtrar por centro y periodo antes de listados por centros.


## Punto De Entrada

Pantalla `que_ctr_lista` (`frontend/asistentes/controller/`).


## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.que_ctr_lista`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/asistentes/que_ctr_lista_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.btn_ok`
- `html.n_agd`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_otro`

## Endpoints Del Flujo

- `/src/asistentes/que_ctr_lista_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** Varias entradas según `lista`/`sactividad` (vsm/vest/dagd > crt/ca/cv > list por ctr; dre > personas > pendientes)
- **Pills2:** ACTIVIDADES > Listados > Listado de asistentes ca/crt por ctr, Mejores ca para n/agd, Listado de personas sin ca/crt
