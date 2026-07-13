---
id: "misas.pantalla.ver_plan_de_misas"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Ver Plan De Misas"
controller: "frontend/misas/controller/ver_plan_de_misas.php"
vistas: ["frontend/misas/view/ver_plan_de_misas.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_cuadricula_zona.php", "frontend/misas/controller/ver_plan_de_misas.php"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
capacidades: ["misas.plan_de_misas_pantalla.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.id_zona", "form.orden", "form.periodo", "form.tipo_plantilla"]
acciones: ["fnjs_ver_cuadricula_zona"]
estado_revision: "revisado"
---

# Ver plan de misas

Consulta del plan de misas de una zona en modo solo lectura (cuadrícula sin edición de celdas).

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/misas/controller/ver_plan_de_misas.php`

## Vistas Relacionadas

- `frontend/misas/view/ver_plan_de_misas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_cuadricula_zona.php`
- `frontend/misas/controller/ver_plan_de_misas.php`

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

- `fnjs_ver_cuadricula_zona`

## Ruta de menú

- **Legacy:** dre > Misas > Ver plan zona
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Ver plan zona
