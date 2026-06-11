---
id: "actividades.actividad_permiso_crear_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/actividad_permiso_crear_datos"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/actividades/infrastructure/ui/http/controllers/actividad_permiso_crear_datos.php"
entrada: ["post.id_tipo_activ:string", "post.dl_propia:string", "get.id_tipo_activ:string", "get.dl_propia:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ActividadPermisoCrearData"
respuesta_data: ["permiso_crear:object|false", "aviso:string"]
requiere_hashb: false
errores: ["Sesión sin permisos de actividades"]
frontend_referencias: ["frontend/actividades/controller/actividad_ver.php"]
casos_uso: []
tags: ["actividades", "actividad", "permiso", "crear", "datos"]
estado_revision: "revisado"
---

# Actividad Permiso Crear Datos

Expone via HTTP `PermisosActividades::getPermisoCrear` (dominio `permisos`),
para que los controllers solo-frontend resuelvan si el usuario puede crear una
actividad de un tipo dado sin contenedor DI. No usa caso de uso de
`application/`: el controller llama directamente al objeto de permisos de la
**sesion** (`$_SESSION['oPermActividades']`).

Forma de `data`:

```json
{
  "permiso_crear": { "of_responsable_txt": "des", "status": 1 },
  "aviso": ""
}
```

- `permiso_crear`: `false` si no hay permiso; si lo hay, objeto con
  `of_responsable_txt` (oficina responsable de la primera fase del proceso) y
  `status` (status inicial que tendria la actividad).
- `aviso`: texto que `getPermisoCrear` emitia por `echo` en el legacy
  (capturado con output buffering).

## Endpoint

- URL: `/src/actividades/actividad_permiso_crear_datos`
- Metodos registrados: `GET, POST` (lee POST y, como fallback, GET)
- Operacion: `consulta` (sobre la sesion; sin efectos persistentes)
- Controller: `src/actividades/infrastructure/ui/http/controllers/actividad_permiso_crear_datos.php`

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_tipo_activ` | `string` | Si (efectivo) | Tipo de actividad a evaluar; se fija en el objeto de permisos de la sesion. |
| `dl_propia` | `string` | No | `'f'`, `'0'` o `'false'` ⇒ evalua para dl externa; cualquier otro valor (o ausencia) ⇒ dl propia. |

## Permisos

- Requiere sesion con `$_SESSION['oPermActividades']` (`PermisosActividades`);
  si falta responde `success: false` con `Sesión sin permisos de actividades`.

## Frontend Relacionado

- `frontend/actividades/controller/actividad_ver.php` — flujo `mod=nuevo` con
  `procesos`: consulta primero `dl_propia='t'` y, segun la oficina responsable,
  vuelve a consultar con `dl_propia='f'`; si ambas fallan, corta con
  "No tiene permiso para crear una actividad de este tipo".

## Revision Manual

- Revisado jun 2026 (lectura del controller + `PermisosActividades::getPermisoCrear`):
  forma real de `data`, semantica de `dl_propia` y dependencia de sesion verificadas.
- Nota: el endpoint **muta el estado de la sesion** (hace `setId_tipo_activ` sobre
  el objeto de permisos compartido) aunque no persiste nada en BD.
