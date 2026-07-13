---
id: "dossiers.pantalla.lista_dossiers"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dossiers"
nombre: "Lista Dossiers"
controller: "frontend/dossiers/controller/dossiers_ver.php"
vistas: ["frontend/dossiers/view/lista_dossiers.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dossiers/dossiers_lista_fichas_data"]
capacidades: ["dossiers.dossiers_lista_fichas.gestionar"]
campos: []
acciones: ["fnjs_update_div"]
estado_revision: "revisado"
---

# Lista Dossiers

Tabla parcial «relación de dossiers» (modo lista de `dossiers_ver`): icono y descripción por tipo; enlaces `href_ver` según permiso (`perm_a` 1 sin acceso, 2 lectura, 3 escritura). Renderizada desde `dossiers_ver.php`, no tiene controller propio.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dossiers/controller/dossiers_ver.php`

## Vistas Relacionadas

- `frontend/dossiers/view/lista_dossiers.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dossiers/dossiers_lista_fichas_data`

## Capacidades Relacionadas

- `dossiers.dossiers_lista_fichas.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_update_div`

## Manual De Usuario

Pantalla revisada contra `frontend/dossiers/` y `src/dossiers/`.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
