---
id: "encargossacd.pantalla.sacd_ausencias"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "encargossacd"
nombre: "Sacd Ausencias"
controller: "frontend/encargossacd/controller/sacd_ausencias.php"
vistas: ["frontend/encargossacd/view/sacd_ausencias.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/sacd_ausencias_get.php"]
endpoints: []
capacidades: []
campos: ["form.filtro_sacd", "form.historial", "form.id_nom", "post.filtro_sacd"]
acciones: ["fnjs_horario", "fnjs_lista_sacd", "fnjs_ver_ficha"]
estado_revision: "generado"
---

# Sacd Ausencias

Ficha de ausencias de un sacd.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/encargossacd/controller/sacd_ausencias.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/sacd_ausencias.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/sacd_ausencias_get.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.filtro_sacd`
- `form.historial`
- `form.id_nom`
- `post.filtro_sacd`

## Acciones Detectadas

- `fnjs_horario`
- `fnjs_lista_sacd`
- `fnjs_ver_ficha`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
