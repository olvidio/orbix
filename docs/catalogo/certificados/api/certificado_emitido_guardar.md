---
id: "certificados.certificado_emitido_guardar"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_emitido_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_emitido_guardar.php"
entrada: ["post.certificado:string", "post.certificado_old:string", "post.destino:string", "post.f_certificado:string", "post.f_enviado:string", "post.firmado:string", "post.id_item:integer", "post.id_nom:integer", "post.idioma:string", "post.nom:string", "post.nuevo:integer"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_2_mpdf.php", "frontend/certificados/controller/certificado_emitido_ver.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoGuardarMessages"]
tags: ["certificados", "certificado", "emitido", "guardar"]
estado_revision: "revisado"
---

# Certificado Emitido Guardar

Alta o edición de metadatos de un certificado emitido (sin subir PDF en esta llamada).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Con `nuevo=1` crea un registro con nuevo `id_item`; en otro caso actualiza el existente. Rellena
`nom` desde la persona global si viene vacío. Borra PDF temporal en `log/tmp/` si cambia
`certificado_old`.

## Endpoint

- URL: `/src/certificados/certificado_emitido_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_emitido_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `nuevo` | `integer` | controller | No | `1` = alta |
| `id_item` | `integer` | controller | No | Obligatorio en edición |
| `id_nom` | `integer` | controller | Sí | Persona |
| `nom` | `string` | controller | No | Se autocompleta si vacío |
| `idioma` | `string` | controller | No | Código locale |
| `destino` | `string` | controller | No | Delegación destino |
| `certificado` | `string` | controller | No | Número de certificado |
| `firmado` | `string` | controller | No | Checkbox |
| `f_certificado` | `string` | controller | No | Fecha local |
| `f_enviado` | `string` | controller | No | Fecha envío; vacío → null |
| `certificado_old` | `string` | controller | No | Para borrar PDF tmp al renumerar |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `data` = objeto `{mensaje: "ok", item: <id_item>}` (doble `JSON.parse`)
- Error de negocio: `success: false`, `mensaje` con texto, `data: "ok"`

## Errores conocidos

- `No se encuentra el certificado` (edición con `id_item` inexistente)
- `Ya existe un certificado emitido para esta persona con la misma fecha de certificado. Cambie la fecha o consulte el listado de certificados ya emitidos.` (duplicado BD)
- Errores crudos de BD vía `CertificadoEmitidoGuardarMessages`

## Permisos

- Sin control de permisos propio en el controller; acceso desde formularios de región STGR.

## Casos De Uso

- `src\certificados\application\CertificadoEmitidoGuardarMessages` (mensajes de error al guardar)

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_2_mpdf.php` (guardar tras imprimir)
- `frontend/certificados/controller/certificado_emitido_ver.php` (modificar metadatos)
