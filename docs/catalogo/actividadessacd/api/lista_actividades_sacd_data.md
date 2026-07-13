---
id: "actividadessacd.lista_actividades_sacd_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/lista_actividades_sacd_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/lista_actividades_sacd_data.php"
entrada: ["post.tipo:string", "post.year:string", "post.periodo:string", "post.empiezamin:string", "post.empiezamax:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_ListaActividadesSacdDataData"
respuesta_data: ["titulo:string", "tipo:string", "inicio_iso:string", "fin_iso:string", "texto_fase_ok_sacd:string", "mostrar_nota_falta_sacd:boolean", "perm_des:boolean", "filas:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/activ_sacd.php", "frontend/actividadessacd/view/activ_sacd.phtml"]
casos_uso: ["src\\actividadessacd\\application\\ListaActividadesSacdData"]
tags: ["actividadessacd", "lista", "actividades", "sacd", "data"]
estado_revision: "revisado"
---

# Lista Actividades Sacd Data

Construye la tabla principal de la pantalla `activ_sacd`: las actividades del tipo + periodo
elegidos, con sus sacd encargados y los flags de permiso por fila.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Resuelve el periodo con `Periodo` (`year`, `periodo`, `empiezamin`, `empiezamax`; por defecto año `next`).
- Selecciona las actividades con `f_ini` en el rango y `status < TERMINADA`, ordenadas por `f_ini`.
  El `tipo` acota por regex de `id_tipo_activ` (`sv`, `na`, `sg`, `sr`, `sssc`, `sf`, `sf_na`,
  `sf_sg`, `sf_sr`) o, si `tipo = falta_sacd`, recupera la descripción de la fase `FASE_OK_SACD`.
- Por cada actividad: filtra por permiso (`ocupado` y `ver`); marca la clase visual (`plaza4` si el
  sacd está aprobado, `wrong-soft` si es proyecto); adjunta los centros encargados al nombre si hay
  permiso `ctr`; y lista los sacd encargados si hay permiso `sacd/ver`.
- En modo `falta_sacd` descarta las actividades ya aprobadas o sin sacd.

## Endpoint

- URL: `/src/actividadessacd/lista_actividades_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/lista_actividades_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo` | `string` | controller (`inputString`) | No | `sv`/`na`/`sg`/`sr`/`sssc`/`sf`/`sf_na`/`sf_sg`/`sf_sr`/`falta_sacd` |
| `year` | `string` | controller (`inputString`) | No | Año del periodo (por defecto `next`) |
| `periodo` | `string` | controller (`inputString`) | No | Selector de periodo (`Periodo`) |
| `empiezamin` | `string` | controller (`inputString`) | No | Límite inferior de `f_ini` |
| `empiezamax` | `string` | controller (`inputString`) | No | Límite superior de `f_ini` |

El controller construye `$input` con estos cinco campos.

## Salida

- Helper: `ContestarJson::enviar('', $useCase->execute($input))` — `data` es el payload serializado
  como string JSON; el front hace un segundo `JSON.parse`.
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadessacd_ListaActividadesSacdDataData`):
  - `titulo` (`string`)
  - `tipo` (`string`)
  - `inicio_iso` / `fin_iso` (`string`): rango del periodo resuelto.
  - `texto_fase_ok_sacd` (`string`): descripción de la fase `FASE_OK_SACD`.
  - `mostrar_nota_falta_sacd` (`boolean`): `true` en modo `falta_sacd`.
  - `perm_des` (`boolean`): permiso de oficina `des`.
  - `filas` (`array`): actividades, cada una `{id_activ, nom_activ, f_ini, f_fin, clase, perm_modificar, perm_crear, sacds}`,
    donde `sacds` es lista de `{id_nom, id_cargo, ap_nom}`.

## Permisos

- Por fila usa `$_SESSION['oPermActividades']` (si `procesos` está instalado):
  `getPermisoActual('datos'|'ctr'|'sacd')` → `have_perm_activ(...)`; si no, `PermisosActividadesTrue`.
  El permiso de oficina `des` proviene de `$_SESSION['oPerm']` (`XPermisos::have_perm_oficina('des')`)
  y viaja como `perm_des`.

## Casos De Uso

- `src\actividadessacd\application\ListaActividadesSacdData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/activ_sacd.php` (emite `url_lista`).
- `frontend/actividadessacd/view/activ_sacd.phtml` (`fnjs_ver` → `fnjs_construir_tabla_lista`).
