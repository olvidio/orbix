---
id: "dbextern.refrescar_bdu"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/refrescar_bdu"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/refrescar_bdu.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/sincro_index.php"]
casos_uso: ["src\\dbextern\\application\\RefrescarBduUseCase"]
tags: ["dbextern", "refrescar", "bdu"]
estado_revision: "generado"
---

# Refrescar Bdu

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/refrescar_bdu`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/refrescar_bdu.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\dbextern\application\RefrescarBduUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/sincro_index.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.