---
id: "misas.pantalla.modificar_cuadricula_zona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Modificar Cuadricula Zona"
controller: "frontend/misas/controller/modificar_cuadricula_zona.php"
vistas: []
fragmentos_frontend: ["frontend/misas/controller/modificar_cuadricula_zona.php"]
endpoints: ["/src/misas/ver_cuadricula_zona_data"]
capacidades: ["misas.ver_cuadricula_zona.gestionar"]
campos: ["post.columna", "post.empiezamax", "post.empiezamin", "post.fila", "post.id_zona", "post.orden", "post.periodo", "post.seleccion", "post.tipo_plantilla"]
acciones: []
estado_revision: "revisado"
---

# Modificar cuadricula zona

Alias de edición de cuadrícula; comparte vista y endpoints con `ver_cuadricula_zona` en modo modificar.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/misas/controller/modificar_cuadricula_zona.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/modificar_cuadricula_zona.php`

## Endpoints Usados

- `/src/misas/ver_cuadricula_zona_data`

## Capacidades Relacionadas

- `misas.ver_cuadricula_zona.gestionar`

## Campos Detectados

- `post.columna`
- `post.empiezamax`
- `post.empiezamin`
- `post.fila`
- `post.id_zona`
- `post.orden`
- `post.periodo`
- `post.seleccion`
- `post.tipo_plantilla`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
