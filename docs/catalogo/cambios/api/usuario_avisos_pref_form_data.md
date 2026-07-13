---
id: "cambios.usuario_avisos_pref_form_data"
tipo: "endpoint"
modulo: "cambios"
url: "/src/cambios/usuario_avisos_pref_form_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/cambios/infrastructure/ui/http/controllers/usuario_avisos_pref_form_data.php"
entrada: ["post.id_item_usuario_objeto:integer", "post.id_usuario:integer", "post.quien:string", "post.salida:string", "post.sel:array"]
entrada_obligatoria: ["id_usuario"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cambios/controller/usuario_avisos_pref.php"]
casos_uso: ["src\\cambios\\application\\UsuarioAvisosPrefFormData"]
tags: ["cambios", "usuario", "avisos", "pref", "form", "data"]
estado_revision: "revisado"
---

# Usuario Avisos Pref Form Data

Bootstrap del formulario `usuario_avisos_pref` (configurar aviso para usuario o grupo).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Con `salida=nuevo` inicializa valores por defecto; con `salida=modificar` carga el
`CambioUsuarioObjetoPref` existente. Resuelve usuario vs grupo (`id_usuario` que empieza por `4` =
usuario, si no = grupo). Emite opciones de objetos, tipos de aviso, fases/estados, casas, flags de
permiso y especificaciones `hash_*`/`paths` para los sub-endpoints AJAX.

## Endpoint

- URL: `/src/cambios/usuario_avisos_pref_form_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/cambios/infrastructure/ui/http/controllers/usuario_avisos_pref_form_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_usuario` | `integer` | controller+application | Sí | Usuario o grupo |
| `salida` | `string` | controller+application | No | `nuevo` / `modificar` |
| `id_item_usuario_objeto` | `integer` | controller+application | No | Obligatorio en `modificar` |
| `quien` | `string` | controller+application | No | Contexto del llamador |
| `sel` | `array` | controller | No | Alternativa para extraer ids |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload amplio: `nombre`, `grupo`, `aObjetos`, `aTiposAviso`, `aFases`, `aOpcionesCasas`,
  preselección (`objeto`, `id_tipo_activ`, `aviso_tipo`, flags `aviso_*`), `perm_jefe`,
  `hash_main`, `paths`, `hash_ajax_fases`, `hash_ajax_propiedades`, `hash_ajax_mod`.

## Errores conocidos

- `falta id_usuario`
- `usuario/grupo no encontrado`
- `preferencia no encontrada`

## Permisos

- `perm_jefe` según: `is_jefeCalendario()`, permisos oficina `des`/`vcsd` (sv), rol `PAU_CDC`/`PAU_SACD`,
  permiso oficina `calendario`. Sin bloqueo duro en el caso de uso.

## Casos De Uso

- `src\cambios\application\UsuarioAvisosPrefFormData`

## Frontend Relacionado

- `frontend/cambios/controller/usuario_avisos_pref.php`: carga inicial;
  `UsuarioAvisosPrefFormRender::enrich` resuelve URLs absolutas.
