---
id: "dbextern.sincro_desunir"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_desunir"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_desunir.php"
entrada: ["post.id_nom_listas:integer", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encontró el registro a desunir", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/dbextern/controller/ver_desaparecidos_de_orbix.php"]
casos_uso: ["src\\dbextern\\application\\DesunirPersonaUseCase"]
tags: ["dbextern", "sincro", "desunir"]
estado_revision: "generado"
---

# Sincro Desunir

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/sincro_desunir`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_desunir.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom_listas` | `integer` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encontró el registro a desunir`
- `hay un error, no se ha eliminado`

## Casos De Uso

- `src\dbextern\application\DesunirPersonaUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_desaparecidos_de_orbix.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.