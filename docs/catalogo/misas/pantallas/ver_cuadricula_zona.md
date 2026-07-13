---
id: "misas.pantalla.ver_cuadricula_zona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Ver Cuadricula Zona"
controller: "frontend/misas/controller/ver_cuadricula_zona.php"
vistas: ["frontend/misas/view/ver_cuadricula_zona.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_cuadricula_zona.php"]
endpoints: ["/src/misas/ver_cuadricula_zona_data"]
capacidades: ["misas.ver_cuadricula_zona.gestionar"]
campos: ["html.grupos_sacd", "post.columna", "post.empiezamax", "post.empiezamin", "post.fila", "post.id_zona", "post.orden", "post.periodo", "post.seleccion", "post.tipo_plantilla"]
acciones: []
estado_revision: "revisado"
---

# Ver cuadricula zona

Cuadrícula SlickGrid de asignaciones EncargoDia (filas sacd × columnas día/encargo). Edición vía `cuadricula_update` y `desplegable_sacd`.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/misas/controller/ver_cuadricula_zona.php`

## Vistas Relacionadas

- `frontend/misas/view/ver_cuadricula_zona.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_cuadricula_zona.php`

## Endpoints Usados

- `/src/misas/ver_cuadricula_zona_data`

## Capacidades Relacionadas

- `misas.ver_cuadricula_zona.gestionar`

## Campos Detectados

- `html.grupos_sacd`
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
