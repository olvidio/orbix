# Refactor — convenciones movidas

El contenido normativo de este documento está integrado en **`AGENTS.md`**, para que sirva de guía permanente del proyecto:

- **Migración `apps/` → `frontend/` + `src/` (convivencia y slices)** — capas, orden de trabajo, endpoints, naming en `application/`, legacy encapsulado, desplegables AJAX, Hash, checklists.
- **Comunicación Frontend-Backend (AJAX y JSON)** — `ContestarJson::enviar`, mutaciones, patrones JS de guardado y llamadas con `PostRequest`.

Este archivo se puede **eliminar por completo** cuando no queden referencias a `refactor.md` en el repositorio (sustituir por `AGENTS.md` en comentarios y en `docs/dev/*`).
