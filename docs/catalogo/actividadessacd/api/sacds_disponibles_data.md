---
id: "actividadessacd.sacds_disponibles_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacds_disponibles_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacds_disponibles_data.php"
entrada: ["post.id_activ:integer", "post.seleccion:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_SacdsDisponiblesDataData"
respuesta_data: ["id_activ:integer", "sacds_ctr:array", "sacds_todos:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php", "frontend/actividadessacd/view/activ_sacd.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SacdsDisponiblesData"]
tags: ["actividadessacd", "sacds", "disponibles", "data"]
estado_revision: "revisado"
---

# Sacds Disponibles Data

Devuelve los sacd candidatos para asignar a una actividad: los titulares de los centros encargados
de la actividad (`sacds_ctr`) y el listado global filtrado por la máscara de selección (`sacds_todos`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- `sacds_ctr`: solo si `encargossacd` está instalada y `id_activ > 0`. Recorre los centros encargados
  de la actividad (`num_orden`), y por cada centro busca su encargo (`^1(00|100|200|300)`) y el
  `EncargoSacd` titular vigente (`modo 2|3`, `f_fin` nulo); añade su sacd con `num_orden`.
- `sacds_todos`: lista de personas sacd según la máscara `seleccion`
  (`PersonaSacdRepository::getSacdsBySelect`).

## Endpoint

- URL: `/src/actividadessacd/sacds_disponibles_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacds_disponibles_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller (`inputInt`) | No | Actividad; `<= 0` deja `sacds_ctr` vacío |
| `seleccion` | `integer` | controller (`inputInt`) | No | Máscara de bits: 2 = prelatura, 4 = de paso, 8 = sss+, 16 = cp (suma de los checkboxes) |

El controller construye `$input` con `id_activ` y `seleccion`.

## Salida

- Helper: `ContestarJson::enviar('', $useCase->execute($input))` — `data` es el payload serializado
  como string JSON; el front hace un segundo `JSON.parse`.
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadessacd_SacdsDisponiblesDataData`):
  - `id_activ` (`integer`)
  - `sacds_ctr` (`array`): titulares de los centros encargados, `{id_nom (int), ap_nom (string), num_orden (int)}`.
  - `sacds_todos` (`array`): sacd globales según `seleccion`, `{id_nom (int), ap_nom (string)}`.

## Permisos

- El caso de uso no aplica control de permisos propio (solo comprueba `is_app_installed('encargossacd')`).
  La autorización se resuelve en el frontend (`activ_sacd.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\actividadessacd\application\SacdsDisponiblesData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php` (emite `url_disponibles`).
- `frontend/actividadessacd/view/activ_sacd.phtml` (`fnjs_nuevo_sacd` construye la máscara `seleccion` con los checkboxes y pinta el desplegable).
