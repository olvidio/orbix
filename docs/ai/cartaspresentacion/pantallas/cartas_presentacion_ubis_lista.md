---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "cartaspresentacion"
titulo: "Cartas Presentacion Ubis Lista"
pantalla: "cartaspresentacion.pantalla.cartas_presentacion_ubis_lista"
preguntas: ["Que se puede hacer en Cartas Presentacion Ubis Lista?", "Que campos tiene Cartas Presentacion Ubis Lista?", "Que acciones hay en Cartas Presentacion Ubis Lista?"]
capacidades: ["cartaspresentacion.ubis.gestionar"]
endpoints: ["/src/cartaspresentacion/ubis_lista_data"]
source: "docs/catalogo/cartaspresentacion/pantallas/cartas_presentacion_ubis_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Cartas Presentacion Ubis Lista

## Resumen

Fragmento AJAX: tabla de centros con estado de carta de presentación (sí/no) y acciones por fila.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.poblacion_sel`
- `post.tipo_lista`

## Acciones Detectadas

- `fnjs_modificar`
- `fnjs_ver_ubi`
- `fnjs_eliminar_cp`

## Capacidades Relacionadas

- `cartaspresentacion.ubis.gestionar`

## Endpoints Relacionados

- `/src/cartaspresentacion/ubis_lista_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
