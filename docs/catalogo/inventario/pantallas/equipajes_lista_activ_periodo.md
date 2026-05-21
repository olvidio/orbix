---
id: "inventario.pantalla.equipajes_lista_activ_periodo"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Equipajes Lista Activ Periodo"
controller: "frontend/inventario/controller/equipajes_lista_activ_periodo.php"
vistas: ["frontend/inventario/view/equipajes_lista_activ_periodo.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/equipajes_lista_activ_periodo"]
capacidades: ["inventario.equipajes_lista_activ_periodo.gestionar"]
campos: ["form.sel", "post.empiezamax", "post.empiezamin", "post.fin", "post.id_cdc", "post.inicio", "post.periodo", "post.year"]
acciones: ["fnjs_nombrar_equipaje"]
estado_revision: "generado"
---

# Equipajes Lista Activ Periodo

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_lista_activ_periodo.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_lista_activ_periodo.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/equipajes_lista_activ_periodo`

## Capacidades Relacionadas

- `inventario.equipajes_lista_activ_periodo.gestionar`

## Campos Detectados

- `form.sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_cdc`
- `post.inicio`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_nombrar_equipaje`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
