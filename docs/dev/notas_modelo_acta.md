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
- Ya hay precedente: `AsignaturasPendientes` usa `publicv.e_notas` en ámbito `rstgr`.
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
- [x] Revisados avisos `comprobarNotas` / “notas sin trasladar” en `comprobar_notas_page_body.inc.php`.

### Slice 3 — Expediente agregado (lectura)

- [x] `ExpedienteNotasPersona` agrega por `id_nom` leyendo **`publicv.e_notas`**, con deduplicación acta > certificado.  
- [x] `NotasDeUnaPersonaData` / dossier 1011 usan expediente agregado (`publicv.e_notas`). Tessera ya leía `PersonaNotaRepository` (padre).  
- [ ] **Resúmenes / informes STGR:** pasada dedicada pendiente (asumen notas en esquema de la persona).

### Slice 4 — Destino de `e_notas_otra_region_stgr` y `tipo_acta=2`

- [x] Inventariar: [`tools/audit/audit_notas_otra_region.php`](../../tools/audit/audit_notas_otra_region.php).  
- [x] Auditoría dry-run: [`tools/fix/fix_notas_otra_region_a_acta.php`](../../tools/fix/fix_notas_otra_region_a_acta.php) + mapa [`tools/fix/data/esquemas_dl_fusionados.php`](../../tools/fix/data/esquemas_dl_fusionados.php) (`dlz`/`dlv` → `dlal`; `dlva`/`dlst` → `dln`).  
- [x] **Aplicación BD (local → prod):** migraciones web  
  [`db/migrations/202607211300_repatriar_notas_otra_region_a_acta__sv.sql`](../../db/migrations/202607211300_repatriar_notas_otra_region_a_acta__sv.sql) /  
  [`…__sf.sql`](../../db/migrations/202607211300_repatriar_notas_otra_region_a_acta__sf.sql)  
  (devel_db_admin → Migraciones). Idempotente; omite destinos sin `e_notas_dl`; deja 9998/9999 y actas sin prefijo.  
- [ ] Ejecutar migración en local completo (todos los esquemas DL) y después en producción; revisar NOTICE de omitidas.  
- [ ] Migrar `json_certificados` al módulo certificados cuando aporte valor.  
- [ ] Deprecar `e_notas_otra_region_stgr` tras migración de datos (salvo casos 9998/9999 pendientes).

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
