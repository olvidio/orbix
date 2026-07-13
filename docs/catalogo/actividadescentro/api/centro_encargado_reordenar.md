---
id: "actividadescentro.centro_encargado_reordenar"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/centro_encargado_reordenar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_reordenar.php"
entrada: ["post.id_activ:integer", "post.id_ubi:integer", "post.num_orden:string"]
entrada_obligatoria: ["id_activ", "id_ubi"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_activ / id_ubi", "direccion de orden incorrecta (mas / menos)", "error al ordenar (1)", "error al ordenar (2)", "error al ordenar (3)", "error al ordenar (4)"]
frontend_referencias: ["frontend/actividadescentro/controller/activ_ctr.php"]
casos_uso: ["src\\actividadescentro\\application\\CentroEncargadoReordenar"]
tags: ["actividadescentro", "centro", "encargado", "reordenar"]
estado_revision: "revisado"
---

# Centro Encargado Reordenar

Sube o baja la prioridad de un `CentroEncargado` dentro del listado de centros encargados de una
actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la función `ordena()` y de la rama `orden` con `num_orden = mas|menos` del dispatcher
legacy `activ_ctr_ajax.php`. Carga los centros ordenados por `num_orden`, localiza el centro `id_ubi`
e intercambia su `num_orden` con el vecino superior (`mas`) o inferior (`menos`). Es una operación de
dos `UPDATE`; si alguno falla, concatena los mensajes `error al ordenar (N)`.

## Endpoint

- URL: `/src/actividadescentro/centro_encargado_reordenar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centro_encargado_reordenar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | application | Si | Debe ser `> 0` |
| `id_ubi` | `integer` | application | Si | Centro a reordenar; debe ser `> 0` |
| `num_orden` | `string` | application | No | **Dirección**, no un número: solo `mas` o `menos` |

El controller pasa `$_POST` completo al caso de uso. Ojo: pese al nombre, `num_orden` transporta la
dirección del movimiento (`mas` / `menos`), no un valor de orden.

## Salida

- Helper: `ContestarJson::enviar` (el controller pasa `'ok'` como data literal).
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"`.
- En error de negocio: `success: false`, `mensaje` con el texto (o concatenación) devuelto.

## Efectos colaterales

- Intercambia el `num_orden` de dos `CentroEncargado` (dos `UPDATE`). Si el centro ya está en el
  extremo correspondiente, no hace nada y devuelve éxito.

## Errores conocidos

- `faltan parametros id_activ / id_ubi`
- `direccion de orden incorrecta (mas / menos)`
- `error al ordenar (1)` … `error al ordenar (4)` (fallo al guardar uno de los dos centros del swap)

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización se resuelve en el frontend (la
  acción solo se ofrece si `permite_modificar` / `perm_modificar_ctr`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadescentro\application\CentroEncargadoReordenar`

## Frontend Relacionado

- `frontend/actividadescentro/controller/activ_ctr.php` (vista `activ_ctr.phtml`): la función
  `fnjs_reordenar` invoca este endpoint (URL firmada `url_reordenar`) desde el popup `+ prioridad` /
  `- prioridad` y refresca la celda con `fnjs_actualizar_activ`.
