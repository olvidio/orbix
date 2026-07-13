---
id: "asistentes.pantalla.tabla_peticiones"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Tabla Peticiones"
controller: "frontend/asistentes/controller/tabla_peticiones.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/asistentes/tabla_peticiones_data"]
capacidades: ["asistentes.tabla_peticiones.gestionar"]
campos: ["post.id_activ_old", "post.sel"]
acciones: []
estado_revision: "revisado"
---

# Tabla Peticiones

Tabla de peticiones de plaza por asistente; enlaces para mover a actividad preferida.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/tabla_peticiones.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/tabla_peticiones_data`

## Capacidades Relacionadas

- `asistentes.tabla_peticiones.gestionar`

## Campos Detectados

- `post.id_activ_old`
- `post.sel`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pantalla revisada contra `frontend/asistentes/`.
## Ruta de menú

- sin entrada de menú en el índice (acceso desde dossier actividad/persona, `actividad_que` o navegación embebida).
