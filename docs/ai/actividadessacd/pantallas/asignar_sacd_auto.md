---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadessacd"
titulo: "Asignar Sacd Auto"
pantalla: "actividadessacd.pantalla.asignar_sacd_auto"
preguntas: ["Que se puede hacer en Asignar Sacd Auto?", "Que campos tiene Asignar Sacd Auto?", "Que acciones hay en Asignar Sacd Auto?"]
capacidades: ["actividadessacd.sacd_asignar_auto.gestionar"]
endpoints: ["/src/actividadessacd/sacd_asignar_auto"]
source: "docs/catalogo/actividadessacd/pantallas/asignar_sacd_auto.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Asignar Sacd Auto

## Resumen

Pantalla auxiliar "Auto asignar sacd a actividades": muestra el criterio de asignación automática (sacd titular del centro encargado a actividades sr/sg actuales posteriores al inicio de curso des) y un botón **continuar** que dispara el endpoint `/src/actividadessacd/sacd_asignar_auto` y pinta el resultado (`asignadas`, `sin_asignar`) sin recargar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- No hay campos detectados.

## Acciones Detectadas

- `fnjs_asignar_sacd_auto`
- `fnjs_esc_asauto`

## Capacidades Relacionadas

- `actividadessacd.sacd_asignar_auto.gestionar`

## Endpoints Relacionados

- `/src/actividadessacd/sacd_asignar_auto`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
