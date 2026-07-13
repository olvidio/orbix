---
id: "procesos.usuario_perm_activ_ajax"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/usuario_perm_activ_ajax"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/procesos/infrastructure/ui/http/controllers/usuario_perm_activ_ajax.php"
entrada: ["post.dl_propia:string", "post.id_tipo_activ:string"]
entrada_obligatoria: ["id_tipo_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/usuario_perm_activ.php"]
casos_uso: ["src\\procesos\\application\\UsuarioPermActivFases"]
tags: ["procesos", "usuario", "perm", "activ", "ajax"]
estado_revision: "revisado"
---

# Usuario Perm Activ Ajax

Opciones del desplegable `fase_ref[]` en permisos de usuario por tipo de actividad.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Al cambiar tipo de actividad o delegación propia, devuelve las fases de los procesos asociados
como mapa `id_fase` → descripción para rellenar los desplegables de fase de referencia.

## Endpoint

- URL: `/src/procesos/usuario_perm_activ_ajax`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/usuario_perm_activ_ajax.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | application | Si | Tipo de actividad |
| `dl_propia` | `string` | application | No | `t`/`f`; procesos propios vs ajenos |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `opciones` (`array<int|string, string>`): mapa fase → etiqueta

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- Sin control de permisos propio; autorización en `usuario_perm_activ.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\UsuarioPermActivFases`

## Frontend Relacionado

- `frontend/procesos/controller/usuario_perm_activ.php` (URL `url_actualizar` al cambiar tipo/DL)
