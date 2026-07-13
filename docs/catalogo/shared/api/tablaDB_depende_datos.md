---
id: "shared.tablaDB_depende_datos"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/tablaDB_depende_datos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/shared/infrastructure/ui/http/controllers/tablaDB_depende_datos.php"
entrada: ["post.accion:string", "post.clase_info:string", "post.opcion_sel:string", "post.pKeyRepository:string", "post.valor_depende:string"]
entrada_obligatoria: ["clase_info", "valor_depende"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/shared/controller/tablaDB_formulario_ver.php"]
casos_uso: []
tags: ["shared", "tablaDB", "depende", "datos"]
estado_revision: "revisado"
---

# TablaDB Depende Datos

Refresca opciones de un desplegable dependiente en formularios `tablaDB`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Cuando el usuario cambia un campo padre (`onchange` → `fnjs_actualizar_depende`), recalcula las
`<option>` del hijo llamando a `getOpcionesParaCondicion` del `Info*` concreto (p. ej. `id_ubi` →
`id_lugar` en inventario).

## Endpoint

- URL: `/src/shared/tablaDB_depende_datos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/shared/infrastructure/ui/http/controllers/tablaDB_depende_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clase_info` | `string` | controller | Sí | Clase `Info*` URL-encoded. |
| `valor_depende` | `string` | controller | Sí | Valor del campo padre seleccionado. |
| `accion` | `string` | frontend | No | Id del desplegable hijo a refrescar (`fnjs_actualizar_depende`). |
| `pKeyRepository` | `string` | controller | No | Primer argumento de `getOpcionesParaCondicion` (nombre leído en controller). |
| `opcion_sel` | `string` | controller | No | Valor preseleccionado en el hijo. |

Nota: el JS envía `accion`; el controller lee `pKeyRepository` y `opcion_sel`. En `Info*` con
dependencias el primer parámetro suele coincidir con el nombre del campo hijo.

## Salida

- Helper: `ContestarJson::enviar`
- Payload (doble `JSON.parse`): `{ aOpciones: "<option>…</option>" }` (HTML para innerHTML del
  `<select>` hijo).

## Errores conocidos

- Sin mensajes `_()` en el controller.

## Permisos

- Sin control en el endpoint.

## Casos De Uso

Lógica inline vía `DatosInfoRepoResolver::resolve` → `getOpcionesParaCondicion`.

## Frontend Relacionado

- AJAX de `fnjs_actualizar_depende` en `frontend/shared/view/tablaDB_formulario.phtml`; URL y hash
  configurados en `tablaDB_formulario_ver.php`.
