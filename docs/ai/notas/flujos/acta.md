---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Crear un acta"
flujo: "notas.acta.gestionar.flujo"
preguntas: ["Como crear un acta en Crear un acta?", "Como eliminar un acta en Crear un acta?"]
pantallas_principales: ["notas.pantalla.acta_select"]
fragmentos: ["notas.pantalla.acta_select", "notas.pantalla.acta_ver"]
endpoints: ["/src/notas/acta_eliminar", "/src/notas/acta_nueva"]
source: "docs/catalogo/notas/flujos/acta.md"
estado_revision: "generado"
---

# Ayuda IA - Crear un acta

Usa este documento para responder preguntas de usuario sobre como trabajar con `Crear un acta`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear un acta en Crear un acta?
- Como eliminar un acta en Crear un acta?

## Donde Entrar

- Acta Select (`notas.pantalla.acta_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear un acta

1. Entrar en **actas** y pulsar nueva acta: se abre **DATOS DEL ACTA**.
2. El **Acta** viene propuesta con el siguiente número de la delegación (por ejemplo «dlb 39/26», con «última = dlb 38/26»); se puede cambiar, con formato num/aa.
3. Rellenar la **Fecha acta** (obligatoria), **Libro**, **Página**, **Línea**, **Lugar** y **Observaciones**.
4. Elegir la **Asignatura** escribiendo parte del nombre y eligiendo de la lista (es obligatoria aunque la pantalla no lo avise; si falta sale un error técnico, D-NOT-2).
5. En **Tribunal**, escribir el **Examinador** (con **Más** se añaden más).
6. Pulsar **Guardar cambios acta**. Después, abriendo el acta, aparece **Alumnos del acta** (al principio «Todavía no hay notas asociadas a este acta») y **Añadir alumno al acta**.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_eliminar`
- `/src/notas/acta_nueva`

## Eliminar un acta

1. En **actas**, marcar el acta y pulsar eliminar; confirmar «esto eliminará los datos del acta, pero no las notas que mantendrán el número de acta». Las notas ya puestas siguen existiendo con ese número de acta.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_eliminar`
- `/src/notas/acta_nueva`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_select`
- `notas.pantalla.acta_select`
- `notas.pantalla.acta_ver`

## Objetivo

Ciclo completo de un acta: listarla, abrirla, crearla, modificarla o eliminarla, con PDF e impresión.

## Errores Documentados

- `No se encuentra el acta`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
