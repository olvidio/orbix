---
id: "misas.pantalla.modificar_encargos"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Modificar Encargos"
controller: "frontend/misas/controller/modificar_encargos.php"
vistas: ["frontend/misas/view/modificar_encargos.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_encargos_zona.php"]
endpoints: ["/src/misas/modificar_encargos_data"]
capacidades: ["misas.modificar_encargos.gestionar"]
campos: ["form.id_zona", "form.orden"]
acciones: ["fnjs_ver_encargos_zona"]
estado_revision: "revisado"
---

# Modificar encargos

Entry point para CRUD de encargos de zona (grupo ZONAS_MISAS). Selectores zona/orden y grid AJAX `ver_encargos_zona`.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/misas/controller/modificar_encargos.php`

## Vistas Relacionadas

- `frontend/misas/view/modificar_encargos.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_encargos_zona.php`

## Endpoints Usados

- `/src/misas/modificar_encargos_data`

## Capacidades Relacionadas

- `misas.modificar_encargos.gestionar`

## Campos Detectados

- `form.id_zona`
- `form.orden`

## Acciones Detectadas

- `fnjs_ver_encargos_zona`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar encargos
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar encargos
