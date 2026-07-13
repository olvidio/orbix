---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividades"
titulo: "Filtros listado CSV San Rafael"
pantalla: "actividades.pantalla.lista_sr_csv_que"
preguntas: ["Que se puede hacer en Filtros listado CSV San Rafael?", "Que campos tiene Filtros listado CSV San Rafael?", "Que acciones hay en Filtros listado CSV San Rafael?"]
capacidades: ["actividades.lista_sr_csv_que.gestionar"]
endpoints: ["/src/actividades/lista_sr_csv_que_datos"]
source: "docs/catalogo/actividades/pantallas/lista_sr_csv_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Filtros listado CSV San Rafael

## Resumen

Formulario para el **listado CSV de actividades SR**: periodo (`PeriodoQue`), selección múltiple de casas (`CasasQue`), tipos de actividad y estados. Al cargar consulta `lista_sr_csv_que_datos` para valores por defecto (preferencias del usuario). El action apunta a `lista_sr_csv.php`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.c_activ`
- `form.empiezamax`
- `form.empiezamin`
- `form.id_cdc_mas`
- `form.id_cdc_num`
- `form.periodo`
- `form.status`
- `form.year`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividades.lista_sr_csv_que.gestionar`

## Endpoints Relacionados

- `/src/actividades/lista_sr_csv_que_datos`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
