---
id: "ubis.pantalla.ubis_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Ubis Lista"
controller: "frontend/ubis/controller/ubis_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/ubis_lista_data"]
capacidades: ["ubis.ubis.gestionar"]
campos: ["post.nombre_ubi"]
acciones: ["fnjs_buscar"]
estado_revision: "revisado"
---

# Ubis Lista

Fragmento HTML de resultados de autocompletado al buscar ubis por nombre.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/ubis/controller/ubis_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/ubis_lista_data`

## Capacidades Relacionadas

- `ubis.ubis.gestionar`

## Campos Detectados

- `post.nombre_ubi`

## Acciones Detectadas

- `fnjs_buscar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
