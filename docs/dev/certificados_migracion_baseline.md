# Baseline migracion `certificados`

Documento de cierre DI del modulo `src/certificados/` siguiendo el patron de
`cambios`, `casas`, `personas`, `usuarios`, `dossiers` y `planning`.

---

## Inventario inicial (antes del cierre DI)

| Capa | Ficheros con `$GLOBALS['container']` | Ocurrencias |
|------|--------------------------------------|------------:|
| `infrastructure/ui/http/controllers/` | 12 | ~28 |
| `application/` | 2 | 4 |
| `domain/` | 6 | ~18 |
| `db/` | 2 | 4 |
| **Total container** | **19 ficheros** | **~54** |

| Capa | Ficheros con `$GLOBALS['oDB*']` |
|------|--------------------------------:|
| `infrastructure/persistence/postgresql/` | 2 |
| `domain/CertificadoEmitidoDelete.php` | 1 |

| Frontend `use src\` | Ficheros |
|--------------------|----------|
| — | **0** |

PHPStan (`phpstan-nobaseline.neon`): **183** errores en `src/certificados/`.

---

## Cierre DI (2026-06-06)

### Estaticos / `new` convertidos a instancia + DI

| Clase | Antes | Despues |
|-------|-------|---------|
| `CertificadoEmitidoSelect` | `::getCamposVista()` | instancia + constructor (3 repos) |
| `CertificadoEmitidoEnviar` | `::enviar()` | `execute()` + constructor (4 deps) |
| `CertificadoEmitidoDelete` | `new` + `$GLOBALS` | constructor `ConnectionRepositoryFactory` |
| `CertificadoRecibidoDelete` | `new` + `$GLOBALS` | constructor (repo + factory) |
| `CertificadoEmitidoUpload` | `new` + `$GLOBALS` | constructor (repo + factory) |
| `CertificadoRecibidoUpload` | idem | idem |
| `Select_certificados_de_una_persona` | `$GLOBALS` en `getTabla()` | constructor `CertificadoRecibidoRepositoryInterface` |
| `CertificadoEmitidoAdjuntarFormData` | `::execute()` | instancia + `PersonaFinderService` |
| `CertificadoRecibidoAdjuntarFormData` | idem | idem |
| `CertificadoRecibidoModificarFormData` | `$GLOBALS` | instancia (2 repos) |
| `CertificadoEmitidoUploadFirmadoFormData` | `$GLOBALS` | instancia (repo + finder) |
| `DBEsquema` / `DBEsquemaSelect` | `$GLOBALS` | `DependencyResolver::get(DelegacionRepositoryInterface)` |

### Repositorios Pg* (2)

| Repositorio | PDO |
|-------------|-----|
| `PgCertificadoEmitidoRepository` | `GlobalPdo::get('oDB')` |
| `PgCertificadoRecibidoRepository` | `GlobalPdo::get('oDB')` |

### HTTP controllers (20)

Todos en `infrastructure/ui/http/controllers/` usan `DependencyResolver::get()`
para casos de uso o repos cross-modulo. Entrada POST via `input_int` /
`input_string` / `input_string_list` donde aplica.

Controllers con logica inline (`certificado_emitido_guardar`,
`certificado_recibido_guardar`, `certificado_emitido_imprimir_mpdf_datos`, etc.)
resuelven repos via `DependencyResolver::get()` — deuda futura: extraer
`*Guardar` / `*ImprimirData` use cases al estilo `casas`.

### `src/certificados/config/dependencies.php`

Registra 2 repositorios + 11 casos de uso / servicios de dominio:

- Repos: `CertificadoEmitido`, `CertificadoRecibido`
- Application: `CertificadoEmitidoAdjuntarFormData`, `CertificadoEmitidoUploadFirmadoFormData`,
  `CertificadoRecibidoAdjuntarFormData`, `CertificadoRecibidoModificarFormData`
- Domain: `CertificadoEmitidoDelete`, `CertificadoEmitidoEnviar`, `CertificadoEmitidoSelect`,
  `CertificadoEmitidoUpload`, `CertificadoRecibidoDelete`, `CertificadoRecibidoUpload`,
  `Select_certificados_de_una_persona`

Repos cross-modulo (`Local`, `Delegacion`, `Asignatura`, `PersonaNota*`, `Anuncio`,
`Trasladar`, `PersonaFinderService`, etc.) se resuelven desde los `dependencies.php`
de sus modulos.

### Helper nuevo

- `application/support/CertificadosSession::esquemaRegionStgr()` — lectura tipada de
  `$_SESSION['session_auth']['esquema']` sin `$GLOBALS['container']`.

---

## Resultado del cierre DI

| Criterio | Antes | Despues |
|----------|------:|--------:|
| `$GLOBALS['container']` en `src/certificados/` | ~54 | **0** |
| `$GLOBALS['oDB*']` en repos/domain Pg* | 3 | **0** |
| Controllers HTTP con `DependencyResolver::get()` | 0/20 | **20/20** |
| `application/` con constructor DI | 0/4 | **4/4** instancia |
| Casos de uso en `dependencies.php` | 2 repos | **13** entradas `autowire()` |
| Frontend `use src\` | 0 | **0** |

---

## PHPStan incremental (`phpstan-nobaseline.neon`)

| Fecha | Comando | Errores |
|-------|---------|--------:|
| 2026-06-06 (pre-cierre) | `composer phpstan:file -- src/certificados/` | **183** |
| 2026-06-06 (cierre DI) | idem | **0** |

Correcciones principales: contratos con tipos de retorno, repos Pg* con guards
`$stmt === false`, entidades con propiedades inicializadas, controllers con
`input_*` helpers, `textos_certificados.php` con `@var` de contexto include,
comparacion `NivelStgrId::R` en impresion mPDF (sustituye `'r'` legacy).

---

## Tests

```bash
vendor/bin/phpunit tests/unit/certificados/
vendor/bin/phpunit tests/integration/certificados/
```

Tests unitarios de application actualizados a constructor DI (sin mock de
`$GLOBALS['container']` en FormData). Tests de dominio con DB usan
`DependencyResolver::get()` + `setoDbl()` para conexion de test.

---

## Deuda futura (fuera de scope DI)

- Extraer use cases `CertificadoEmitidoGuardar`, `CertificadoRecibidoGuardar`,
  `CertificadoEmitidoImprimirMpdfData` desde controllers inline.
- Widget `Select_certificados_de_una_persona`: mover a `application/` (patron
  `Select_notas_de_una_persona` en dossiers).
