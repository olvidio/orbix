---
id: "misas.anadir_ctr_tarea"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/anadir_ctr_tarea"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/anadir_ctr_tarea.php"
entrada: ["post.que:string", "post.id_ubi:integer", "post.id_tarea:integer", "post.id_item:integer"]
entrada_obligatoria: ["que"]
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_AnadirCtrTareaData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\misas\\application\\AnadirCtrTarea"]
tags: ["misas", "anadir", "ctr", "tarea"]
estado_revision: "revisado"
errores: ["Error: falta el id_item", "No se encuentra la plantilla %d", "opción no definida en switch en %s, linea %s", "<repositorio getErrorTxt()>"]
---

# Anadir ctr tarea

Añade o elimina una fila de plantilla (centro asociado a tarea) en el editor de plantillas. Rama que=anadir crea Plantilla con semana=-1; rama quitar elimina por id_item.

Linaje: Slice 9 — migrado desde apps/misas/controller/anadir_ctr_tarea.php (zanadir_ctr_tarea es alias).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Añade o elimina una fila de plantilla (centro asociado a tarea) en el editor de plantillas. Rama que=anadir crea Plantilla con semana=-1; rama quitar elimina por id_item.

## Endpoint

- URL: `/src/misas/anadir_ctr_tarea`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/anadir_ctr_tarea.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | application | Si | |
| `id_ubi` | `integer` | application | No | |
| `id_tarea` | `integer` | application | No | |
| `id_item` | `integer` | application | No | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Exito: `success: true`, `data: "ok"` (string vacio serializado).

## Errores conocidos
- `Error: falta el id_item`
- `No se encuentra la plantilla %d`
- `opción no definida en switch en %s, linea %s`
- `<repositorio getErrorTxt()>`

## Permisos

Sin control de permisos propio en casos de uso; autorización vía `IdNomJefeResolver` (rol p-sacd/jefe calendario), rol ctr/sv/sf en planes y frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\misas\application\AnadirCtrTarea`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`[]`).
