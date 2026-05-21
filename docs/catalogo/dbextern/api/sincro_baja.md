---
id: "dbextern.sincro_baja"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_baja"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_baja.php"
entrada: ["post.dl:string", "post.id_nom_orbix:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio."]
frontend_referencias: ["frontend/dbextern/controller/ver_desaparecidos_de_listas.php"]
casos_uso: ["src\\dbextern\\application\\BajaPersonaUseCase"]
tags: ["dbextern", "sincro", "baja"]
estado_revision: "generado"
---

# Sincro Baja

Da de baja a una persona (fallecido o traslado a otra región).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/sincro_baja`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_baja.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller | No | controller |
| `id_nom_orbix` | `string` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `OJO: Debería cambiar el campo situación. No se ha hecho ningún cambio.`

## Casos De Uso

- `src\dbextern\application\BajaPersonaUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_desaparecidos_de_listas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.