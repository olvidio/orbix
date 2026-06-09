---
id: "dbextern.sincro_crear"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_crear"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_crear.php"
entrada: ["post.id:integer", "post.id_nom_listas:integer", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no se encontró la persona en la BDU", "no se pudo resolver la delegación de listas", "No existe la clase de la persona", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/dbextern/controller/ver_listas.php"]
casos_uso: ["src\\dbextern\\application\\CrearPersonaDesdeListasUseCase"]
tags: ["dbextern", "sincro", "crear"]
estado_revision: "generado"
---

# Sincro Crear

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/sincro_crear`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_crear.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id` | `integer` | controller | No | controller |
| `id_nom_listas` | `integer` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no se encontró la persona en la BDU`
- `no se pudo resolver la delegación de listas`
- `No existe la clase de la persona`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\dbextern\application\CrearPersonaDesdeListasUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_listas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.