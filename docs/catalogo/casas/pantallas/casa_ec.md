---
id: "casas.pantalla.casa_ec"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "casas"
nombre: "Casa Ec"
controller: "frontend/casas/controller/casa_ec.php"
vistas: ["frontend/casas/view/casa_ec.phtml"]
fragmentos_frontend: ["frontend/casas/controller/casas_resumen_lista.php"]
endpoints: []
capacidades: []
campos: ["html.buscar"]
acciones: ["fnjs_detalles", "fnjs_mas_casas", "fnjs_ver"]
estado_revision: "revisado"
---

# Casa Ec

Pantalla `casa_ec`: filtro casa y carga AJAX de la estadística económica por año (5 años).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/casas/controller/casa_ec.php`

## Vistas Relacionadas

- `frontend/casas/view/casa_ec.phtml`

## Fragmentos Frontend Relacionados

- `frontend/casas/controller/casas_resumen_lista.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `html.buscar`

## Acciones Detectadas

- `fnjs_detalles`
- `fnjs_mas_casas`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
## Ruta de menú

- **Legacy:** adl > Gestión casas > estadística por años
- **Pills2:** CASAS Y CTR > Gestión casas > estadística por años

