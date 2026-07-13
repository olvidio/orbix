---
id: "dossiers.dossiers_ver_pantalla_data"
tipo: "endpoint"
modulo: "dossiers"
url: "/src/dossiers/dossiers_ver_pantalla_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/dossiers/infrastructure/ui/http/controllers/dossiers_ver_pantalla_data.php"
entrada: ["post.clase_info:string", "post.id_activ:integer", "post.id_dossier:string", "post.id_pau:integer", "post.mod:string", "post.modo_curso:integer", "post.obj_pau:string", "post.pau:string", "post.permiso:string", "post.que:string", "post.queSel:string", "post.refresh:integer", "post.restored_id_sel:mixed", "post.restored_scroll_id:integer", "post.scroll_id:integer", "post.sel:mixed", "post.stack:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["clase_info invalida", "No encuentro a nadie con id_nom: <id>", "ubi no encontrada", "actividad no encontrada", "pau desconocido", "El dossier <id> no está disponible (sin widget ni datos configurados en d_tipos_dossiers)."]
frontend_referencias: ["frontend/dossiers/controller/dossiers_ver.php"]
casos_uso: ["src\\dossiers\\application\\DossiersVerPantallaData"]
tags: ["dossiers", "ver", "pantalla", "data"]
estado_revision: "revisado"
---

# Dossiers Ver Pantalla Data

Cuerpo de `dossiers_ver`: datos de cabecera más el listado de carpetas o la(s) ficha(s) del dossier.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye la pantalla de dossiers de una entidad. Resuelve el ámbito (`pau` = `p` persona / `a`
actividad / `u` ubi) y la entidad (`id_pau`), monta la cabecera (`top_data`: nombre, enlaces "ir a
dossiers" / "ir a home") y luego bifurca:

- **Modo lista** (`id_dossier` vacío): delega en `DossiersListaFichasData` y devuelve `lista_a_filas`.
- **Modo ficha** (`id_dossier` presente, admite varios encadenados con `y`, p. ej. `1301y3101`): por
  cada tipo de dossier resuelve un segmento, que puede ser un *widget* de selección
  (`ficha_segmentos` de tipo `select_*`) o una tabla de datos genérica (`datos_tabla`).

Casos especiales de `queSel`/`que` (`activ`, `matriculas`, `asis`, `asig`, `carg`) fuerzan `pau`,
`permiso` y/o `id_dossier` para reutilizar la pantalla desde otras vistas (asistentes, matrículas,
cargos, asignaturas). Si un tipo de actividad de tipo `stgr` tiene mala configuración de región,
devuelve solo el aviso (`aviso`) sin romper.

## Endpoint

- URL: `/src/dossiers/dossiers_ver_pantalla_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/dossiers/infrastructure/ui/http/controllers/dossiers_ver_pantalla_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `pau` | `string` | application | No | Ámbito/tabla: `p` (persona), `a` (actividad), `u` (ubi) |
| `id_pau` | `integer` | application | No | Id de la entidad; puede derivarse del primer token de `sel` (`id_pau#...`) |
| `obj_pau` | `string` | application | No | Colectivo/clase concreta (se autodetecta para personas) |
| `id_dossier` | `string` | application | No | Vacío → modo lista; con valor → modo ficha. Admite encadenado con `y` |
| `sel` | `mixed` | application | No | Array de tokens `id_pau#...`; se ignora cuando `mod` es `nuevo`/`eliminar` |
| `permiso` | `string` | application | No | Permiso a propagar a las fichas; los casos `queSel` lo fijan a `3` |
| `queSel` / `que` | `string` | application | No | Vista reutilizada: `activ`, `matriculas`, `asis`, `asig`, `carg` |
| `id_activ` | `integer` | application | No | Usado en `queSel = matriculas` |
| `mod` | `string` | application | No | `nuevo` / `eliminar` limpian `sel`; `sel_es_asistente` en matrículas |
| `modo_curso` | `integer` | application | No | Se propaga al widget de selección |
| `clase_info` | `string` | application | No | Clase `DatosInfo` urlencoded; si `id_dossier` vacío, deriva `id_dossier`/`pau` |
| `refresh` | `integer` | application | No | `> 0` fuerza usar `id_pau` directo (ignora `sel`) |
| `scroll_id` / `restored_scroll_id` | `integer` | application | No | Restauración de scroll de la lista |
| `id_sel` / `restored_id_sel` | `mixed` | application | No | Restauración de la fila seleccionada |
| `stack` | `string` | application | No | Profundidad de navegación (se sanea a número) |

El controller pasa `$_POST` completo al caso de uso (`build($_POST)`); la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace un segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` incluye siempre `top_data` (`web_icons`, `alt_dossiers`, `txt_dossiers`, `nom_cabecera`,
  `go_dossiers_link_spec`, `go_home_link_spec`) y `modo` (`lista` | `ficha`):
  - **modo `lista`**: `lista_a_filas` (filas de `DossiersListaFichasData`) y `ficha_segmentos: []`.
  - **modo `ficha`**: `ficha_segmentos` con segmentos `select_*` (widgets de selección) o
    `datos_tabla` (con `titulo`, `script_ctx`, `action_tabla_link_spec`, `hash`, `tabla`, `permiso`,
    `ins_traslado_link_spec`).
  - `aviso` (`string`): presente cuando hay problemas de configuración de región `stgr`.
- Los link specs se firman en el frontend (`HashFrontSignedLink::tryFromSpec` / `signFilas`).
- En error, el controller extrae `error` del resultado y responde `success: false`, `mensaje` con el
  texto, y `data` con el resto del payload (incluye `ficha_segmentos: []`).

## Errores conocidos

- `clase_info invalida` (la clase codificada en `clase_info` no existe)
- `No encuentro a nadie con id_nom: <id> ...` (persona no encontrada; incluye traza interna que el frontend recorta)
- `ubi no encontrada`
- `actividad no encontrada`
- `pau desconocido` (valor de `pau` no soportado)
- `El dossier <id> no está disponible (sin widget ni datos configurados en d_tipos_dossiers).`

## Permisos

- El endpoint no aplica un middleware de permisos propio; el `permiso` viaja como parámetro y se
  propaga a las fichas/segmentos. Los casos reutilizados (`queSel`) fijan `permiso = '3'`. La
  autorización efectiva se resuelve en el frontend (`dossiers_ver.php`) y en `$_SESSION['oPerm']`.

## Casos De Uso

- `src\dossiers\application\DossiersVerPantallaData`

## Frontend Relacionado

- `frontend/dossiers/controller/dossiers_ver.php` (firma los link specs, gestiona la navegación por
  stack y renderiza cabecera + lista/segmentos).
