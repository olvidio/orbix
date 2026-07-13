---
id: "actividadessacd.sacds_encargados_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/sacds_encargados_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/sacds_encargados_data.php"
entrada: ["post.id_activ:integer", "post.id_tipo_activ:string", "post.dl_org:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_SacdsEncargadosDataData"
respuesta_data: ["id_activ:integer", "permite_ver:boolean", "permite_modificar:boolean", "sacds:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php", "frontend/actividadessacd/view/activ_sacd.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SacdsEncargadosData"]
tags: ["actividadessacd", "sacds", "encargados", "data"]
estado_revision: "revisado"
---

# Sacds Encargados Data

Devuelve los sacd encargados actuales de una actividad (para refrescar la celda tras
asignar/reordenar/borrar) junto con los flags de permiso.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Si `id_activ <= 0`, devuelve payload vacío (`permite_ver = false`, `permite_modificar = false`, `sacds = []`).
- Resuelve el permiso sacd de la actividad (ver más abajo). Solo si `permite_ver`, carga los
  `ActividadCargo` del grupo `sacd` de la actividad y, por cada uno, resuelve el nombre de la persona
  (`Persona::findPersonaEnGlobal` → `getPrefApellidosNombre`).

## Endpoint

- URL: `/src/actividadessacd/sacds_encargados_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/sacds_encargados_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `integer` | controller (`inputInt`) | No | Actividad; `<= 0` devuelve payload vacío |
| `id_tipo_activ` | `string` | controller (`inputString`) | No | Tipo de actividad, para resolver el permiso |
| `dl_org` | `string` | controller (`inputString`) | No | Delegación de origen, para resolver el permiso |

El controller construye `$input` con `id_activ`, `id_tipo_activ` y `dl_org`. El frontend
(`fnjs_actualizar_activ`) los envía con `id_tipo_activ` y `dl_org` vacíos.

## Salida

- Helper: `ContestarJson::enviar('', $useCase->execute($input))` — `data` es el payload serializado
  como string JSON; el front hace un segundo `JSON.parse`.
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadessacd_SacdsEncargadosDataData`):
  - `id_activ` (`integer`)
  - `permite_ver` (`boolean`)
  - `permite_modificar` (`boolean`)
  - `sacds` (`array`): lista de `{id_nom (int), id_cargo (int), ap_nom (string)}`.

## Permisos

- El caso de uso resuelve el permiso sacd con `$_SESSION['oPermActividades']` (si `procesos` está
  instalado): `setActividad(id_activ, id_tipo_activ, dl_org)` → `getPermisoActual('sacd')` →
  `have_perm_activ('ver')` / `have_perm_activ('modificar')`. Si no, usa `PermisosActividadesTrue`
  (todo permitido). Los flags `permite_ver` / `permite_modificar` viajan en el payload.

## Casos De Uso

- `src\actividadessacd\application\SacdsEncargadosData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php` (emite `url_encargados`).
- `frontend/actividadessacd/view/activ_sacd.phtml` (`fnjs_actualizar_activ` refresca la celda `<id_activ>_sacds`).
