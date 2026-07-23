# Notas: modelo anclado al acta (decisión y plan técnico)

**Estado:** núcleo del modelo acta implementado (slices 0–7). Pendientes operativos: repatriación apply de `otra_region`, resúmenes/informes STGR, fixture certificado externo, deprecación de `CertificadoEmitidoEnviar` como relleno de notas.  
**Relacionado:** [`notas_migracion_baseline.md`](notas_migracion_baseline.md), módulo certificados, [`Trasladar::copiarNotas`](../../src/personas/domain/Trasladar.php), [`EditarPersonaNota`](../../src/notas/application/EditarPersonaNota.php), [`ExpedienteNotasPersona`](../../src/notas/application/ExpedienteNotasPersona.php).

---

## 1. Veredicto confirmado

**Se adopta el modelo B: las notas dependen del acta (y por tanto de la DL examinadora), no de la ubicación administrativa actual del alumno.**

| Pregunta | Respuesta |
|----------|-----------|
| ¿Dónde se conserva el hecho académico? | En la DL del acta (`e_notas_dl` del esquema que examina). |
| ¿Qué hace el traslado de persona con las notas? | Nada: no mueve ni borra notas de actas. |
| ¿Cuándo hay certificado ligado a notas? | Solo hacia **entidad externa** (definición §2). |
| ¿Cómo ve la DL del alumno el historial? | Expediente agregado vía `publicv.e_notas` (+ certificados recibidos cuando apliquen). |

### Lectura agregada: `publicv.e_notas`

PostgreSQL: `publicv.e_notas` es la **tabla padre** de las hijas `{esquema}.e_notas_dl`. Consultar el padre por `id_nom` ya agrega las notas de todas las DLs sin recorrer esquemas uno a uno.

Implicaciones:

- El expediente del alumno (Slice 3) debe basarse en `publicv.e_notas` (filtrando hijas que no sean nota de acta cuando proceda; p. ej. excluir `e_notas_otra_region_stgr` si sigue heredando).
- Ya hay precedente: `Resumen` / `AsignaturasPendientes` usan `publicv/f.e_notas` para el expediente del alumno.
- **Resúmenes / informes STGR:** se revisarán en una pasada posterior (no bloquean Slice 0–2); hoy asumen notas en el esquema de la persona y habrá que alinearlos al padre / expediente.

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

- [x] Fijar este documento como ADR (incl. § lectura `publicv.e_notas`).  
- [x] Tests de intención del nuevo contrato ([`tests/unit/notas/trasladosNotasModeloActaTest.php`](../../tests/unit/notas/trasladosNotasModeloActaTest.php); [`trasladosNotasTest.php`](../../tests/unit/notas/trasladosNotasTest.php) actualizado al modelo B):
  - traslado interno/inter-región Orbix → notas intactas en esquema del acta;  
  - sin filas nuevas `tipo_acta=2` / `FALTA_CERTIFICADO` por traslado;  
  - externo → certificado (documento), no copia de nota como acta en destino;  
  - expediente visible vía `publicv.e_notas` por `id_nom`.

### Slice 1 — Escritura de notas (`EditarPersonaNota`)

- [x] `getReposPersonaNota`: la nota real **siempre** en `e_notas_dl` de la **DL que introduce/examina**.  
- [x] Eliminada la rama `repo_certificado` / placeholder `FALTA_CERTIFICADO` (también de `crear_*` / `editar_*`).  
- [x] Personas de paso / resto: nota en la DL examinadora; `DestinoNotaExterno` + flag `destino_externo` en el alta; certificado hacia fuera = documental (módulo certificados / PDF), sin fila placeholder ni escritura en `resto`.

### Slice 2 — Traslado (`Trasladar::copiarNotas`)

- [x] Traslado Orbix→Orbix: **no copiar / no borrar** notas (`copiarNotas` no-op). Incluye notas `tipo_acta=acta` y `tipo_acta=certificado` ya existentes.  
- [x] Quitar dependencia de `mismaRegionStgr` para mover notas.  
- [ ] Solo si destino externo: disparar/avisar flujo de certificado documental (emitir/adjuntar PDF), **sin** vaciar ni mover notas del origen. Aún no automatizado en `Trasladar` (sigue siendo flujo manual del módulo certificados).  
- [x] Eliminado aviso `comprobar_notas` de «notas en esquema distinto» (esperado con modelo acta).

### Slice 3 — Expediente agregado (lectura)

- [x] `ExpedienteNotasPersona` agrega por `id_nom` leyendo **`publicv.e_notas`**, con deduplicación acta > certificado.  
- [x] `NotasDeUnaPersonaData` / dossier 1011 / tessera usan expediente agregado (`publicv.e_notas`).  
- [x] **`Resumen` STGR (alumnos):** indicadores por persona vía padre `publicv/f.e_notas` (no solo `e_notas_dl` local). `$tablaNotasDl` reservado si hiciera falta métrica «examinado en esta DL».  
- [x] **`AsignaturasPendientes`:** mismo criterio (expediente padre).  
- [x] **`comprobar_notas`:** lecturas y borrados de cursadas vía `publicv/f.e_notas`; INSERT 9998/9999 vía `ActaFinCicloInsert` (`acta`=sigla DL que inserta, `detalle`=«fin …», `tipo_acta`=1).

### Slice 4 — Destino de `e_notas_otra_region_stgr` y `tipo_acta=2`

- [x] Inventariar: [`tools/audit/audit_notas_otra_region.php`](../../tools/audit/audit_notas_otra_region.php).  
- [x] Auditoría dry-run: [`tools/fix/fix_notas_otra_region_a_acta.php`](../../tools/fix/fix_notas_otra_region_a_acta.php); mapa en BD **comun** [`public.mapa_prefijo_acta_esquema`](../../db/migrations/202607211100_mapa_prefijo_acta_esquema__comun.sql) (diag: [`tools/audit/diag_notas_otra_region_mapa.sql`](../../tools/audit/diag_notas_otra_region_mapa.sql); durante el lote usa snapshot `publicv/f._mig_…` de 211120) — **fuente única** de prefijo↔esquema (también búsqueda de actas absorbidas y `AbsorberEsquema`).  
- [x] Separar prefijo pegado al nº (`dlb156/93` → `dlb 156/93`; `M1 3/20` → `M 13/20`): [`202607211140_…`](../../db/migrations/202607211140_separar_prefijo_acta_pegado_a_numero__sv.sql) (antes de repatriar/mover).  
- [x] Reescribir actas libres (`ratio` / `aquinate` / `?`) en `otra_region`: [`202607211150_…`](../../db/migrations/202607211150_reescribir_acta_libre_sigla_esquema__sv.sql) (antes del repatriado).  
- [x] Certificados **tipo 2**: [`202607211250_certificados_otra_region_limpiar`](../../db/migrations/202607211250_certificados_otra_region_limpiar__sv.sql) — borrar si hay acta pareja; si no, dejar en `otra_region` de región (`H-Hv`, `M-Mv`, `Galbel-crGalbelv`, …). No repatriar a `e_notas_dl`.  
- [x] Repatriar **solo tipo 1** (excluye 9998/9999): [`202607211300_…`](../../db/migrations/202607211300_repatriar_notas_otra_region_a_acta__sv.sql) (lee snapshot del mapa).  
- [x] Mover tipo 1 mal ubicados entre `e_notas_dl` (excluye 9998/9999): [`202607222000_…`](../../db/migrations/202607222000_mover_notas_dl_segun_mapa_acta__sv.sql).  
- [ ] Ejecutar: **comun** `211100` → `211110`; luego **sv/sf** `211120` → `211140` → `211150` → `211250` → `211300` → `222000`. Ampliar filas del mapa en comun si el diag marca `sin_mapa`. Fin de ciclo histórico (9998/9999): **no migrar**. Altas nuevas vía `ActaFinCicloInsert`.  
- [x] Usar `MapaPrefijoActaEsquemaRepository` al grabar notas con acta histórica (routing a esquema destino).  
- [x] Buscar/validar actas: `ActaSelectData` / `BuscarActaData` / `ActaDlGuard` leen prefijos absorbidos del mapa; `AbsorberEsquema` registra la fusión en la misma tabla.  
- [ ] Migrar `json_certificados` al módulo certificados cuando aporte valor.  
- [ ] Deprecar `e_notas_otra_region_stgr` tras migración de datos (salvo certificados sin acta pareja).

#### Fuente única: `comun.public.mapa_prefijo_acta_esquema` (owner `orbix`)

| Uso | Dirección |
|-----|-----------|
| Runtime (routing notas, búsqueda actas, AbsorberEsquema) | PDO `oDBPC` → comun |
| Migraciones de datos en sv/sf | snapshot `publicv/f._mig_mapa_prefijo_acta_esquema` (211120; CSV desde 211110) |
| Repatriar / escribir nota con acta histórica | prefijo → `esquema_base` |
| Buscar actas desde la DL matriz (p. ej. H-dlal ve `dlz`/`dlv`) | `esquema_base` → lista de prefijos |
| Absorber esquema | escribe/actualiza filas en comun (`notas` «fusionada en …») |

No hay un catálogo aparte en `xu_dl` ni en PHP: al absorber, las filas de `e_actas_dl` pasan al esquema matriz **con el prefijo antiguo** (por eso la búsqueda por texto `dlz` ya las encontraba). El mapa hace explícito y mantenible ese vínculo. Fusiones H: `dlz` → **H-dlal**; `dlv`/`dlva`/`dlst` → **H-dln**.

### Slice 5 — Módulo certificados

- [x] Desacoplado `addCertificado` / `deleteCertificado` en `PgPersonaNotaOtraRegionStgrRepository` del alta automática de notas `FORMATO_CERTIFICADO` en traslados internos.  
- [x] Mantener emitir/guardar/enviar PDF para **externo** y uso manual.  
- [x] `CertificadoEmitidoEnviar`: documentada la reevaluación (sigue enviando PDF/recibido; ya no es el mecanismo para «rellenar nota» del expediente).

### Slice 6 — Migración de datos y limpieza

- [x] Migraciones SQL repatriación + limpieza placeholders (ver Slice 4). CLI dry-run de apoyo; `--apply` CLI deprecado.  
- [ ] Informe en `docs/dev/reports/` tras migración en staging/producción.  
- [ ] Actualizar factories cuando `otra_region` esté vacía / deprecada.

### Slice 7 — Documentación de usuario / catálogo

- [x] Nota operativa en §7 (este documento) y actualización de [`backlog.md`](backlog.md).  
- [ ] Manual notas + certificados: revisión editorial completa en [`docs/manual/notas.md`](../manual/notas.md).  
- [ ] Regenerar fragmentos AI/catálogo afectados (no bloqueante; párrafo en ADR suficiente por ahora).

---

## 4. Riesgos y dependencias

| Riesgo | Mitigación |
|--------|------------|
| Expediente lento (N esquemas) | Preferir `publicv.e_notas` (herencia PG) antes que N conexiones; cache solo si hace falta |
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

- Cambio de PK `(id_nom, id_nivel, tipo_acta)` ni FK fuerte acta↔nota (mejora posterior posible).  
- Rediseño visual de pantallas más allá de lo necesario para leer el expediente agregado.

---

## 7. Nota operativa para usuarios y soporte

Desde la adopción del **modelo B** (2026-07):

1. **Traslado de persona** entre DLs o regiones STGR Orbix **no mueve ni borra** las notas del acta. La fila permanece en la DL que examinó.  
2. **Expediente del alumno** en la DL de destino: se consulta vía agregación (`publicv.e_notas` / servicio `ExpedienteNotasPersona`), no porque las notas se hayan copiado localmente.  
3. **Dos sentidos de «certificado» (no confundir):**
   - **Nota con `tipo_acta = certificado`**: calificación que llega de una entidad externa; en Orbix **no** existe el acta origen. Es una fila legítima del expediente (se escribe/edita como cualquier nota; el traslado no la mueve).  
   - **Placeholder «falta certificado»** (`FALTA_CERTIFICADO`): inventado en traslados internos — **eliminado** del modelo B.  
   - **PDF del módulo certificados**: documento formal hacia fuera / recibido; distinto de la fila de nota.

**Pendiente explícito:** informes y resúmenes STGR que aún lean solo el esquema local de la persona — alinear en pasada posterior (Slice 3). Flujo automático de PDF al trasladar a externo — Slice 2 (checkbox abierto).
