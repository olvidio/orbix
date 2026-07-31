---
id: "encargossacd.pantalla.propuestas_lista"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "encargossacd"
nombre: "Propuestas Lista"
controller: "frontend/encargossacd/controller/propuestas_lista.php"
vistas: ["frontend/encargossacd/view/propuestas_lista.html.twig"]
fragmentos_frontend: ["frontend/encargossacd/controller/propuestas_ajax.php"]
endpoints: ["/src/encargossacd/opciones_seccion_data", "/src/encargossacd/propuestas_ajax"]
capacidades: []
campos: ["form.filtro_ctr", "form.dedic_m", "form.dedic_t", "form.dedic_v", "html.lista"]
acciones: ["fnjs_lista_propuestas", "fnjs_ver_sacd_posibles", "fnjs_cmb_sacd", "fnjs_info", "fnjs_dedicacion", "fnjs_guardar_horario", "fnjs_cerrar_propuesta_popup"]
estado_revision: "revisado"
---

# Propuestas Lista

Pantalla editable de propuestas: filtro por grupo de centros y tabla HTML de encargos con
titular/suplente/colaboradores, popups de selección SACD, info y dedicación.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/encargossacd/controller/propuestas_lista.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/propuestas_lista.html.twig`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/propuestas_ajax.php`

## Endpoints Usados

- `/src/encargossacd/opciones_seccion_data` (desplegable `filtro_ctr`)
- `/src/encargossacd/propuestas_ajax` (vía FE proxy; ramas `get_lista`, `lista_sacd`, `cmb_sacd`, `info`, `dedicacion`, `dedicacion_update`)

## Capacidades Relacionadas

Ninguna ficha de capacidad aún.

## Campos Detectados

- `form.filtro_ctr`
- `form.dedic_m` / `form.dedic_t` / `form.dedic_v` (popup horario)
- `html.lista` (`#lista`)

## Acciones Detectadas

- `fnjs_lista_propuestas`
- `fnjs_ver_sacd_posibles`
- `fnjs_cmb_sacd`
- `fnjs_info`
- `fnjs_dedicacion`
- `fnjs_guardar_horario`
- `fnjs_cerrar_propuesta_popup`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (acceso desde Encargos > propuestas → «modificar propuestas»)
- **Pills2:** sin entrada de menú en el índice
