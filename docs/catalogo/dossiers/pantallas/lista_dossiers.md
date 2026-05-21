---
id: "dossiers.pantalla.lista_dossiers"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dossiers"
nombre: "Lista Dossiers"
controller: "frontend/dossiers/controller/lista_dossiers.php"
vistas: ["frontend/dossiers/view/lista_dossiers.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dossiers/dossiers_lista_fichas_data"]
capacidades: ["dossiers.dossiers_lista_fichas.gestionar"]
campos: []
acciones: ["fnjs_update_div"]
estado_revision: "generado"
---

# Lista Dossiers

Include desde 'home_persona.phtml' y 'home_ubis.phtml' (variables $pau, $id_pau, $Qobj_pau).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dossiers/controller/lista_dossiers.php`

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

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
