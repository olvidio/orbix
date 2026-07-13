---
id: "misas.pantalla.misas_index"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Misas Index"
controller: "frontend/misas/controller/misas_index.php"
vistas: ["frontend/misas/view/misas_index.phtml"]
fragmentos_frontend: ["frontend/misas/controller/buscar_plan_ctr.php", "frontend/misas/controller/buscar_plan_sacd.php", "frontend/misas/controller/cambiar_status.php", "frontend/misas/controller/modificar_encargos.php", "frontend/misas/controller/modificar_encargos_centros.php", "frontend/misas/controller/modificar_iniciales_sacd_zona.php", "frontend/misas/controller/modificar_plan_de_misas.php", "frontend/misas/controller/modificar_plantilla.php", "frontend/misas/controller/preparar_plan_de_misas.php", "frontend/misas/controller/ver_plan_de_misas.php"]
endpoints: []
capacidades: []
campos: []
acciones: ["fnjs_update_div"]
estado_revision: "revisado"
---

# Misas index

Índice de navegación del módulo con enlaces HashFront a las 10 pantallas principales (plan, encargos, plantilla, iniciales, status). Sin backend JSON.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/misas/controller/misas_index.php`

## Vistas Relacionadas

- `frontend/misas/view/misas_index.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/buscar_plan_ctr.php`
- `frontend/misas/controller/buscar_plan_sacd.php`
- `frontend/misas/controller/cambiar_status.php`
- `frontend/misas/controller/modificar_encargos.php`
- `frontend/misas/controller/modificar_encargos_centros.php`
- `frontend/misas/controller/modificar_iniciales_sacd_zona.php`
- `frontend/misas/controller/modificar_plan_de_misas.php`
- `frontend/misas/controller/modificar_plantilla.php`
- `frontend/misas/controller/preparar_plan_de_misas.php`
- `frontend/misas/controller/ver_plan_de_misas.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
