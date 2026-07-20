# Notas: modelo anclado al acta (decisión y plan técnico)

**Estado:** decisión de dominio confirmada (2026-07-20). Sin cambios de código en esta fase.  
**Relacionado:** [`notas_migracion_baseline.md`](notas_migracion_baseline.md), módulo certificados, [`Trasladar::copiarNotas`](../../src/personas/domain/Trasladar.php), [`EditarPersonaNota`](../../src/notas/application/EditarPersonaNota.php).

---

## 1. Veredicto confirmado

**Se adopta el modelo B: las notas dependen del acta (y por tanto de la DL examinadora), no de la ubicación administrativa actual del alumno.**

| Pregunta | Respuesta |
|----------|-----------|
| ¿Dónde se conserva el hecho académico? | En la DL del acta (`e_notas_dl` del esquema que examina). |
| ¿Qué hace el traslado de persona con las notas? | Nada: no mueve ni borra notas de actas. |
| ¿Cuándo hay certificado ligado a notas? | Solo hacia **entidad externa** (definición §2). |
| ¿Cómo ve la DL del alumno el historial? | Expediente agregado (consulta) + certificados recibidos cuando apliquen. |

### Por qué se descarta el modelo A (persona-céntrico actual)

El modelo actual responde a “¿cómo ve el destino el historial?” **mutando** “¿dónde vive el hecho?”: mueve filas, usa `e_notas_otra_region_stgr`, placeholders `falta certificado` y certificados internos entre regiones STGR. Eso reescribe historia, infla el significado de certificado y acumula estados intermedios frágiles.

### Separación de responsabilidades

1. **Hecho académico** → fijo en la DL del acta.  
2. **Expediente usable** → lectura agregada (y, en frontera externa, certificado).

---

## 2. Definición operativa de «entidad externa»

### Definición

**Entidad externa** = destino con el que **no** se puede resolver el expediente del alumno leyendo actas/notas Orbix por agregación multi-esquema.

En la práctica, es externa cuando:

- El destino **no** es una DL/región con esquema Orbix propio (`*v` / `*f` distinto de `resto*`), o
- La persona es de **paso** / vive en `restov`/`restof` (`id_nom` negativo o esquema resto), o
- La comunicación exigida es solo documental (PDF/impreso) hacia una institución o autoridad **fuera** de Orbix.

### Qué **no** es entidad externa

Cualquier traslado o consulta entre esquemas Orbix, **incluida otra región STGR**:

- Traslado DL ↔ DL misma región STGR.  
- Traslado entre regiones STGR distintas pero ambas en Orbix.  
- Alumno de otra DL/región Orbix al que se le pone nota en un acta local.

En esos casos: la nota **queda** en la DL del acta; el expediente del alumno se arma por **agregación**; **no** se crea placeholder `falta certificado` ni se exige certificado formal para “llevar” la nota.

### Certificados del módulo `certificados`

- **Automáticos / ligados al flujo de notas-traslado:** solo si el destino es entidad externa (§2).  
- **Manuales (emitir/recibir PDF):** pueden seguir existiendo como documento administrativo voluntario; **no** son el mecanismo para mover ni duplicar notas entre DLs Orbix.

Señal ya existente en código: personas de paso no admiten “enviar” certificado digital (“Hay que imprimir”) — alinea con frontera externa.

### Criterio de decisión en código (futuro)

```text
destino_es_externo =
  esquema_persona in {restov, restof}
  OR id_nom < 0
  OR destino_sin_esquema_orbix
  OR flag_explicito_envio_fuera_orbix
```

Cualquier otra pareja origen/destino Orbix → **interno** → sin movimiento de notas, sin placeholder certificado.

---

## 3. Plan técnico (siguiente fase)

Orden de trabajo recomendado. Cada slice debe dejar tests verdes y no mezclar migración de datos con rediseño de UI sin necesidad.

### Slice 0 — Contrato de dominio (doc + tests de intención)

- Fijar este documento como ADR.  
- Reescribir expectativas de [`tests/unit/notas/trasladosNotasTest.php`](../../tests/unit/notas/trasladosNotasTest.php) como **tests fallidos/pendientes** o nuevos tests que describan:
  - traslado interno/inter-región Orbix → notas intactas en esquema del acta;  
  - sin filas nuevas `tipo_acta=2` / `FALTA_CERTIFICADO` por traslado;  
  - externo → certificado (documento), no copia de nota como acta en destino.

### Slice 1 — Escritura de notas (`EditarPersonaNota`)

- `getReposPersonaNota`: la nota real **siempre** en `e_notas_dl` (o tabla de actas) de la **DL que introduce/examina**, no en función del esquema del alumno.  
- Eliminar (o acotar a externo) la rama que crea `repo_certificado` + placeholder en la DL del alumno.  
- Personas de paso / resto: nota en la DL examinadora; certificado documental si hace falta comunicar fuera (sin inventar fila “certificado” en un esquema resto inexistente).

### Slice 2 — Traslado (`Trasladar::copiarNotas`)

- Traslado Orbix→Orbix: **no copiar / no borrar** notas de actas.  
- Quitar dependencia de `mismaRegionStgr` para mover notas.  
- Solo si destino externo: flujo de certificado (emitir/adjuntar), sin vaciar el acta origen.  
- Revisar avisos `comprobarNotas` / “notas sin trasladar” en `comprobar_notas_page_body.inc.php`.

### Slice 3 — Expediente agregado (lectura)

Punto crítico de producto: la DL del alumno debe ver el historial sin tener las filas localmente.

- Nuevo servicio de aplicación (p. ej. `ExpedienteNotasPersona`) que agregue por `id_nom`:
  - notas en esquemas Orbix donde existan filas (DL de cada acta);  
  - certificados recibidos del módulo certificados (frontera externa).  
- Sustituir lecturas “solo esquema actual” en:
  - notas de una persona / tessera;  
  - asignaturas pendientes / resumen;  
  - comprobaciones e informes STGR que asumen notas en el schema de la persona.  
- Definir reglas de deduplicación si coexisten nota de acta y certificado sobre la misma asignatura (prioridad: acta Orbix > certificado).

### Slice 4 — Destino de `e_notas_otra_region_stgr` y `tipo_acta=2`

- Inventariar filas actuales (herramienta en `tools/audit/` o `tools/fix/` con `--dry-run`).  
- Estrategia de consolidación:
  - notas “reales” en `otra_region` → repatriar a `e_notas_dl` de la DL/región que las examinó (según acta/`id_schema` histórico);  
  - placeholders `falta certificado` / filas solo certificado internas → eliminar o convertir en `CertificadoRecibido` cuando haya PDF/número real;  
  - `json_certificados` → migrar al módulo certificados cuando aporte valor.  
- Decidir si `e_notas_otra_region_stgr` se depreca o queda solo para casos residuales de paso (preferencia: **deprecar** tras migración).

### Slice 5 — Módulo certificados

- Desacoplar `addCertificado` / `deleteCertificado` en `PgPersonaNotaOtraRegionStgrRepository` del alta automática de notas `FORMATO_CERTIFICADO` en traslados internos.  
- Mantener emitir/guardar/enviar PDF para **externo** y uso manual.  
- `CertificadoEmitidoEnviar` hacia DL Orbix: reevaluar si sigue siendo necesario cuando el expediente es agregado (posible deprecación del “enviar para rellenar nota”).

### Slice 6 — Migración de datos y limpieza

- Script `tools/fix/` con `--dry-run` / `--apply`.  
- Informe en `docs/dev/reports/` si el volumen lo requiere.  
- Actualizar tests de integración y factories (`PersonaNotaOtraRegionStgrFactory`, etc.).

### Slice 7 — Documentación de usuario / catálogo

- Manual notas + certificados: traslado ya no “lleva” notas; certificado solo externo.  
- Regenerar fragmentos AI/catálogo afectados tras cambiar flujos.

---

## 4. Riesgos y dependencias

| Riesgo | Mitigación |
|--------|------------|
| Expediente lento (N esquemas) | Cache por persona / vista materializada / búsqueda acotada por regiones STGR conocidas |
| Datos huérfanos en `otra_region` | Slice 4 obligatorio antes de borrar tabla |
| Doble conteo acta+certificado | Regla de prioridad en § Slice 3 |
| Permisos cross-schema | Reutilizar patrones de `Persona::buscarEnTodasRegiones` y repos con `setoDbl` |
| Regresión en informes STGR | Tests de `Resumen` / asignaturas pendientes en misma entrega que Slice 3 |

---

## 5. Criterios de aceptación globales

- Trasladar alumno entre DLs/regiones Orbix **no** altera filas de notas de actas.  
- Poner nota en un acta deja la fila en la DL del acta aunque el alumno sea de otra región Orbix.  
- No aparecen `FALTA_CERTIFICADO` por traslados internos.  
- Expediente del alumno en destino muestra esas notas (agregación).  
- Certificado automático solo si el destino cumple §2 (entidad externa).  
- Tests de traslados y de expediente reflejan el nuevo contrato.

---

## 6. Fuera de alcance de este documento

- Implementación de código (otra entrega).  
- Cambio de PK `(id_nom, id_nivel, tipo_acta)` ni FK fuerte acta↔nota (mejora posterior posible).  
- Rediseño visual de pantallas más allá de lo necesario para leer el expediente agregado.
