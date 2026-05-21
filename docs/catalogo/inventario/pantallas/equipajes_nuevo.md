---
id: "inventario.pantalla.equipajes_nuevo"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Equipajes Nuevo"
controller: "frontend/inventario/controller/equipajes_nuevo.php"
vistas: ["frontend/inventario/view/equipajes_nuevo.phtml"]
fragmentos_frontend: ["frontend/inventario/controller/equipajes_casas_posibles.php", "frontend/inventario/controller/equipajes_form_nuevo.php", "frontend/inventario/controller/equipajes_lista_activ_periodo.php", "frontend/inventario/controller/equipajes_ver.php"]
endpoints: []
capacidades: []
campos: ["post.empiezamax", "post.empiezamin", "post.periodo", "post.year"]
acciones: ["fnjs_cerrar", "fnjs_guardar", "fnjs_nombrar_equipaje", "fnjs_update_div", "fnjs_ver_actividades_casa", "fnjs_ver_casas"]
estado_revision: "generado"
---

# Equipajes Nuevo

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/inventario/controller/equipajes_nuevo.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_nuevo.phtml`

## Fragmentos Frontend Relacionados

- `frontend/inventario/controller/equipajes_casas_posibles.php`
- `frontend/inventario/controller/equipajes_form_nuevo.php`
- `frontend/inventario/controller/equipajes_lista_activ_periodo.php`
- `frontend/inventario/controller/equipajes_ver.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_nombrar_equipaje`
- `fnjs_update_div`
- `fnjs_ver_actividades_casa`
- `fnjs_ver_casas`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
