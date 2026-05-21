---
id: "misas.pantalla.preparar_plan_de_misas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Preparar Plan De Misas"
controller: "frontend/misas/controller/preparar_plan_de_misas.php"
vistas: ["frontend/misas/view/preparar_plan_de_misas.phtml"]
fragmentos_frontend: ["frontend/misas/controller/crear_nuevo_periodo.php", "frontend/misas/controller/preparar_plan_de_misas.php", "frontend/misas/controller/ver_cuadricula_zona.php"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
capacidades: ["misas.plan_de_misas_pantalla.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.id_zona", "form.orden", "form.periodo", "form.tipo_plantilla", "form.tipoplantilla", "html.preparar"]
acciones: ["button:preparar", "fnjs_nuevo_periodo", "fnjs_ver_cuadricula_zona"]
estado_revision: "generado"
---

# Preparar Plan De Misas

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/preparar_plan_de_misas.php`

## Vistas Relacionadas

- `frontend/misas/view/preparar_plan_de_misas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/crear_nuevo_periodo.php`
- `frontend/misas/controller/preparar_plan_de_misas.php`
- `frontend/misas/controller/ver_cuadricula_zona.php`

## Endpoints Usados

- `/src/misas/plan_de_misas_pantalla_data`

## Capacidades Relacionadas

- `misas.plan_de_misas_pantalla.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.id_zona`
- `form.orden`
- `form.periodo`
- `form.tipo_plantilla`
- `form.tipoplantilla`
- `html.preparar`

## Acciones Detectadas

- `button:preparar`
- `fnjs_nuevo_periodo`
- `fnjs_ver_cuadricula_zona`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
