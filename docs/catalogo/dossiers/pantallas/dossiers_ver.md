---
id: "dossiers.pantalla.dossiers_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dossiers"
nombre: "Dossiers Ver"
controller: "frontend/dossiers/controller/dossiers_ver.php"
vistas: ["frontend/dossiers/view/dossiers_ver_top.phtml", "frontend/dossiers/view/lista_dossiers.phtml"]
fragmentos_frontend: []
endpoints: ["/src/dossiers/dossiers_ver_pantalla_data"]
capacidades: ["dossiers.dossiers_ver_pantalla.gestionar"]
campos: []
acciones: ["fnjs_update_div"]
estado_revision: "generado"
---

# Dossiers Ver

Para asegurar que inicia la sesion, y poder acceder a los permisos

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dossiers/controller/dossiers_ver.php`

## Vistas Relacionadas

- `frontend/dossiers/view/dossiers_ver_top.phtml`
- `frontend/dossiers/view/lista_dossiers.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/dossiers/dossiers_ver_pantalla_data`

## Capacidades Relacionadas

- `dossiers.dossiers_ver_pantalla.gestionar`

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
