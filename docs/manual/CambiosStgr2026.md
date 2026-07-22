# Cambios STGR — plan de estudios 2026

Documento orientado a **usuarios** (estudios / secretaría): qué cambia con el plan 2026 y con el nuevo criterio de las notas.  
Detalle técnico: [`docs/dev/notas_modelo_acta.md`](../dev/notas_modelo_acta.md).

---

## 1. Cambio de perspectiva: la nota va con el acta (no con el alumno)

Hasta ahora, al trasladar a alguien entre delegaciones Orbix, el sistema tendía a **llevarse las notas** (o a inventar certificados / huecos “falta certificado” entre regiones).

**A partir de ahora:**

- La nota es un hecho del **acta de la DL que examinó**.
- El **traslado de persona entre DLs Orbix no mueve ni borra** esas notas.
- El **expediente** del alumno se ve completo agregando las notas de todas las DLs (no hace falta “copiar” la nota a la DL actual).

### Certificados

| Situación | ¿Hace falta certificado? |
|-----------|---------------------------|
| Traslado / consulta entre **DLs Orbix** (misma o distinta región STGR) | **No.** El expediente ya se lee de forma agregada. |
| Nota o historial hacia una **entidad externa** (fuera de Orbix / “resto”) | **Sí**, como documento (PDF / módulo de certificados), no como truco para “rellenar” notas internas. |

En la práctica: **en el futuro no harán falta certificados internos entre delegaciones Orbix** para que el alumno “lleve” las notas. El certificado queda para lo que es de verdad externo.

---

## 2. Dos planes de estudios: 1997 y 2026

Orbix distingue el plan **1997** (anterior) y el **2026** (vigente por defecto).

**Cómo se asigna el plan a una persona (tessera y listados):**

- Si tiene marca de **cuadrienio terminado (9998)** con fecha de acta **anterior al 2026-03-30** → plan **1997**.
- En el resto de casos → plan **2026**.

En pantallas como **comprobar notas** se puede elegir el plan (por defecto **2026**) para las comprobaciones de bienio / cuadrienio / c1 / c2.

---

## 3. Tessera: orden curricular y equivalencias (plan 2026)

La tessera muestra solo las asignaturas del **plan de la persona**, ordenadas por el nivel curricular (`id_nivel`) de ese plan.

### Cambios de orden (plan 2026)

| Asignatura | Qué cambia en 2026 |
|------------|--------------------|
| **Latín III** | Pasa a un nivel curricular más temprano (antes iba más adelante). |
| **Latín IV** | Se reordena a continuación del nuevo hueco de Latín III. |
| **Primeros Cristianos** | Entra en el plan 2026 en un nivel más avanzado del cuadrienio (sustituye el hueco que en 1997 ocupaban hebreo / griego; ver más abajo). |

En el plan **1997**, en el año I del cuadrienio figuraban como obligatorias **Lingua hebraica** y **Lingua graeca neotestamentaria**. En el plan **2026** esas dos dejan de ser obligatorias en ese hueco; el hueco curricular lo ocupa **Primeros Cristianos** (más adelante en el plan).

Las notas ya grabadas de alumnos que **aún no** habían cerrado el cuadrienio antiguo se **remapean** al nuevo orden del plan 2026 en lo que toca a Latín III/IV y Primeros Cristianos (migración de datos). Quien ya tenía el cuadrienio cerrado con el plan viejo sigue viendo el layout **1997**.

### Hebreo / griego → Primeros Cristianos (y opcional)

Criterio acordado al pasar al plan **2026** (hoy **no** hay migración automática en Orbix que lo aplique sola; se hace / se hará al convalidar expediente):

| Tiene en el plan 1997… | Qué hacer en el plan 2026 |
|------------------------|---------------------------|
| **Solo una** de las dos (hebreo **o** griego) | Esa nota **convalida Primeros Cristianos**. |
| **Las dos** (hebreo **y** griego) | Una de ellas convalida **Primeros Cristianos**; la **segunda** se registra como **opcional**. |

Así no se pierde el trabajo hecho: al menos una lengua bíblica cubre Primeros Cristianos; si el alumno hizo las dos, la sobrante cuenta como opcional del plan nuevo.

### Opcionales

- Las opcionales “clásicas” del plan **1997** (p. ej. las del bienio 1230–1232 y el bloque antiguo de opcionales) **no** forman parte del catálogo 2026.
- En **2026** hay un bloque nuevo de opcionales / seminarios (**Op. I–IV**).
- En comprobaciones del plan **1997**, la opcional de nivel **2430** sigue contando para decidir **c1/c2** (año I vs II–IV). En **2026** esa regla no aplica: c1/c2 se basan en el bloque de bienio (1000–2000) más la marca de bienio acabado (**9999**).

### Otras convalidaciones

- Sigue existiendo la situación de nota **«convalidada»** al editar una nota.
- El paso automático 1997 → 2026 que ya está hecho en migraciones es sobre todo **reordenar / reubicar** asignaturas del catálogo (Latín III/IV, Primeros Cristianos, opcionales), no un mapa completo de equivalencias (hebreo/griego → Primeros Cristianos es el criterio de § anterior, aún sin script de migración).
- Notas de asignaturas que **solo existían en 1997** (p. ej. un Latín I partido) **no se muestran** en la tessera 2026; no desaparecen de la base de datos, pero no encajan en la plantilla nueva hasta que se defina la convalidación.

---

## 4. Pendiente: Latín I-1 / Latín I-2 → «Latín I»

En el plan antiguo el latín inicial podía ir en **dos partes** (Latín I-1 y Latín I-2). En el plan **2026** el hueco curricular es **«Latín I»** (una sola asignatura).

**Aún está pendiente** acordar y aplicar cómo se convalida a quien:

- solo tiene **Latín I-1**, o  
- solo tiene **Latín I-2**,  

para dar por cubierta la asignatura **Latín I** del plan 2026 (¿basta una de las dos?, ¿hace falta nota numérica?, ¿convalidada?, ¿exigir ambas?, etc.).

Hasta entonces, esas notas parciales del plan 1997 **no aparecen** como “Latín I” en la tessera 2026.

---

## 5. Qué notará el usuario en el día a día

1. **Expediente / tessera / “asignaturas que faltan”**  
   Ven el historial aunque las actas estén en **otra DL** Orbix (sin pedir certificado interno).

2. **Traslado entre DLs Orbix**  
   Las notas **no se “trasladan”**; siguen ancladas al acta de origen. El alumno se ve igual de completo en destino.

3. **Comprobar notas**  
   - Plan por defecto **2026** (se puede pasar a 1997).  
   - Umbrales de “bienio/cuadrienio terminado” según el **número de asignaturas activas** de ese plan.  
   - Criterios **c1/c2** distintos por plan (ver §3).  
   - Ya no hay aviso de “notas en esquema distinto al de la ficha” (eso es lo esperado con el modelo acta).

4. **Certificados**  
   Reservados a salidas **fuera de Orbix**; no como mecanismo habitual entre delegaciones.

---

## 6. Fechas útiles

| Fecha | Uso |
|-------|-----|
| **2026-03-30** | Corte para asignar plan 1997 vs 2026 según la marca de cuadrienio terminado (9998). |
| **2026-09-30** | Referencia en migraciones de remap de niveles de notas hacia el orden 2026 (alumnos sin cuadrienio antiguo cerrado). |

---

## 7. Resumen en una frase

**Plan 2026 + modelo acta:** la tessera y el expediente siguen el plan vigente (Latín III/IV y Primeros Cristianos reordenados; hebreo/griego → Primeros Cristianos / opcional según regla acordada); las notas viven en la DL del acta y **ya no hace falta certificado interno entre Orbix**; queda por definir la convalidación **Latín I-1 / I-2 → Latín I**.
