---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "cartaspresentacion"
titulo: "Cartas Presentacion"
pantalla: "cartaspresentacion.pantalla.cartas_presentacion"
preguntas: ["Que se puede hacer en Cartas Presentacion?", "Que campos tiene Cartas Presentacion?", "Que acciones hay en Cartas Presentacion?"]
capacidades: ["cartaspresentacion.cartas_presentacion_shell.gestionar"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_shell_data", "/src/cartaspresentacion/ubis_lista_data", "/src/cartaspresentacion/poblaciones_data", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update", "/src/cartaspresentacion/carta_presentacion_eliminar"]
source: "docs/catalogo/cartaspresentacion/pantallas/cartas_presentacion.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Cartas Presentacion

## Resumen

Pantalla principal del módulo: selección dl/regiones + población, listado AJAX de centros con estado de carta de presentación y modal de modificación.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `html.buscar`
- `html.tipo_lista`
- `html.poblacion_sel`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_construir_desplegable`
- `fnjs_eliminar_cp`
- `fnjs_guardar_cp`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_poblacion`
- `fnjs_ver`
- `fnjs_ver_ubi`

## Capacidades Relacionadas

- `cartaspresentacion.cartas_presentacion_shell.gestionar`

## Endpoints Relacionados

- `/src/cartaspresentacion/cartas_presentacion_shell_data`
- `/src/cartaspresentacion/ubis_lista_data`
- `/src/cartaspresentacion/poblaciones_data`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`
- `/src/cartaspresentacion/carta_presentacion_eliminar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
