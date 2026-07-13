---
id: "encargossacd.pantalla.sacd_ausencias_jefe_zona"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "encargossacd"
nombre: "Sacd Ausencias Jefe Zona"
controller: "frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php"
vistas: ["frontend/encargossacd/view/sacd_ausencias_jefe_zona.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/sacd_ausencias_get.php"]
endpoints: ["/src/encargossacd/sacd_ausencias_jefe_zona_data"]
capacidades: ["encargossacd.sacd_ausencias_jefe_zona.gestionar"]
campos: ["form.filtro_sacd", "form.historial", "form.id_nom"]
acciones: ["fnjs_horario", "fnjs_lista_sacd", "fnjs_ver_ficha"]
estado_revision: "revisado"
---

# Sacd Ausencias Jefe Zona

Muestra la ficha de ausencias para un jefe de zona / oficial.

## Tipo

- Subtipo: `pantalla_principal`
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

## Ruta de menú

- **Legacy:** exterior > sacd > Misas > Ausencias
- **Pills2:** sin entrada de menú en el índice

## Ruta de menú

- **Legacy:** exterior > sacd > Misas > Ausencias
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** exterior > sacd > Misas > Ausencias
- **Pills2:** sin entrada de menú en el índice

