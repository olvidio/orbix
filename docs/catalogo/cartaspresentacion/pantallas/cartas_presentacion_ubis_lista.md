---
id: "cartaspresentacion.pantalla.cartas_presentacion_ubis_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cartaspresentacion"
nombre: "Cartas Presentacion Ubis Lista"
controller: "frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/cartaspresentacion/ubis_lista_data"]
capacidades: ["cartaspresentacion.ubis.gestionar"]
campos: ["post.poblacion_sel", "post.tipo_lista"]
acciones: ["fnjs_modificar", "fnjs_ver_ubi", "fnjs_eliminar_cp"]
estado_revision: "revisado"
---

# Cartas Presentacion Ubis Lista

Fragmento AJAX: tabla de centros con estado de carta de presentación (sí/no) y acciones por fila.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php`

## Vistas Relacionadas

No tiene vista PHTML; genera HTML con `frontend\shared\web\Lista`.

## Endpoints Usados

- `/src/cartaspresentacion/ubis_lista_data`

## Campos Detectados

- `post.tipo_lista` — `get_dl` o `get_r`
- `post.poblacion_sel` — filtro de población (modo `get_dl`)

## Acciones Detectadas

- `fnjs_modificar` — enlace columna «director» (definido en shell padre)
- `fnjs_ver_ubi` — enlace nombre del centro
- `fnjs_eliminar_cp` — enlace «quitar» si la carta existe

## Manual De Usuario

Se carga en `#ficha2` al pulsar **buscar** en la pantalla principal. Cada fila indica si el centro
tiene carta y permite modificarla, ver la ficha del centro o quitar la carta existente.

## Ruta de menú

sin entrada de menú en el índice (fragmento de la pantalla `cartas_presentacion`).
