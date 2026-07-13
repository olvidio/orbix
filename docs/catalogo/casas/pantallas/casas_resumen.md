---
id: "casas.pantalla.casas_resumen"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "casas"
nombre: "Casas Resumen"
controller: "frontend/casas/controller/casas_resumen.php"
vistas: ["frontend/casas/view/casas_resumen.phtml"]
fragmentos_frontend: ["frontend/casas/controller/casas_resumen_lista.php"]
endpoints: []
capacidades: []
campos: ["html.buscar", "post.sfsv", "post.tipo"]
acciones: ["fnjs_detalles", "fnjs_mas_casas", "fnjs_ver"]
estado_revision: "revisado"
---

# Casas Resumen

Pantalla `casa_resumen`: filtro casa + periodo y carga AJAX del resumen económico.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/casas/controller/casas_resumen.php`

## Vistas Relacionadas

- `frontend/casas/view/casas_resumen.phtml`

## Fragmentos Frontend Relacionados

- `frontend/casas/controller/casas_resumen_lista.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `html.buscar`
- `post.sfsv`
- `post.tipo`

## Acciones Detectadas

- `fnjs_detalles`
- `fnjs_mas_casas`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
## Ruta de menú

- **Legacy:** adl > Gestión casas > estadística  por casas
- **Pills2:** CASAS Y CTR > Gestión casas > estadística  por casas

