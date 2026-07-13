---
id: "notas.pantalla.informe_stgr_agd"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Informe Stgr Agd"
controller: "frontend/notas/controller/informe_stgr_agd.php"
vistas: ["frontend/notas/view/informe_stgr_tabla.phtml"]
fragmentos_frontend: []
endpoints: ["/src/notas/informe_stgr_agd_data"]
capacidades: ["notas.informe_stgr_agd.gestionar"]
campos: ["post.dl", "post.lista"]
acciones: []
estado_revision: "revisado"
---

# Informe Stgr Agd

Informe anual de agregados: métricas de progreso en planes de estudio.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/informe_stgr_agd.php`

## Vistas Relacionadas

- `frontend/notas/view/informe_stgr_tabla.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/notas/informe_stgr_agd_data`

## Capacidades Relacionadas

- `notas.informe_stgr_agd.gestionar`

## Campos Detectados

- `post.dl`
- `post.lista`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** Calendario > Nuevo calendario > Previsión asistentes (sin lista); variantes lista en ESTUDIOS
- **Pills2:** ACTIVIDADES > Estadísticas económicas > Previsión asistentes; ESTUDIOS > Datos e informes > Informe anual agd > Con números / Con listados

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
