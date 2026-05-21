---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Lista Sr Csv"
pantalla: "actividades.pantalla.lista_sr_csv"
preguntas: ["Que se puede hacer en Lista Sr Csv?", "Que campos tiene Lista Sr Csv?", "Que acciones hay en Lista Sr Csv?"]
capacidades: ["actividades.lista_sr_csv.gestionar"]
endpoints: ["/src/actividades/lista_sr_csv_datos"]
source: "docs/catalogo/actividades/pantallas/lista_sr_csv.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Lista Sr Csv

## Resumen

Listado de actividades de SR para exportar como CSV o mostrar en pantalla.

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
