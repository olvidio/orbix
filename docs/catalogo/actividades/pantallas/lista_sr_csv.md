---
id: "actividades.pantalla.lista_sr_csv"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Listado SR (tabla/CSV)"
controller: "frontend/actividades/controller/lista_sr_csv.php"
vistas: ["frontend/actividades/view/lista_sr_csv.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/lista_sr_csv.php"]
endpoints: ["/src/actividades/lista_sr_csv_datos"]
capacidades: ["actividades.lista_sr_csv.gestionar"]
campos: ["post.c_activ", "post.dl_org", "post.empiezamax", "post.empiezamin", "post.id_cdc", "post.periodo", "post.que", "post.status", "post.year"]
acciones: []
estado_revision: "revisado"
---

# Listado SR (tabla/CSV)

Pantalla de **resultados** del listado CSV de San Rafael: recibe filtros de
`lista_sr_csv_que` (periodo, casas `id_cdc`, tipos `c_activ`, estados) y llama a
`lista_sr_csv_datos`. Según `que` muestra tabla HTML o fuerza descarga CSV.

## Tipo

- Subtipo: `pantalla_principal` (nav + tabla en `lista_sr_csv.phtml`)
- Controller: `frontend/actividades/controller/lista_sr_csv.php`

## Endpoints Usados

- `/src/actividades/lista_sr_csv_datos`

## Manual De Usuario

Tras filtrar en `lista_sr_csv_que`, el usuario ve el listado o exporta CSV según
el botón elegido.

## Ruta de menú

Destino del formulario de `lista_sr_csv_que`:

- **Legacy:** vsr > listas actividades > listado csv (resultado).
- **Pills2:** sin entrada dedicada (hereda vsr).
