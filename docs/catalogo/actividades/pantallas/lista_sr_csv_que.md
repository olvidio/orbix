---
id: "actividades.pantalla.lista_sr_csv_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Filtros listado CSV San Rafael"
controller: "frontend/actividades/controller/lista_sr_csv_que.php"
vistas: ["frontend/actividades/view/lista_sr_csv_que.html.twig"]
fragmentos_frontend: ["frontend/actividades/controller/lista_sr_csv.php"]
endpoints: ["/src/actividades/lista_sr_csv_que_datos"]
capacidades: ["actividades.lista_sr_csv_que.gestionar"]
campos: ["form.c_activ", "form.empiezamax", "form.empiezamin", "form.id_cdc_mas", "form.id_cdc_num", "form.periodo", "form.status", "form.year", "post.empiezamax", "post.empiezamin", "post.periodo", "post.year"]
acciones: []
estado_revision: "revisado"
---

# Filtros listado CSV San Rafael

Formulario para el **listado CSV de actividades SR**: periodo (`PeriodoQue`),
selección múltiple de casas (`CasasQue`), tipos de actividad y estados. Al cargar
consulta `lista_sr_csv_que_datos` para valores por defecto (preferencias del
usuario). El action apunta a `lista_sr_csv.php`.

## Tipo

- Subtipo: `pantalla_principal` (`ViewNewTwig`)
- Controller: `frontend/actividades/controller/lista_sr_csv_que.php`

## Endpoints Usados

- `/src/actividades/lista_sr_csv_que_datos` — bootstrap de defaults

## Manual De Usuario

Elegir casas, periodo, tipos y estados; buscar o exportar CSV en la pantalla de
resultados.

## Ruta de menú

- **Legacy:** vsr > listas actividades > listado csv.
- **Pills2:** sin entrada dedicada en el índice (misma ruta vsr).
