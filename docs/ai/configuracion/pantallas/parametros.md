---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "configuracion"
titulo: "Configuración del esquema"
pantalla: "configuracion.pantalla.parametros"
preguntas: ["Que se puede hacer en Configuración del esquema?", "Que campos tiene Configuración del esquema?", "Que acciones hay en Configuración del esquema?"]
capacidades: ["configuracion.parametros.gestionar"]
endpoints: ["/src/configuracion/parametros_lista", "/src/configuracion/parametros_update"]
source: "docs/catalogo/configuracion/pantallas/parametros.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Configuración del esquema

## Resumen

Pantalla de parámetros globales del esquema Orbix: periodos de curso STGR/CRT, jefe de calendario, datos de estudios/certificados, notas, idioma, ámbito (dl/región/rstgr) y gestión de calendario. Cada bloque es un formulario independiente con HashFront.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.ini_dia`
- `form.ini_mes`
- `form.fin_dia`
- `form.fin_mes`
- `form.valor`
- `form.parametro`

## Acciones Detectadas

- `fnjs_guardar`

## Capacidades Relacionadas

- `configuracion.parametros.gestionar`

## Endpoints Relacionados

- `/src/configuracion/parametros_lista`
- `/src/configuracion/parametros_update`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
