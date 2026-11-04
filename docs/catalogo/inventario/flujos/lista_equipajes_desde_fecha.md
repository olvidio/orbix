---
id: "inventario.lista_equipajes_desde_fecha.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Cambiar los equipajes que se ven"
capacidad: "inventario.lista_equipajes_desde_fecha.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_desplegable", "inventario.pantalla.equipajes_movimientos_que", "inventario.pantalla.equipajes_ver"]
fragmentos: ["inventario.pantalla.equipajes_desplegable", "inventario.pantalla.equipajes_movimientos_que", "inventario.pantalla.equipajes_ver"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_equipajes_desde_fecha"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Equipajes Desde Fecha

## Objetivo De Usuario

Cargar la lista de equipajes entre los que escoger, según el filtro de fechas.

## Punto De Entrada

Dentro de **hacer equipajes**, **imprimir equipajes** y **eliminar equipajes**: se actualiza al cambiar el filtro.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_desplegable`
- `inventario.pantalla.equipajes_movimientos_que`
- `inventario.pantalla.equipajes_ver`

## Escenarios Inferidos

### Cambiar los equipajes que se ven

Pasos propuestos:
1. Abrir **hacer equipajes** (o imprimir / eliminar).
2. Elegir el filtro: **desde hoy**, **todo el curso** o **todos**.
3. El desplegable **escoger un equipaje** se actualiza con los equipajes de ese periodo.

Endpoints asociados:
- `/src/inventario/lista_equipajes_desde_fecha`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.filtro`
- `form.id_equipaje`
- `form.loc`
- `form.sel`
- `form.texto`
- `post.eliminar`
- `post.filtro`
- `post.imprimir`

Acciones JavaScript:
- `fnjs_actualizar_lista_equipaje`
- `fnjs_add_doc`
- `fnjs_cerrar`
- `fnjs_del_doc`
- `fnjs_docs_libres`
- `fnjs_eliminar_equipaje`
- `fnjs_eliminar_grupo`
- `fnjs_guardar_listado`
- `fnjs_lista_docs`
- `fnjs_mod_texto_equipaje`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`
- `fnjs_nuevo_grupo`
- `fnjs_update_div`
- `fnjs_update_grupo`
- `fnjs_ver_1`
- `fnjs_ver_2`
- `fnjs_ver_docs`
- `fnjs_ver_movimientos`

## Endpoints Del Flujo

- `/src/inventario/lista_equipajes_desde_fecha`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > equipajes > movimientos maletas
- **Pills2:** sin entrada de menú en el índice
