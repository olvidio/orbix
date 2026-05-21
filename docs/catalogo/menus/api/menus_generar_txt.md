---
id: "menus.menus_generar_txt"
tipo: "endpoint"
modulo: "menus"
url: "/src/menus/menus_generar_txt"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/menus/infrastructure/ui/http/controllers/menus_generar_txt.php"
entrada: []
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["menus", "generar", "txt"]
estado_revision: "generado"
---

# Menus Generar Txt

Esta página genera un fichero con todos los textos de los menús que hay en la base de datos, para poder traducirlos por gettex

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/menus/menus_generar_txt`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/menus/infrastructure/ui/http/controllers/menus_generar_txt.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.