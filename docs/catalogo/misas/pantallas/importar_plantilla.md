---
id: "misas.pantalla.importar_plantilla"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Importar Plantilla"
controller: "frontend/misas/controller/importar_plantilla.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/misas/importar_plantilla_data"]
capacidades: ["misas.importar_plantilla.gestionar"]
campos: ["post.id_zona", "post.tipo_plantilla_destino", "post.tipo_plantilla_origen"]
acciones: []
estado_revision: "revisado"
---

# Importar plantilla

Fragmento AJAX que copia asignaciones entre tipos de plantilla (`importar_plantilla_data`).

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/misas/controller/importar_plantilla.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/misas/importar_plantilla_data`

## Capacidades Relacionadas

- `misas.importar_plantilla.gestionar`

## Campos Detectados

- `post.id_zona`
- `post.tipo_plantilla_destino`
- `post.tipo_plantilla_origen`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
