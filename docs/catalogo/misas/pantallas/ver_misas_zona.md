---
id: "misas.pantalla.ver_misas_zona"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Ver Misas Zona"
controller: "frontend/misas/controller/ver_misas_zona.php"
vistas: []
fragmentos_frontend: ["frontend/misas/controller/ver_misas_zona.php"]
endpoints: ["/src/misas/ver_misas_zona_data"]
capacidades: ["misas.ver_misas_zona.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_zona", "post.seleccion"]
acciones: []
estado_revision: "revisado"
---

# Ver misas zona

Consulta de misas por zona y fechas (solo lectura). Sin entrada de menú en el índice; acceso vía enlaces internos.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/misas/controller/ver_misas_zona.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_misas_zona.php`

## Endpoints Usados

- `/src/misas/ver_misas_zona_data`

## Capacidades Relacionadas

- `misas.ver_misas_zona.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_zona`
- `post.seleccion`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
