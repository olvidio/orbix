---
id: "certificados.certificado_recibido_guardar"
tipo: "endpoint"
modulo: "certificados"
url: "/src/certificados/certificado_recibido_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/certificados/infrastructure/ui/http/controllers/certificado_recibido_guardar.php"
entrada: ["post.certificado:string", "post.certificado_old:string", "post.destino:string", "post.f_certificado:string", "post.f_recibido:string", "post.firmado:string", "post.id_item:integer", "post.id_nom:integer", "post.idioma:string", "post.nom:string", "post.nuevo:integer"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_recibido_modificar.php"]
casos_uso: []
tags: ["certificados", "certificado", "recibido", "guardar"]
estado_revision: "revisado"
---

# Certificado Recibido Guardar

Alta o edición de metadatos de un certificado recibido en la región STGR local.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Análogo a `certificado_emitido_guardar` pero sobre `CertificadoRecibido` y con `f_recibido` en
lugar de `f_enviado`. Borra PDF temporal si cambia `certificado_old`.

## Endpoint

- URL: `/src/certificados/certificado_recibido_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/certificados/infrastructure/ui/http/controllers/certificado_recibido_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `nuevo` | `integer` | controller | No | `1` = alta |
| `id_item` | `integer` | controller | No | Edición |
| `id_nom` | `integer` | controller | Sí | Persona |
| `nom`, `idioma`, `destino`, `certificado`, `firmado` | varios | controller | No | Metadatos |
| `f_certificado`, `f_recibido` | `string` | controller | No | Fechas locales |
| `certificado_old` | `string` | controller | No | Limpieza PDF tmp |

## Salida

- Helper: `ContestarJson::enviar`
- Éxito: `data` = `{mensaje: "ok", item: <id_item>}` (doble `JSON.parse`)

## Errores conocidos

- `No se encuentra el certificado`
- Errores de BD del repositorio en `mensaje`

## Permisos

- Sin control de permisos propio; formularios en dossier de persona o modificar recibido.

## Casos De Uso

- Lógica inline en el controller.

## Frontend Relacionado

- `frontend/certificados/controller/certificado_recibido_modificar.php`
