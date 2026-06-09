---
id: "inventario.lista_colecciones"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/lista_colecciones"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/lista_colecciones.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "inventario_ColeccionesOpcionesDataData"
respuesta_data: ["a_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/inventario/domain/ListaAgrupar.php"]
casos_uso: ["src\\inventario\\application\\ColeccionesOpcionesData"]
tags: ["inventario", "lista", "colecciones"]
estado_revision: "generado"
---

# Lista Colecciones

Opciones del desplegable de colecciones (`lista_colecciones.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/inventario/lista_colecciones`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/lista_colecciones.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `inventario_ColeccionesOpcionesDataData`):
  - `a_opciones` (`array`)

## Casos De Uso

- `src\inventario\application\ColeccionesOpcionesData`

## Frontend Relacionado

- `frontend/inventario/domain/ListaAgrupar.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.