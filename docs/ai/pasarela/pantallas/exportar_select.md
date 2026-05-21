---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "pasarela"
titulo: "Exportar Select"
pantalla: "pasarela.pantalla.exportar_select"
preguntas: ["Que se puede hacer en Exportar Select?", "Que campos tiene Exportar Select?", "Que acciones hay en Exportar Select?"]
capacidades: ["pasarela.exportar_actividades.gestionar"]
endpoints: ["/src/pasarela/exportar_actividades_data"]
source: "docs/catalogo/pasarela/pantallas/exportar_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Exportar Select

## Resumen

Resultado del filtro "exportar actividades": delega el armado del listado en `/src/pasarela/exportar_actividades_data` (caso de uso {@see \src\pasarela\application\ExportarActividadesData}) y solo se ocupa de renderizar la tabla con `frontend\shared\web\Lista`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.iactividad_val`
- `post.iasistentes_val`
- `post.id_cdc`
- `post.id_tipo_activ`
- `post.isfsv_val`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `pasarela.exportar_actividades.gestionar`

## Endpoints Relacionados

- `/src/pasarela/exportar_actividades_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
