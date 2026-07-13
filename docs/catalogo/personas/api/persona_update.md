---
id: "personas.persona_update"
tipo: "endpoint"
modulo: "personas"
url: "/src/personas/persona_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/personas/infrastructure/ui/http/controllers/persona_update.php"
entrada: ["post.apel_fam:string", "post.apellido1:string", "post.apellido2:string", "post.ce:integer", "post.ce_fin:integer", "post.ce_ini:integer", "post.ce_lugar:string", "post.dl:string", "post.eap:string", "post.edad:string", "post.f_inc:string", "post.f_nacimiento:string", "post.f_situacion:string", "post.id_ctr:integer", "post.id_nom:integer", "post.idioma_preferido:string", "post.inc:string", "post.lugar_nacimiento:string", "post.nivel_stgr:integer", "post.nom:string", "post.nx1:string", "post.nx2:string", "post.obj_pau:string", "post.observ:string", "post.profesion:string", "post.profesor_stgr:string", "post.sacd:string", "post.situacion:string", "post.trato:string"]
entrada_obligatoria: ["post.id_nom", "post.obj_pau"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se ha pasado el id_nom", "No existe la clase de la persona", "No se encuentra la persona", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/personas/view/_persona_form_js.phtml"]
casos_uso: ["src\\personas\\application\\PersonaUpdate"]
tags: ["personas", "persona", "update"]
estado_revision: "revisado"
---

# Persona Update

Guarda (crea o actualiza) los datos de una persona según `obj_pau`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Despacha a `guardarPersonaDl` (N, Agd, Nax, S, SSSC) o `guardarPersonaEx`. Si no existe
registro, crea entidad nueva con `id_nom` e `id_tabla` del POST. Aplica campos comunes
(identidad, situación, STGR, fechas, sacd, inc, observ) y campos DL (`id_ctr`, `ce*`).
`PersonaEx` admite además `edad` y `profesor_stgr`.

## Endpoint

- URL: `/src/personas/persona_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/personas/infrastructure/ui/http/controllers/persona_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | application | Sí | |
| `obj_pau` | `string` | application | Sí | Colectivo destino |
| Resto | varios | application | No | Campos del formulario según plantilla |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar($error_txt, 'ok')`.
- Éxito: `success: true`, `data: "ok"`, `mensaje: ""`.
- Error: `success: false`, mensaje en `mensaje` (puede incluir detalle del repositorio tras salto de línea).

## Permisos

- Sin `perm_*` en el caso de uso. El frontend solo muestra botón guardar si `ok=1` según
  `have_perm_oficina` del colectivo (`sm`, `agd`, `sg`, `des`, `vcsd`, `est`).

## Errores conocidos

- `No se ha pasado el id_nom`
- `No existe la clase de la persona`
- `hay un error, no se ha guardado` (+ detalle `getErrorTxt()` del repositorio)

## Casos De Uso

- `src\personas\application\PersonaUpdate`

## Frontend Relacionado

- `frontend/personas/view/_persona_form_js.phtml` (`fnjs_guardar` → HashFront)
