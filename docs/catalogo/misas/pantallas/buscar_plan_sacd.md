---
id: "misas.pantalla.buscar_plan_sacd"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Buscar Plan Sacd"
controller: "frontend/misas/controller/buscar_plan_sacd.php"
vistas: ["frontend/misas/view/buscar_plan_sacd.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_plan_sacd.php"]
endpoints: ["/src/misas/buscar_plan_sacd_data"]
capacidades: ["misas.buscar_plan_sacd.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.id_sacd", "form.periodo"]
acciones: ["fnjs_ver_plan_sacd"]
estado_revision: "revisado"
---

# Buscar plan sacd

Buscador de plan por sacerdote: desplegable sacd filtrado por rol y rango de fechas.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/misas/controller/buscar_plan_sacd.php`

## Vistas Relacionadas

- `frontend/misas/view/buscar_plan_sacd.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_plan_sacd.php`

## Endpoints Usados

- `/src/misas/buscar_plan_sacd_data`

## Capacidades Relacionadas

- `misas.buscar_plan_sacd.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.id_sacd`
- `form.periodo`

## Acciones Detectadas

- `fnjs_ver_plan_sacd`

## Ruta de menú

- **Legacy:** dre > Misas > Plan sacerdote
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Plan sacerdote
