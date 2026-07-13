---
id: "actividadessacd.comunicacion_activ_sacd_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/comunicacion_activ_sacd_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_data.php"
entrada: ["post.que:string", "post.id_nom:integer", "post.propuesta:string", "post.periodo:string", "post.year:string", "post.empiezamin:string", "post.empiezamax:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividadessacd_ComunicacionActividadesSacdDataData"
respuesta_data: ["que:string", "propuesta:string", "mi_dele:string", "lugar_fecha:string", "periodo_txt:string", "sacds:array", "sacds_paso:array"]
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_activ_periodo.php", "frontend/actividadessacd/view/com_sacd_activ_periodo.phtml"]
casos_uso: ["src\\actividadessacd\\application\\ComunicacionActividadesSacdData"]
tags: ["actividadessacd", "comunicacion", "activ", "sacd", "data"]
estado_revision: "revisado"
---

# Comunicacion Activ Sacd Data

Construye el listado de "atención actividades" a comunicar a los sacd en un periodo: por cada sacd,
las actividades en las que participa y los textos de la carta. Incluye, cuando procede, los "sacd de
paso" (`sacds_paso`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Resuelve el contexto (`resolverContexto`): `que` (`nagd` / `sssc` / `un_sacd`), `id_nom`,
  `propuesta` y el periodo. El rol `p-sacd` fuerza `que = un_sacd` sobre el propio usuario; un `sel`
  con token `id_nom#…` también fija `un_sacd`. En `un_sacd` el periodo se amplía a curso completo.
- Si no hay periodo válido, devuelve el payload con `sacds`/`sacds_paso` vacíos y
  `mensaje_periodo = "falta determinar un periodo"`.
- Carga las personas sacd según `que` y construye la estructura de comunicación
  (`ComunicarActividadesSacdService::getArrayComunicacion`): nombre, textos y actividades.
- Salvo en `un_sacd`, calcula además `sacds_paso` (personas sacd `PersonaEx` activas, solo cargos,
  quitando inactivos).

## Endpoint

- URL: `/src/actividadessacd/comunicacion_activ_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/comunicacion_activ_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | application | No | `nagd` (por defecto) / `sssc` / `un_sacd` |
| `id_nom` | `integer` | application | No | Sacd concreto cuando `que = un_sacd` |
| `propuesta` | `string` | application | No | Modo propuesta (relaja el filtro de permisos por actividad) |
| `periodo` | `string` | application | No | Selector de periodo (`Periodo`) |
| `year` | `string` | application | No | Año del periodo |
| `empiezamin` | `string` | application | No | Límite inferior de `f_ini` |
| `empiezamax` | `string` | application | No | Límite superior de `f_ini` |
| `sel` | `array` | application | No | Token `id_nom#…`; si viene, fija el sacd y `que = un_sacd` |

El controller pasa `$_POST` completo al caso de uso (`execute($_POST)`).

## Salida

- Helper: `ContestarJson::enviar('', $useCase->execute($_POST))` — `data` es el payload serializado
  como string JSON; el front hace un segundo `JSON.parse`.
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividadessacd_ComunicacionActividadesSacdDataData`):
  - `que` (`string`), `propuesta` (`string`), `mi_dele` (`string`), `lugar_fecha` (`string`),
    `periodo_txt` (`string`).
  - `sacds` (`array`): por sacd, `{id_nom, nom_ap, txt: {clave=>texto}, actividades: [...]}`.
  - `sacds_paso` (`array`): misma forma, para los sacd de paso (solo si `que != un_sacd`).
  - `mensaje_periodo` (`string`): presente solo cuando falta el periodo (`"falta determinar un periodo"`);
    en ese caso `sacds`/`sacds_paso` van vacíos.

## Permisos

- Sin control propio de acceso, pero el filtrado por actividad usa
  `$_SESSION['oPermActividades']->havePermisoSacd(...)` (salvo en modo `propuesta`). El rol `p-sacd`
  limita el listado al propio sacd. URL firmada con `HashFront`.

## Casos De Uso

- `src\actividadessacd\application\ComunicacionActividadesSacdData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_activ_periodo.php` (emite `url_data`).
- `frontend/actividadessacd/view/com_sacd_activ_periodo.phtml` (`fnjs_ver` → `fnjs_construir_listado`).
