---
id: "misas.pantalla.cambiar_status"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Cambiar Status"
controller: "frontend/misas/controller/cambiar_status.php"
vistas: ["frontend/misas/view/cambiar_status.phtml"]
fragmentos_frontend: ["frontend/misas/controller/cambiar_status.php", "frontend/misas/controller/ver_cuadricula_zona.php"]
endpoints: ["/src/misas/cambiar_status_data", "/src/misas/nuevo_status"]
capacidades: ["misas.cambiar_status.gestionar", "misas.nuevo_status.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.estado", "form.id_zona", "form.orden", "form.periodo", "form.tipo_plantilla", "html.cambiar"]
acciones: ["button:cambiar", "fnjs_nuevo_estado", "fnjs_ver_cuadricula_zona"]
estado_revision: "revisado"
---

# Cambiar status

Pantalla para cambio masivo de estado de encargos en un rango de fechas (`cambiar_status_data`, `nuevo_status`).

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/misas/controller/cambiar_status.php`

## Vistas Relacionadas

- `frontend/misas/view/cambiar_status.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/cambiar_status.php`
- `frontend/misas/controller/ver_cuadricula_zona.php`

## Endpoints Usados

- `/src/misas/cambiar_status_data`
- `/src/misas/nuevo_status`

## Capacidades Relacionadas

- `misas.cambiar_status.gestionar`
- `misas.nuevo_status.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.estado`
- `form.id_zona`
- `form.orden`
- `form.periodo`
- `form.tipo_plantilla`
- `html.cambiar`

## Acciones Detectadas

- `button:cambiar`
- `fnjs_nuevo_estado`
- `fnjs_ver_cuadricula_zona`

## Ruta de menú

- **Legacy:** dre > Misas > Cambiar estado
- **Pills2:** dre > ?110 > Cambiar estado<br>ATENCIÓN SACD > Gestión de misas > Cambiar estado
