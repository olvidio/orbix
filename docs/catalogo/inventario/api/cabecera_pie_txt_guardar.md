---
id: "inventario.cabecera_pie_txt_guardar"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/cabecera_pie_txt_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/cabecera_pie_txt_guardar.php"
entrada: ["post.cabecera:string", "post.cabeceraB:string", "post.firma:string", "post.pie:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/cabecera_pie_txt.php"]
casos_uso: []
tags: ["inventario", "cabecera", "pie", "txt", "guardar"]
estado_revision: "revisado"
---

# Guardar textos globales cabecera/pie

Persiste en `cabecera_pie_textos.ini` los textos por defecto de cabecera, cabecera B, firma y pie para impresión de equipajes.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Persiste en `cabecera_pie_textos.ini` los textos por defecto de cabecera, cabecera B, firma y pie para impresión de equipajes.

## Endpoint

- URL: `/src/inventario/cabecera_pie_txt_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/cabecera_pie_txt_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `cabecera` | `string` | POST | No | |
| `cabeceraB` | `string` | POST | No | |
| `firma` | `string` | POST | No | |
| `pie` | `string` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `data: "ok"`. Errores de ConfigMagik en `mensaje` (unidos con `;`).

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/cabecera_pie_txt.php`
