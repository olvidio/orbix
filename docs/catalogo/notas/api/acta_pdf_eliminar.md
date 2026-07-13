---
id: "notas.acta_pdf_eliminar"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/acta_pdf_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/acta_pdf_eliminar.php"
entrada: ["post.acta_num:string"]
entrada_obligatoria: ["acta_num"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se encuentra el acta"]
frontend_referencias: []
casos_uso: ["src\\notas\\application\\ActaPdfEliminar"]
tags: ["notas", "acta", "pdf", "eliminar"]
estado_revision: "revisado"
---

# Acta Pdf Eliminar

Elimina el PDF adjunto a un acta.

Elimina el PDF firmado asociado a un `Acta` (sin borrar el acta).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/acta_pdf_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/acta_pdf_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `acta_num` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `success: true`, `data: "ok"`. Error en `mensaje`.

## Efectos colaterales

- Elimina el PDF firmado asociado a un `Acta` (sin borrar el acta).

## Objetivo funcional

Borrado del fichero PDF asociado (`acta_num` = número de acta).

## Permisos

- Desde `acta_ver` con permiso de edición de actas.

## Errores conocidos

- `No se encuentra el acta`

## Casos De Uso

- `src\notas\application\ActaPdfEliminar`

## Frontend Relacionado

- `frontend/notas/controller/acta_pdf_delete.php` → `acta_ver`.