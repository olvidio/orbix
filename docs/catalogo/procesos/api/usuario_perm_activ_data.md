---
id: "procesos.usuario_perm_activ_data"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/usuario_perm_activ_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/procesos/infrastructure/ui/http/controllers/usuario_perm_activ_data.php"
entrada: ["post.dl_propia:mixed", "post.id_tipo_activ_txt:string", "post.id_usuario:integer"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/usuario_perm_activ.php"]
casos_uso: ["src\\procesos\\application\\UsuarioPermActivData"]
tags: ["procesos", "usuario", "perm", "activ", "data"]
estado_revision: "revisado"
---

# Usuario Perm Activ Data

Datos para la pantalla de permisos de usuario por tipo de actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye el formulario de permisos `PermUsuarioActividad` para un usuario y tipo de actividad:
selector de tipo (HTML), fases disponibles, acciones, ámbitos (`afecta_a`) y filas de permisos
existentes (`aPerm`). Calcula `perm_jefe` según rol/ oficinas para habilitar búsqueda extendida
de tipos.

## Endpoint

- URL: `/src/procesos/usuario_perm_activ_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/usuario_perm_activ_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | application | Si | Usuario cuyos permisos se editan |
| `id_tipo_activ_txt` | `string` | application | No | Código tipo actividad; vacío fuerza `dl_propia=t` |
| `dl_propia` | `mixed` | application | No | Booleano; normalizado a `t`/`f` |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `nombre` (`string`): etiqueta del usuario
  - `dl_propia` (`string`): `t` o `f`
  - `perm_jefe` (`bool`): habilita selector extendido de tipos
  - `tipo_actividad_html` (`string`): widget `ActividadTipo`
  - `a_fases` (`array`): fases del proceso del tipo
  - `a_acciones` (`array`): etiquetas de `PermAccionBits`
  - `a_afecta_a` (`array`): ámbitos de permiso
  - `aPerm` (`list`): filas con `afecta_a`, `num`, `fase_ref`, `perm_on`, `perm_off`, `marcado`

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- `perm_jefe` según `is_jefeCalendario`, oficinas `des`/`vcsd` (con SFSV=1) o `calendario` en
  `$_SESSION['oPerm']`; no bloquea la petición si no cumple.

## Casos De Uso

- `src\procesos\application\UsuarioPermActivData`

## Frontend Relacionado

- `frontend/procesos/controller/usuario_perm_activ.php` (carga inicial vía `PostRequest::getDataFromUrl`)
- `frontend/usuarios/view/perm_activ_lista.phtml` (navegación hacia `usuario_perm_activ.php`)
