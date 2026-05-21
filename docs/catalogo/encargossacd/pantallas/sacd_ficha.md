---
id: "encargossacd.pantalla.sacd_ficha"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "encargossacd"
nombre: "Sacd Ficha"
controller: "frontend/encargossacd/controller/sacd_ficha.php"
vistas: ["frontend/encargossacd/view/sacd_ficha.phtml"]
fragmentos_frontend: []
endpoints: []
capacidades: []
campos: ["post.filtro_sacd"]
acciones: ["fnjs_guardar", "fnjs_lista_sacd", "fnjs_ver_ficha"]
estado_revision: "generado"
---

# Sacd Ficha

Ficha de encargos de un sacd.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/encargossacd/controller/sacd_ficha.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/sacd_ficha.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `post.filtro_sacd`

## Acciones Detectadas

- `fnjs_guardar`
- `fnjs_lista_sacd`
- `fnjs_ver_ficha`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
