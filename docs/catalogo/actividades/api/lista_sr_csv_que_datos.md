---
id: "actividades.lista_sr_csv_que_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/lista_sr_csv_que_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_que_datos.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/lista_sr_csv_que.php"]
casos_uso: ["src\\actividades\\application\\ListaSrCsvQueDatos"]
tags: ["actividades", "lista", "sr", "csv", "que", "datos"]
estado_revision: "generado"
---

# Lista Sr Csv Que Datos

Endpoint backend para `lista_sr_csv_que`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/lista_sr_csv_que_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_que_datos.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividades\application\ListaSrCsvQueDatos`

## Frontend Relacionado

- `frontend/actividades/controller/lista_sr_csv_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.