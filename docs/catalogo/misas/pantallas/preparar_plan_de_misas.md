---
id: "misas.pantalla.preparar_plan_de_misas"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Preparar Plan De Misas"
controller: "frontend/misas/controller/preparar_plan_de_misas.php"
vistas: ["frontend/misas/view/preparar_plan_de_misas.phtml"]
fragmentos_frontend: ["frontend/misas/controller/crear_nuevo_periodo.php", "frontend/misas/controller/preparar_plan_de_misas.php", "frontend/misas/controller/ver_cuadricula_zona.php"]
endpoints: ["/src/misas/plan_de_misas_pantalla_data"]
capacidades: ["misas.plan_de_misas_pantalla.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.id_zona", "form.orden", "form.periodo", "form.tipo_plantilla", "form.tipoplantilla", "html.preparar"]
acciones: ["button:preparar", "fnjs_nuevo_periodo", "fnjs_ver_cuadricula_zona"]
estado_revision: "revisado"
---

# Preparar plan de misas

Formulario para crear un nuevo plan de misas: zona, orden, tipo plantilla y periodo. Redirige a `crear_nuevo_periodo`.

## Tipo

- Subtipo: `pantalla_principal`


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

## Ruta de menú

- **Legacy:** dre > Misas >  Nuevo plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Nuevo plan
