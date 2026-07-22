# Backlog técnico (diferido)

Listado corto de **mejoras o migraciones decididas pero no ejecutadas**. No sustituye a issues/Trello del equipo si los usáis; sirve como memoria dentro del repo.

Formato sugerido por ítem:

- **Qué**, **por qué no ahora**, **notas / enlaces** (ficheros, hilos).

---

## Pendientes

### Notas ancladas al acta (dejar de mover notas en traslados Orbix)

- **Qué:** Modelo B de [`notas_modelo_acta.md`](notas_modelo_acta.md): notas fijas en la DL del acta; expediente agregado; certificado automático solo hacia entidad externa; deprecar `e_notas_otra_region_stgr` / placeholders internos.
- **Progreso (2026-07):** Slices 1–3 y 5 hechos (`EditarPersonaNota`, `Trasladar::copiarNotas` no-op, `ExpedienteNotasPersona`, certificados desacoplados). Herramientas audit/fix en `tools/` (Slices 4 y 6). Tests actualizados al contrato B.
- **Pendiente:** ejecutar migraciones en prod: **`221200` (mapa BD)** → `211200` → `211250` → `211300` (sv+sf); flujo certificado destino externo; cablear `MapaPrefijoActaEsquemaRepository` al grabar notas con acta histórica.
- **Notas:** Mapa SSOT `public.mapa_prefijo_acta_esquema` (búsqueda actas absorbidas + Absorber + repatriación). Ampliar con `INSERT` (o reaplicar `221200`). Diag: `tools/audit/diag_notas_otra_region_mapa.sql`. Auditoría: `php tools/fix/fix_notas_otra_region_a_acta.php --por-prefijo`.

### Migración `ServerConf` → `.env` (y bootstrap unificado)

- **Qué:** Cargar configuración por instalación (rutas, host, dmz, `DIR_PWD`, etc.) vía `.env` / variables de entorno en lugar de (o como capa sobre) constantes en `ServerConf`.
- **Por qué no ahora:** Refactor grande: `ServerConf::*` aparece masivamente (`ConfigGlobal` + muchos entrypoints); hace falta prelude común muy temprano y sustituir `const`/inicializadores de propiedades por lectura en tiempo de ejecución.
- **Notas:** Análisis en conversación; cuidado con `private $dir_base = ServerConf::DIR . '...'` y con `css/*.php`, `scripts/*.js.php`, CLI y tests (`getDIR_PWD()` / modo test).

### Camino interno opcional para `PostRequest` sin HTTP

- **Qué:** Mantener HTTP como contrato base entre `frontend/` y `src/`, válido tanto si front y API están en servidores separados como si comparten instalación. En instalaciones monolito, estudiar una optimización opcional para que `PostRequest::getDataFromUrl('/src/...')` pueda resolver algunas rutas mediante un dispatcher interno que invoque directamente el mismo caso de uso de `application/`, sin abrir una subpetición HTTP.
- **Por qué no ahora:** Mientras haya controladores HTTP con lógica mezclada (`$_POST`, `echo`, `exit`, headers, validación de hash, `ContestarJson`, etc.), no es seguro saltarse HTTP de forma general. Incluir controladores PHP como atajo acoplaría la ejecución a efectos laterales de la capa HTTP.
- **Notas:** Requisito previo para cada endpoint candidato: controlador `src/.../infrastructure/ui/http/controllers/*.php` fino, que solo lea request, invoque un caso de uso/servicio de `application/` y responda con `ContestarJson::enviar`. El futuro dispatcher interno debería ser progresivo: si una ruta está registrada y preparada, llama al caso de uso; si no, fallback a HTTP. No poner esta decisión en `ContestarJson`, cuya responsabilidad debe seguir siendo solo envolver la respuesta JSON.
