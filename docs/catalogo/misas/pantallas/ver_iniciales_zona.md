---
id: "misas.pantalla.ver_iniciales_zona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Ver Iniciales Zona"
controller: "frontend/misas/controller/ver_iniciales_zona.php"
vistas: ["frontend/misas/view/ver_iniciales_zona.phtml"]
fragmentos_frontend: []
endpoints: ["/src/misas/update_iniciales", "/src/misas/ver_iniciales_zona_data"]
capacidades: ["misas.update_iniciales.gestionar", "misas.ver_iniciales_zona.gestionar"]
campos: ["form.color", "form.id_sacd", "form.iniciales", "post.id_zona"]
acciones: ["fnjs_generarNomEnc"]
estado_revision: "generado"
---

# Ver Iniciales Zona

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/misas/controller/ver_iniciales_zona.php`

## Vistas Relacionadas

- `frontend/misas/view/ver_iniciales_zona.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/misas/update_iniciales`
- `/src/misas/ver_iniciales_zona_data`

## Capacidades Relacionadas

- `misas.update_iniciales.gestionar`
- `misas.ver_iniciales_zona.gestionar`

## Campos Detectados

- `form.color`
- `form.id_sacd`
- `form.iniciales`
- `post.id_zona`

## Acciones Detectadas

- `fnjs_generarNomEnc`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
