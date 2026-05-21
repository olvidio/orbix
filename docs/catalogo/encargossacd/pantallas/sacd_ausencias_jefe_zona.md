---
id: "encargossacd.pantalla.sacd_ausencias_jefe_zona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Sacd Ausencias Jefe Zona"
controller: "frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php"
vistas: ["frontend/encargossacd/view/sacd_ausencias_jefe_zona.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/sacd_ausencias_get.php"]
endpoints: ["/src/encargossacd/sacd_ausencias_jefe_zona_data"]
capacidades: ["encargossacd.sacd_ausencias_jefe_zona.gestionar"]
campos: ["form.filtro_sacd", "form.historial", "form.id_nom"]
acciones: ["fnjs_horario", "fnjs_lista_sacd", "fnjs_ver_ficha"]
estado_revision: "generado"
---

# Sacd Ausencias Jefe Zona

Muestra la ficha de ausencias para un jefe de zona / oficial.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/sacd_ausencias_jefe_zona.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/sacd_ausencias_get.php`

## Endpoints Usados

- `/src/encargossacd/sacd_ausencias_jefe_zona_data`

## Capacidades Relacionadas

- `encargossacd.sacd_ausencias_jefe_zona.gestionar`

## Campos Detectados

- `form.filtro_sacd`
- `form.historial`
- `form.id_nom`

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
