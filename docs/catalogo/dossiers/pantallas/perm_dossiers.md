---
id: "dossiers.pantalla.perm_dossiers"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dossiers"
nombre: "Perm Dossiers"
controller: "frontend/dossiers/controller/perm_dossiers.php"
vistas: ["frontend/dossiers/view/perm_dossiers.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dossiers/perm_dossiers_data"]
capacidades: ["dossiers.perm_dossiers.gestionar"]
campos: ["post.tipo"]
acciones: ["fnjs_update_div"]
estado_revision: "generado"
---

# Perm Dossiers

Página de selección de los dossiers cuyos permisos deseo visualizar o modificar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dossiers/controller/perm_dossiers.php`

## Vistas Relacionadas

- `frontend/dossiers/view/perm_dossiers.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dossiers/perm_dossiers_data`

## Capacidades Relacionadas

- `dossiers.perm_dossiers.gestionar`

## Campos Detectados

- `post.tipo`

## Acciones Detectadas

- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
