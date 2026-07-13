---
id: "misas.pantalla.modificar_iniciales_sacd_zona"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "misas"
nombre: "Modificar Iniciales Sacd Zona"
controller: "frontend/misas/controller/modificar_iniciales_sacd_zona.php"
vistas: ["frontend/misas/view/modificar_iniciales_sacd_zona.phtml"]
fragmentos_frontend: ["frontend/misas/controller/ver_iniciales_zona.php"]
endpoints: ["/src/misas/modificar_iniciales_sacd_zona_data"]
capacidades: ["misas.modificar_iniciales_sacd_zona.gestionar"]
campos: ["form.id_zona"]
acciones: ["fnjs_ver_iniciales_sacd_zona"]
estado_revision: "revisado"
---

# Modificar iniciales sacd zona

Entry point para editar iniciales y color de sacerdotes por zona. Selector de zona y carga AJAX de `ver_iniciales_zona`.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/misas/controller/modificar_iniciales_sacd_zona.php`

## Vistas Relacionadas

- `frontend/misas/view/modificar_iniciales_sacd_zona.phtml`

## Fragmentos Frontend Relacionados

- `frontend/misas/controller/ver_iniciales_zona.php`

## Endpoints Usados

- `/src/misas/modificar_iniciales_sacd_zona_data`

## Capacidades Relacionadas

- `misas.modificar_iniciales_sacd_zona.gestionar`

## Campos Detectados

- `form.id_zona`

## Acciones Detectadas

- `fnjs_ver_iniciales_sacd_zona`

## Ruta de menú

- **Legacy:** dre > Misas > Iniciales sacd
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Iniciales sacd
