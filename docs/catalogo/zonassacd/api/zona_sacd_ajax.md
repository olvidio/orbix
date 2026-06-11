---
id: "zonassacd.zona_sacd_ajax"
tipo: "endpoint"
modulo: "zonassacd"
url: "/src/zonassacd/zona_sacd_ajax"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_ajax.php"
entrada: []
entrada_obligatoria: []
respuesta: "pendiente_revision"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["zonassacd", "zona", "sacd", "ajax"]
estado_revision: "revisado"
---

# Zona Sacd Ajax

**Ruta muerta** (confirmado jun 2026): `routes.php` registra la ruta pero el
controller `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_ajax.php`
**no existe**. Era el dispatcher legacy (`que=get_lista|update|get_lista_tot`),
sustituido por `zona_sacd_lista`, `zona_sacd_update` y `zona_sacd_lista_tot`.
Pendiente: eliminar la ruta de `src/zonassacd/config/routes.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/zonassacd/zona_sacd_ajax`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/zonassacd/infrastructure/ui/http/controllers/zona_sacd_ajax.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

No se ha detectado salida estandar. Revisar manualmente.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Revisado jun 2026: ruta sin controller; ver descripcion.