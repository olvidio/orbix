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
estado_revision: "generado"
---

# Ver Cuadricula Zona

Descripcion funcional pendiente de revisar.

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

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
