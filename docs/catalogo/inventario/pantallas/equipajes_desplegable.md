---
id: "inventario.pantalla.equipajes_desplegable"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Equipajes Desplegable"
controller: "frontend/inventario/controller/equipajes_desplegable.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_equipajes_desde_fecha"]
capacidades: ["inventario.lista_equipajes_desde_fecha.gestionar"]
campos: ["post.eliminar", "post.filtro", "post.imprimir"]
acciones: ["fnjs_ver_1", "fnjs_ver_2"]
estado_revision: "generado"
---

# Equipajes Desplegable

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_desplegable.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_equipajes_desde_fecha`

## Capacidades Relacionadas

- `inventario.lista_equipajes_desde_fecha.gestionar`

## Campos Detectados

- `post.eliminar`
- `post.filtro`
- `post.imprimir`

## Acciones Detectadas

- `fnjs_ver_1`
- `fnjs_ver_2`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
