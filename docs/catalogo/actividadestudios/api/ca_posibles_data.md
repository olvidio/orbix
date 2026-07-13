---
id: "actividadestudios.ca_posibles_data"
tipo: "endpoint"
modulo: "actividadestudios"
url: "/src/actividadestudios/ca_posibles_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividadestudios/infrastructure/ui/http/controllers/ca_posibles_data.php"
entrada: ["post.na:string", "post.id_ctr_n:integer", "post.id_ctr_agd:integer", "post.ca_estudios:string", "post.ca_repaso:string", "post.ca_todos:string", "post.grupo_estudios:string", "post.periodo:string", "post.year:integer", "post.empiezamin:string", "post.empiezamax:string", "post.idca:string", "post.texto:string", "post.ref:string", "post.obj_pau:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["debe seleccionar un centro o grupo de centros", "Parámetro na no válido", "sólo debebería haber uno"]
frontend_referencias: ["frontend/actividadestudios/controller/ca_posibles.php"]
casos_uso: ["src\\actividadestudios\\application\\CaPosiblesData"]
tags: ["actividadestudios", "ca", "posibles", "data"]
estado_revision: "revisado"
---

# Ca Posibles Data

Cuadro de "posibles CA" (centros de estudios) para las personas de un centro/grupo: para cada alumno
y cada CA candidato, calcula los créditos posibles. Misma lógica que `frontend/.../ca_posibles.php`,
en versión serializable. En modo `lista`, la especificación del enlace de página (`pagina_link_spec`)
la firma el front.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Reúne las personas (numerarios `n` o agregados `a`, según `na`) del centro/grupo indicado y las
actividades de tipo CA en el periodo, y para cada par calcula los créditos (`PosiblesCa::contar_creditos`)
según el nivel STGR de la persona y del CA. Devuelve:

- **modo `tabla`** (por defecto): `tabla_filas`, una por centro, con contadores por nivel
  (bienio/cuadrienio/repaso/CE/otros) y el cuadro de personas × actividades.
- **modo `lista`** (cuando `sel` trae una sola persona): detalle de un alumno con `titulo`, `stgr`,
  `aActividades` y `pagina_link_spec` (enlace al dossier `1301y1302`).

## Endpoint

- URL: `/src/actividadestudios/ca_posibles_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividadestudios/infrastructure/ui/http/controllers/ca_posibles_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `na` | `string` | application | No | Tabla de personas: `n` (numerarios) o `a`/`agd` (agregados); se deduce de `id_ctr_n`/`id_ctr_agd` si viene vacío |
| `id_ctr_n` | `integer` | application | No | Centro de numerarios (`1` = todos) |
| `id_ctr_agd` | `integer` | application | No | Centro de agregados (`1` = todos) |
| `ca_estudios` / `ca_repaso` / `ca_todos` | `string` | application | No | Filtro de tipo de CA (estudios / repaso / todos) |
| `grupo_estudios` | `string` | application | No | Grupo de estudios; `todos` evita filtrar por DL |
| `periodo` / `year` / `empiezamin` / `empiezamax` | `string`/`integer` | application | No | Ventana temporal (`Periodo`); `periodo` por defecto `curso_ca` |
| `idca` | `string` | application | No | Si viene, no recalcula la lista de CA |
| `texto` / `ref` / `obj_pau` | `string` | application | No | Propagados a la salida |
| `sel` | `array` | application | No | Tokens `id_nom#…`; activa el modo persona/lista |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Modo `tabla`: `{ modo: "tabla", msg_txt, tabla_filas: [...] }`.
- Modo `lista`: `{ modo: "lista", msg_txt, titulo, stgr, aActividades, pagina_link_spec }`.

## Errores conocidos

- `debe seleccionar un centro o grupo de centros` (si no hay `id_ctr_n` ni `id_ctr_agd`).
- `Parámetro na no válido`.
- `sólo debebería haber uno` (modo lista con más de un centro en el cuadro).

Además, avisos no bloqueantes se acumulan en `msg_txt` (CA sin nivel/asignaturas, centros sin persona, etc.).

## Permisos

- Sin control de permisos propio en el caso de uso; la autorización de oficina se resuelve en el
  frontend (`ca_posibles.php` / `ca_posibles_que.php`) y en `$_SESSION['oPerm']`. No inferir permisos aquí.

## Casos De Uso

- `src\actividadestudios\application\CaPosiblesData`

## Frontend Relacionado

- `frontend/actividadestudios/controller/ca_posibles.php` (invocado desde `ca_posibles_que`).