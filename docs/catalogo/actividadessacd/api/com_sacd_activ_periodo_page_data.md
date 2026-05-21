---
id: "actividadessacd.com_sacd_activ_periodo_page_data"
tipo: "endpoint"
modulo: "actividadessacd"
url: "/src/actividadessacd/com_sacd_activ_periodo_page_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadessacd/infrastructure/ui/http/controllers/com_sacd_activ_periodo_page_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividadessacd/controller/com_sacd_activ_periodo.php"]
casos_uso: ["src\\actividadessacd\\application\\ComSacdActivPeriodoPageData"]
tags: ["actividadessacd", "com", "sacd", "activ", "periodo", "page", "data"]
estado_revision: "generado"
---

# Com Sacd Activ Periodo Page Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadessacd/com_sacd_activ_periodo_page_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadessacd/infrastructure/ui/http/controllers/com_sacd_activ_periodo_page_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividadessacd\application\ComSacdActivPeriodoPageData`

## Frontend Relacionado

- `frontend/actividadessacd/controller/com_sacd_activ_periodo.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.