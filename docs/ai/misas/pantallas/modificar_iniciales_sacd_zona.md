---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "misas"
titulo: "Modificar Iniciales Sacd Zona"
pantalla: "misas.pantalla.modificar_iniciales_sacd_zona"
preguntas: ["Que se puede hacer en Modificar Iniciales Sacd Zona?", "Que campos tiene Modificar Iniciales Sacd Zona?", "Que acciones hay en Modificar Iniciales Sacd Zona?"]
capacidades: ["misas.modificar_iniciales_sacd_zona.gestionar"]
endpoints: ["/src/misas/modificar_iniciales_sacd_zona_data"]
source: "docs/catalogo/misas/pantallas/modificar_iniciales_sacd_zona.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Modificar Iniciales Sacd Zona

## Resumen

Entry point para editar iniciales y color de sacerdotes por zona. Selector de zona y carga AJAX de `ver_iniciales_zona`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_zona`

## Acciones Detectadas

- `fnjs_ver_iniciales_sacd_zona`

## Capacidades Relacionadas

- `misas.modificar_iniciales_sacd_zona.gestionar`

## Endpoints Relacionados

- `/src/misas/modificar_iniciales_sacd_zona_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
