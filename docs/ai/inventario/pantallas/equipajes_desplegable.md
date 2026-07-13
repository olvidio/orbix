---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Desplegable equipajes"
pantalla: "inventario.pantalla.equipajes_desplegable"
preguntas: ["Que se puede hacer en Desplegable equipajes?", "Que campos tiene Desplegable equipajes?", "Que acciones hay en Desplegable equipajes?"]
capacidades: ["inventario.lista_equipajes_desde_fecha.gestionar"]
endpoints: ["/src/inventario/lista_equipajes_desde_fecha"]
source: "docs/catalogo/inventario/pantallas/equipajes_desplegable.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Desplegable equipajes

## Resumen

Opciones equipajes desde fecha.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.eliminar`
- `post.filtro`
- `post.imprimir`

## Acciones Detectadas

- `fnjs_ver_1`
- `fnjs_ver_2`

## Capacidades Relacionadas

- `inventario.lista_equipajes_desde_fecha.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_equipajes_desde_fecha`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
