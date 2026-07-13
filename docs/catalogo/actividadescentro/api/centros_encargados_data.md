---
id: "actividadescentro.centros_encargados_data"
tipo: "endpoint"
modulo: "actividadescentro"
url: "/src/actividadescentro/centros_encargados_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadescentro/infrastructure/ui/http/controllers/centros_encargados_data.php"
entrada: ["post.dl_org:string", "post.id_activ:integer", "post.id_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadescentro_CentrosEncargadosDataData"
respuesta_data: ["id_activ:integer", "permite_ver:boolean", "permite_modificar:boolean", "centros:list<array{id_ubi: int, nombre_ubi: string}>"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadescentro/controller/activ_ctr.php"]
casos_uso: ["src\\actividadescentro\\application\\CentrosEncargadosData"]
tags: ["actividadescentro", "centros", "encargados", "data"]
estado_revision: "revisado"
---

# Centros Encargados Data

Devuelve los centros encargados actuales de una actividad y los flags `permite_ver` /
`permite_modificar`, usados por el frontend para repintar la celda de centros tras una mutación
(asignar / reordenar / eliminar).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `get` del dispatcher legacy. Si `id_activ <= 0` devuelve un payload vacío
(`permite_ver`/`permite_modificar` a `false`). En caso contrario resuelve el permiso `ctr` de la
actividad y, solo si permite `ver`, carga la lista de centros encargados ordenados. El frontend usa
`permite_modificar` para decidir si pinta cada centro como enlace (`fnjs_cambiar_ctr`) o como texto.

## Endpoint

- URL: `/src/actividadescentro/centros_encargados_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadescentro/infrastructure/ui/http/controllers/centros_encargados_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller+application | No | Actividad; si `<= 0` devuelve payload vacío |
| `id_tipo_activ` | `string` | controller+application | No | Contexto de permisos; el frontend lo manda vacío |
| `dl_org` | `string` | controller+application | No | Contexto de permisos; el frontend lo manda vacío |

El controller construye el `$input` con los tres campos (`inputInt`/`inputString`).

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadescentro_CentrosEncargadosDataData`):
  - `id_activ` (`integer`): actividad consultada (0 si la entrada era inválida).
  - `permite_ver` (`boolean`) / `permite_modificar` (`boolean`): flags de la faceta `ctr`.
  - `centros` (`list<array{id_ubi: int, nombre_ubi: string}>`): centros encargados (vacío si no hay
    permiso `ver`).

## Permisos

- Resuelve el permiso `ctr` con `PermisosActividades` (`$_SESSION['oPermActividades']`) cuando
  `procesos` está instalado, o con `PermisosActividadesTrue` en caso contrario. `permite_ver` y
  `permite_modificar` son `have_perm_activ('ver')` y `have_perm_activ('modificar')` de esa faceta.

## Casos De Uso

- `src\actividadescentro\application\CentrosEncargadosData`

## Frontend Relacionado

- `frontend/actividadescentro/controller/activ_ctr.php` (vista `activ_ctr.phtml`): la función
  `fnjs_actualizar_activ` invoca este endpoint (URL firmada `url_encargados`) tras asignar, reordenar
  o eliminar, y repinta la celda con `fnjs_construir_celda_ctrs`.
