---
id: "personas.personas_select_data"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/personas_select_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/personas/infrastructure/ui/http/controllers/personas_select_data.php"
entrada: ["post.apellido1:string", "post.apellido2:string", "post.centro:string", "post.cmb:string", "post.es_sacd:integer", "post.exacto:string", "post.na:string", "post.nombre:string", "post.tabla:string", "post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra el usuario", "No se encuentra ningún centro con esta condición"]
frontend_referencias: ["frontend/personas/controller/personas_select.php"]
casos_uso: ["src\\personas\\application\\PersonasSelectData"]
tags: ["personas", "select", "data", "lista"]
estado_revision: "revisado"
---

# Personas Select Data

Devuelve los datos crudos para montar la tabla `personas_select` tras una búsqueda en
`personas_que`. No genera HTML: el frontend instancia `web\Lista` con filas, botones y
cabeceras según `tabla`, permisos y módulos instalados.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Ejecuta la consulta de personas según el colectivo (`tabla`) y filtros de nombre/apellidos/centro.
Ramas principales:

- **Usuario PAU_NOM** (`ConfigGlobal::mi_role_pau()`): fija `id_nom` del usuario logado y deduce
  `tabla` a partir de su `id_tabla` (n/s/x/a/pa/pn); no aplica filtros de búsqueda libre.
- **Búsqueda normal**: construye `$aWhere` con prefijos `^` y operador `sin_acentos` si no hay
  `exacto`; filtra `situacion=A` salvo que `cmb` esté marcado y el usuario no tenga permiso `dtor`
  (entonces excluye situación `B`); opcional `es_sacd=1` y filtro por centros vía `centro`.
- **Colectivos** (`switch tabla`): `p_numerarios`, `p_agregados`, `p_supernumerarios`, `p_nax`,
  `p_sssc`, `p_de_paso`/`p_de_paso_ex` (con `na` → `id_tabla=p{na}`), `p_cp_ae_sssc` no está en
  switch → cae en `nada` si no coincide. Si el filtro de centro no devuelve ctr → `tabla=nada`.
- Avisos suaves de región STGR (`RegionStgrAviso`) se devuelven en `aviso` sin cortar el listado;
  el controller convierte algunos `error` suaves en `aviso`.

## Endpoint

- URL: `/src/personas/personas_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/personas/infrastructure/ui/http/controllers/personas_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tabla` | `string` | application | No | Colectivo: `p_numerarios`, `p_agregados`, `p_supernumerarios`, `p_nax`, `p_sssc`, `p_de_paso`, `p_de_paso_ex`, `nada` |
| `na` | `string` | application | No | Para de paso: `n`, `a`, `s`, `x`, `sss` → filtra `id_tabla=p{na}` |
| `tipo` | `string` | application | No | Si `planning`, no incluye columna `nivel_stgr` en filas n/agd |
| `es_sacd` | `integer` | application | No | `1` filtra `sacd=t` |
| `exacto` | `string` | application | No | Vacío → búsqueda con prefijo `^` sin acentos |
| `cmb` | `string` | application | No | Marcado → incluye situación y fecha en filas; afecta filtro situación |
| `nombre` | `string` | application | No | Campo `nom` en repositorio |
| `apellido1` | `string` | application | No | |
| `apellido2` | `string` | application | No | |
| `centro` | `string` | application | No | Resuelve `id_ctr` vía centros; `+` → `.` en exacto |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en el front).
- Forma: `standard_envelope_string_data`.
- Claves del payload: `tabla`, `obj_pau` (`PersonaN`, `PersonaAgd`, `PersonaS`, `PersonaSSSC`,
  `PersonaNax`, `PersonaEx`), `id_tabla`, `permiso` (1 lectura / 3 edición según oficina),
  `sPrefs` (preferencia usuario `tabla_presentacion`), `total`, `personas[]` con
  `id_nom`, `id_tabla`, `nom`, `nombre_ubi`, opcional `nivel_stgr`, `situacion`, `f_situacion`,
  y opcional `aviso` (problemas región STGR).
- Errores duros en `mensaje`; avisos suaves de región/persona no válida en `aviso` con listado vacío.

## Permisos

- `permiso=3` si `$_SESSION['oPerm']->have_perm_oficina(...)` según colectivo: `sm` (n),
  `agd` (agd), `sg` (s), `des` (sssc), `nax` (nax), o cualquiera de `sm|agd|des|sg|est` en de paso.
- Resto: `permiso=1`. Sin `perm_*` propio en el caso de uso.

## Errores conocidos

- `No se encuentra el usuario` (modo PAU_NOM sin `MiUsuario`)
- `No se encuentra ningún centro con esta condición` (filtro centro sin resultados)
- Avisos suaves (no error duro): persona no válida, delegaciones sin región STGR, etc.

## Casos De Uso

- `src\personas\application\PersonasSelectData`

## Frontend Relacionado

- `frontend/personas/controller/personas_select.php` (PostRequest tras `personas_que`)
