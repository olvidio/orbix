---
id: "asistentes.pantalla.lista_asistentes"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Lista Asistentes"
controller: "frontend/asistentes/controller/lista_asistentes.php"
vistas: ["frontend/asistentes/view/lista_asistentes.phtml"]
fragmentos_frontend: []
endpoints: ["/src/asistentes/lista_asistentes_data"]
capacidades: ["asistentes.lista_asistentes.gestionar"]
campos: []
acciones: []
estado_revision: "revisado"
---

# Lista Asistentes

Listado de asistentes y cargos-asistentes de una actividad concreta.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/lista_asistentes.php`

## Vistas Relacionadas

- `frontend/asistentes/view/lista_asistentes.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/lista_asistentes_data`

## Capacidades Relacionadas

- `asistentes.lista_asistentes.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pantalla revisada contra `frontend/asistentes/`.
## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
