---
id: "misas.pantalla.crear_nuevo_periodo"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Crear Nuevo Periodo"
controller: "frontend/misas/controller/crear_nuevo_periodo.php"
vistas: []
fragmentos_frontend: ["frontend/misas/controller/ver_cuadricula_zona.php"]
endpoints: ["/src/misas/crear_nuevo_periodo_data"]
capacidades: ["misas.crear_nuevo_periodo.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_zona", "post.orden", "post.periodo", "post.seleccion", "post.tipoplantilla"]
acciones: []
estado_revision: "revisado"
---

# Crear nuevo periodo

Fragmento que ejecuta `crear_nuevo_periodo_data` y renderiza `ver_cuadricula_zona.phtml` con el nuevo periodo.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/misas/controller/crear_nuevo_periodo.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_cuadricula_zona.php`

## Endpoints Usados

- `/src/misas/crear_nuevo_periodo_data`

## Capacidades Relacionadas

- `misas.crear_nuevo_periodo.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_zona`
- `post.orden`
- `post.periodo`
- `post.seleccion`
- `post.tipoplantilla`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
