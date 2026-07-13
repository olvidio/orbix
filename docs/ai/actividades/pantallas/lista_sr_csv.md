---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Listado SR (tabla/CSV)"
pantalla: "actividades.pantalla.lista_sr_csv"
preguntas: ["Que se puede hacer en Listado SR (tabla/CSV)?", "Que campos tiene Listado SR (tabla/CSV)?", "Que acciones hay en Listado SR (tabla/CSV)?"]
capacidades: ["actividades.lista_sr_csv.gestionar"]
endpoints: ["/src/actividades/lista_sr_csv_datos"]
source: "docs/catalogo/actividades/pantallas/lista_sr_csv.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Listado SR (tabla/CSV)

## Resumen

Pantalla de **resultados** del listado CSV de San Rafael: recibe filtros de `lista_sr_csv_que` (periodo, casas `id_cdc`, tipos `c_activ`, estados) y llama a `lista_sr_csv_datos`. Según `que` muestra tabla HTML o fuerza descarga CSV.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.c_activ`
- `post.dl_org`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.que`
- `post.status`
- `post.year`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividades.lista_sr_csv.gestionar`

## Endpoints Relacionados

- `/src/actividades/lista_sr_csv_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
