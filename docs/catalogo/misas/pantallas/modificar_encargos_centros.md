---
id: "misas.pantalla.modificar_encargos_centros"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Modificar Encargos Centros"
controller: "frontend/misas/controller/modificar_encargos_centros.php"
vistas: ["frontend/misas/view/modificar_encargos_centros.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_encargos_centros.php"]
endpoints: ["/src/misas/modificar_encargos_centros_data"]
capacidades: ["misas.modificar_encargos_centros.gestionar"]
campos: ["form.id_zona"]
acciones: ["fnjs_ver_encargos_centros"]
estado_revision: "revisado"
---

# Modificar encargos centros

Entry point para vincular encargos de zona con centros (EncargoCtr). Selector de zona y grid `ver_encargos_centros`.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/misas/controller/modificar_encargos_centros.php`

## Vistas Relacionadas

- `frontend/misas/view/modificar_encargos_centros.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_encargos_centros.php`

## Endpoints Usados

- `/src/misas/modificar_encargos_centros_data`

## Capacidades Relacionadas

- `misas.modificar_encargos_centros.gestionar`

## Campos Detectados

- `form.id_zona`

## Acciones Detectadas

- `fnjs_ver_encargos_centros`

## Ruta de menú

- **Legacy:** dre > Misas > Encargos centro
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Encargos ctr
