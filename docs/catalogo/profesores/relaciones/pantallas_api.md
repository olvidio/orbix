---
tipo: "relacion_pantallas_api"
modulo: "profesores"
pantallas: 6
endpoints_api: 6
capacidades: 6
estado_revision: "generado"
---

# Relacion Pantallas API - profesores

Indice generado automaticamente para cruzar pantallas frontend, capacidades y endpoints API.

## Por Pantalla

### `profesores.pantalla.congresos`

- Controller: `frontend/profesores/controller/congresos.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/profesores/congresos`

Capacidades:
- `profesores.congresos.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `profesores.pantalla.docencia`

- Controller: `frontend/profesores/controller/docencia.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/profesores/docencia`

Capacidades:
- `profesores.docencia.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `profesores.pantalla.ficha_profesor_stgr`

- Controller: `frontend/profesores/controller/ficha_profesor_stgr.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/profesores/ficha_profesor_stgr`

Capacidades:
- `profesores.ficha_profesor_stgr.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `profesores.pantalla.lista_por_departamentos`

- Controller: `frontend/profesores/controller/lista_por_departamentos.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/profesores/lista_por_departamentos`

Capacidades:
- `profesores.lista_por_departamentos.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `profesores.pantalla.profesor_asignatura_ajax`

- Controller: `frontend/profesores/controller/profesor_asignatura_ajax.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/profesores/profesor_asignatura_ajax`

Capacidades:
- `profesores.profesor_asignatura_ajax.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

### `profesores.pantalla.profesor_asignatura_que`

- Controller: `frontend/profesores/controller/profesor_asignatura_que.php`
- Subtipo: `fragmento_ajax`

Endpoints directos:
- `/src/profesores/profesor_asignatura_que`

Capacidades:
- `profesores.profesor_asignatura_que.gestionar`

Endpoints aportados por capacidades:
- Ninguno adicional.

## Por Endpoint API

### `/src/profesores/congresos`

Pantallas directas:
- `profesores.pantalla.congresos`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/profesores/docencia`

Pantallas directas:
- `profesores.pantalla.docencia`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/profesores/ficha_profesor_stgr`

Pantallas directas:
- `profesores.pantalla.ficha_profesor_stgr`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/profesores/lista_por_departamentos`

Pantallas directas:
- `profesores.pantalla.lista_por_departamentos`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/profesores/profesor_asignatura_ajax`

Pantallas directas:
- `profesores.pantalla.profesor_asignatura_ajax`

Pantallas via capacidad:
- Ninguna adicional.

### `/src/profesores/profesor_asignatura_que`

Pantallas directas:
- `profesores.pantalla.profesor_asignatura_que`

Pantallas via capacidad:
- Ninguna adicional.

## Alertas De Revision

Endpoints sin pantalla directa detectada:
- Ninguno.

Endpoints sin pantalla directa ni capacidad relacionada:
- Ninguno — los endpoints listados arriba se consumen via AJAX, forms `.phtml`/`.twig` o login; ver `docs/REPASSO_FINAL.md` § B.

## Revision Manual

- Repaso 2026-05-21: huerfanos aceptados como patron normal Orbix (PostRequest / fetch desde vista).

## Revision Manual

- Confirmar si los endpoints sin pantalla directa se usan desde fragmentos AJAX no enlazados.
- Revisar pantallas que dependen de varias capacidades.
- Completar relaciones si hay navegacion generada dinamicamente o desde menus.
