---
id: "pasarela.pantalla.exportar_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Exportar Select"
controller: "frontend/pasarela/controller/exportar_select.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/pasarela/exportar_actividades_data"]
capacidades: ["pasarela.exportar_actividades.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.iactividad_val", "post.iasistentes_val", "post.id_cdc", "post.id_tipo_activ", "post.isfsv_val", "post.periodo", "post.year"]
acciones: []
estado_revision: "generado"
---

# Exportar Select

Resultado del filtro "exportar actividades": delega el armado del listado en `/src/pasarela/exportar_actividades_data` (caso de uso {@see \src\pasarela\application\ExportarActividadesData}) y solo se ocupa de renderizar la tabla con `frontend\shared\web\Lista`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/exportar_select.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/pasarela/exportar_actividades_data`

## Capacidades Relacionadas

- `pasarela.exportar_actividades.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.iactividad_val`
- `post.iasistentes_val`
- `post.id_cdc`
- `post.id_tipo_activ`
- `post.isfsv_val`
- `post.periodo`
- `post.year`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
