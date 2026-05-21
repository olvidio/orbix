---
id: "shared.locales_posibles"
tipo: "endpoint"
modulo: "shared"
url: "/src/shared/locales_posibles"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/shared/infrastructure/ui/http/controllers/locales_posibles.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/certificados/controller/certificado_emitido_imprimir.php", "frontend/certificados/controller/certificado_emitido_ver.php", "frontend/usuarios/controller/preferencias.php"]
casos_uso: []
tags: ["shared", "locales", "posibles"]
estado_revision: "generado"
---

# Locales Posibles

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/shared/locales_posibles`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/shared/infrastructure/ui/http/controllers/locales_posibles.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/certificados/controller/certificado_emitido_imprimir.php`
- `frontend/certificados/controller/certificado_emitido_ver.php`
- `frontend/usuarios/controller/preferencias.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.