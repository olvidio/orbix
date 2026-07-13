---
id: "misas.pantalla.modificar_plan_de_misas"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Modificar Plan De Misas"
controller: "frontend/misas/controller/modificar_plan_de_misas.php"
vistas: ["frontend/misas/view/modificar_plan_de_misas.phtml"]
fragmentos_frontend: ["frontend/misas/controller/modificar_cuadricula_zona.php", "frontend/misas/controller/modificar_plan_de_misas.php"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
capacidades: ["misas.plan_de_misas_pantalla.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.id_zona", "form.orden", "form.periodo", "form.tipo_plantilla"]
acciones: ["fnjs_modificar_cuadricula_zona"]
estado_revision: "revisado"
---

# Modificar plan de misas

Formulario para editar plan existente: zona, orden y rango de fechas. Carga cuadrícula `ver_cuadricula_zona`.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/misas/controller/modificar_plan_de_misas.php`

## Vistas Relacionadas

- `frontend/misas/view/modificar_plan_de_misas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/modificar_cuadricula_zona.php`
- `frontend/misas/controller/modificar_plan_de_misas.php`

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

## Acciones Detectadas

- `fnjs_modificar_cuadricula_zona`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plan
