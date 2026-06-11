---
id: "zonassacd.zona_sacd_lista_tot"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd_lista_tot"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_lista_tot.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\zonassacd\\application\\ZonaSacdListaTot"]
tags: ["zonassacd", "zona", "sacd", "lista", "tot"]
estado_revision: "revisado"
---

# Zona Sacd Lista Tot

Listado **global** de todos los sacd de la delegacion con sus zonas asignadas
(una fila por asignacion, zona propia primero) y el flag `propia` (si/no).
Los sacd sin zona aparecen con zona vacia. Ordenado por apellidos.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/zonassacd/zona_sacd_lista_tot`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_lista_tot.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- `data`: `tipo: "lista"`, `a_cabeceras` (sacd, zona, propia), `a_valores`.

## Casos De Uso

- `src\zonassacd\application\ZonaSacdListaTot`

## Frontend Relacionado

Sin consumidor en `frontend/` (confirmado jun 2026): el menu legacy **Lista sacd-zona**
apuntaba a `zona_sacd_ajax.php?que=get_lista_tot`. Es el endpoint canonico para ese
listado; la pantalla frontend esta pendiente de crear.

## Revision Manual

- Revisado jun 2026 (lectura de `ZonaSacdListaTot::execute`).
- Pendiente: pantalla frontend que lo consuma + ejemplos de request/response.