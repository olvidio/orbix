---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Traslado Doc Que"
pantalla: "inventario.pantalla.traslado_doc_que"
preguntas: ["Que se puede hacer en Traslado Doc Que?", "Que campos tiene Traslado Doc Que?", "Que acciones hay en Traslado Doc Que?"]
capacidades: ["inventario.lista_de_ctr.gestionar", "inventario.lista_lugares_de_ubi.gestionar"]
endpoints: ["/src/inventario/lista_de_ctr", "/src/inventario/lista_lugares_de_ubi"]
source: "docs/catalogo/inventario/pantallas/traslado_doc_que.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Traslado Doc Que

## Resumen

Descripcion funcional pendiente de revisar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_ubi`
- `form.id_ubi_new`
- `form.sel`
- `html.ok`

## Acciones Detectadas

- `fnjs_busca_docs`
- `fnjs_busca_lugares`
- `fnjs_busca_lugares_destino`
- `fnjs_busca_lugares_origen`
- `fnjs_crearSelectDesdeJson`
- `fnjs_guardar`
- `fnjs_put_desplegable_lugares`

## Capacidades Relacionadas

- `inventario.lista_de_ctr.gestionar`
- `inventario.lista_lugares_de_ubi.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_de_ctr`
- `/src/inventario/lista_lugares_de_ubi`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
