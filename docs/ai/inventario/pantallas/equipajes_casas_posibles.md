---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "inventario"
titulo: "Casas posibles"
pantalla: "inventario.pantalla.equipajes_casas_posibles"
preguntas: ["Que se puede hacer en Casas posibles?", "Que campos tiene Casas posibles?", "Que acciones hay en Casas posibles?"]
capacidades: ["inventario.lista_casas_posibles_periodo.gestionar"]
endpoints: ["/src/inventario/lista_casas_posibles_periodo"]
source: "docs/catalogo/inventario/pantallas/equipajes_casas_posibles.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Casas posibles

## Resumen

Desplegable casas en periodo.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.inicio`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_ver_actividades_casa`

## Capacidades Relacionadas

- `inventario.lista_casas_posibles_periodo.gestionar`

## Endpoints Relacionados

- `/src/inventario/lista_casas_posibles_periodo`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
