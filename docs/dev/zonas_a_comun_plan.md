# Zonas SACD a la base comun

Plan para mover las tablas del módulo `zonassacd` de **sv-e** a **comun** y sustituir
la columna `id_zona` de las tablas de centros por una tabla de relación propia.

## Por qué

La zona de un centro está hoy repartida en tres tablas de dos bases distintas y sin
ninguna clave ajena que las sujete:

| Tabla | Base | Quién la escribe | Quién la lee |
|---|---|---|---|
| `u_centros_dl.id_zona` | sv | `ZonaCtrUpdate` (centros sv) | `ZonaCtrLista`, `CentrosPorFiltroOpciones`, `ListasCData`, `ListasDData` |
| `cu_centros_dlf.id_zona` | comun | `ZonaCtrUpdate` (centros sf) | `ZonaCtrLista`, misas, encargos |
| `cu_centros_dl.id_zona` | comun | **nadie** | misas (4 sitios) |

De ahí salen dos problemas concretos. El primero es un fallo en producción: misas
guarda sus tablas en comun y por eso lee los centros de las copias, pero para los
centros de sv pregunta por `cu_centros_dl.id_zona`, que nadie rellena; el resultado
es que los centros de sv no aparecen al filtrar por zona en
`DesplegableCentrosZonaData`, `BuscarPlanCtrData`, `VerEncargosZonaData` y
`VerEncargosCentrosData`.

El segundo es de alcance: el catálogo `zonas` y la tabla `zonas_sacd` viven en sv-e y
se leen por `oDBE`, que en la instalación **sf** apunta a la base sf. Desde sf no hay
forma de obtener nombres de zona ni sacd por zona, así que el plan de misas no es
legible entero desde ese lado por mucho que la relación centro↔zona esté en comun.

La clave ajena nunca fue posible: en `src/zonassacd/db/DBEsquema.php` está el intento
comentado con la razón escrita, «las zonas están en sv-e y los centros en sv».

## Estado comprobado antes de mover

- **No hay ninguna consulta** que cruce `zonas`, `zonas_grupos` o `zonas_sacd` con
  tablas de otra base en la misma sentencia. Todo el cruce se hace en PHP con
  repositorios separados, así que mover las tablas sólo cambia la conexión.
- **No hay vistas ni vistas materializadas** que las incluyan.
- La única clave ajena viva es `zonas_sacd.id_zona → zonas(id_zona) ON DELETE CASCADE`,
  intramódulo, que se conserva si las tres tablas se mueven juntas.
- Secuencias por esquema: `zonas_id_zona_seq`, `zonas_grupos_id_grupo_seq`,
  `zonas_sacd_id_item_seq`.

## Destino

Las tres tablas actuales más una nueva, todas en comun por esquema, heredando de
`global.*` y replicadas a `comun_select`:

```sql
-- padre, en comun: src/zonassacd/db/DB.php
CREATE TABLE IF NOT EXISTS global.zonas_ctr (
    id_schema integer NOT NULL,
    id_ubi    integer NOT NULL,
    id_zona   integer NOT NULL
);

-- hija por esquema: src/zonassacd/db/DBEsquema.php
CREATE TABLE IF NOT EXISTS "H-dlb".zonas_ctr (
    CONSTRAINT zonas_ctr_pkey PRIMARY KEY (id_ubi),
    CONSTRAINT zonas_ctr_id_zona_fkey FOREIGN KEY (id_zona)
        REFERENCES zonas(id_zona) ON DELETE CASCADE
) INHERITS (global.zonas_ctr);
ALTER TABLE "H-dlb".zonas_ctr ALTER id_schema SET DEFAULT public.idschema('H-dlb'::text);
CREATE INDEX IF NOT EXISTS zonas_ctr_id_zona_idx ON "H-dlb".zonas_ctr (id_zona);
```

La clave primaria es `id_ubi` porque un centro pertenece como mucho a una zona, y la
ausencia de fila significa «sin zona»: sustituye al `id_zona IS NULL` de hoy y permite
que `id_zona` sea `NOT NULL`. La clave ajena hacia `zonas` es la que nunca se pudo
poner, y hace innecesaria cualquier limpieza al borrar una zona.

Un detalle de nombres: en sv-e el esquema lleva sufijo (`H-dlbv`) y en comun no
(`H-dlb`). Al mover desaparece el juego de guardar y restaurar
`$this->esquema = ConfigGlobal::mi_region_dl()` que hoy repiten todos los `create_*`
de `DBEsquema`; queda como en `src/misas/db/`, que es el patrón de referencia para
tablas de comun.

## Cambios de código

### Capa `db/`

| Fichero | Cambio |
|---|---|
| `src/zonassacd/db/DB.php` | `permisoGlobalEffective('sfsv-e')` → `'comun'`; `modulosSuscripcionGlobal()` de `['sv-e']` a `['comun']`; añadir `create_zonas_ctr` / `eliminar_zonas_ctr` |
| `src/zonassacd/db/DBEsquema.php` | `addPermisoGlobal('sfsv-e')` → `'comun'`; quitar el override de `esquema` / `role`; `setConexion('sfsv-e')` → `'comun'` en los `llenar_*`; añadir `create_zonas_ctr` / `eliminar_zonas_ctr`; borrar los bloques comentados de la FK a `u_centros_dl` |
| `src/zonassacd/db/DBEsquemaSelect.php` | `'sfsv-e_select'` → `'comun_select'`; `eliminarDeSVESelect` → `eliminarDeComunSelect`; `refreshSubscriptionModulo('sv-e')` → `('comun')`; añadir el espejo de `zonas_ctr` |

### Repositorios

`PgZonaRepository`, `PgZonaGrupoRepository` y `PgZonaSacdRepository` pasan de
`GlobalPdo::get('oDBE')` / `'oDBE_Select'` a `'oDBC'` / `'oDBC_Select'`. Esas
conexiones existen en las tres situaciones (sv, sf y DMZ).

Se añade la pieza nueva: entidad `ZonaCtr`, contrato `ZonaCtrRepositoryInterface` con
`idUbisDeZona`, `zonaDeCentro`, `mapaZonaPorCentro`, `asignar` (con `null` borra la
fila) y `eliminarPorZona`, más `PgZonaCtrRepository` y su registro en
`src/zonassacd/config/dependencies.php`.

### Escritura de la relación

`ZonaCtrUpdate` deja de cargar centros: desaparecen las dependencias de
`CentroDlRepositoryInterface` y `CentroEllasRepositoryInterface` y la bifurcación por
prefijo de `id_ubi`, y queda una llamada a `asignar()` por centro seleccionado. El
borrado de zona ya no necesita limpieza en aplicación: lo hace la clave ajena.

### Lectores

Ocho sitios filtran centros por zona y pasan a resolver primero los `id_ubi` de la
zona y luego cargar los centros por id (`getCentros` ya admite el operador `IN`). Para
no repetir el patrón, un servicio `src/zonassacd/application/services/CentrosDeZona.php`
devuelve los ids de una zona y los activos sin zona.

| Fichero | Qué hace hoy |
|---|---|
| `src/zonassacd/application/ZonaCtrLista.php` | tres ramas (`no`, `no_sf`, zona concreta) |
| `src/misas/application/DesplegableCentrosZonaData.php` | `getCentros(['id_zona' => X])` en las dos copias |
| `src/misas/application/BuscarPlanCtrData.php` | igual |
| `src/misas/application/VerEncargosZonaData.php` | igual |
| `src/misas/application/VerEncargosCentrosData.php` | igual |
| `src/encargossacd/application/CentrosPorFiltroOpciones.php` | filtro `ZONAS_MISAS` |
| `src/encargossacd/application/ListasCData.php` | centros de cada zona |
| `src/encargossacd/application/ListasDData.php` | igual |

En `CentrosPorFiltroOpciones` hay además un fallo aparte que conviene corregir en la
misma pasada: `mergeSvSfCgi` usa `centroDlRepository` también para el lado sf.

### Limpieza posterior

Cuando ya nada lea la columna: quitar `id_zona` de `u_centros_dl`, `cu_centros_dl` y
`cu_centros_dlf`, de las entidades `CentroDl`, `CentroEllos` y `CentroEllas`, dejar
`CuCentrosFila::COLUMNAS_DEL_DESTINO` en `[]`, retirar el `setNullDatos` de
`RenombrarEsquema` y mover las tres entradas de zonas de
`RenombrarEsquemaDefaultsCatalog::svE()` a `::comun()`, con `idschema` sin sufijo.

## Traslado de datos

Las tablas se crean al instalar el módulo, así que para las instalaciones que ya
existen **no** vale una migración SQL: un fichero `__comun.sql` se ejecuta contra
comun, y tanto el registro de módulos (`m0_mods_installed_dl`) como las tablas de
origen están en sv-e. Va un script CLI que tiene las tres conexiones a la vez:

```text
php tools/fix/zonas_a_comun.php --dry-run
php tools/fix/zonas_a_comun.php --apply [--esquema=H-dlb]
```

Por cada esquema con el módulo instalado:

1. Comprobar que existe `"<esquema>v".zonas` en sv-e y que el módulo consta instalado.
2. Crear las tablas en comun y en `comun_select` con `DBEsquema` / `DBEsquemaSelect`.
3. Copiar `zonas`, `zonas_grupos` y `zonas_sacd` conservando los ids, y ajustar las
   secuencias con `SETVAL`.
4. Llenar `zonas_ctr` desde `u_centros_dl.id_zona` (sv) y `cu_centros_dlf.id_zona`
   (comun).
5. Contrastar recuentos antes y después, y avisar si la instalación sf tiene filas
   propias en sus esquemas de zonas: en comun el esquema es compartido entre sv y sf,
   así que ahí habría colisión que decidir a mano.

El script de traslado no borra nada de sv-e. La fase 4 lo hace con las
migraciones `db/migrations/202609061230_*` y `202609061231_*` (menú
devel_db_admin), cuando el código nuevo ya no lee esas tablas.

## Fases

1. **Hecho (código).** Tablas en `DB` / `DBEsquema` / `DBEsquemaSelect`, repos a `oDBC`,
   entidad `ZonaCtr` + `PgZonaCtrRepository`. Traslado:
   `php tools/fix/zonas_a_comun.php --dry-run` / `--apply`.
2. **Hecho.** `ZonaCtrUpdate` escribe en `zonas_ctr` y, de momento, también en las
   columnas antiguas.
3. **Hecho.** Los ocho lectores resuelven los centros por `zonas_ctr`
   (`CentrosDeZona`).
4. **Hecho (código).** Sin doble escritura; `id_zona` fuera de las entidades y
   repos de centros; `CuCentrosFila::COLUMNAS_DEL_DESTINO = []`. DDL en
   `db/migrations/202609061230_quitar_id_zona_centros__{sv,sf,comun}.sql` y
   `202609061231_drop_tablas_zonas_sve__{sv-e,sf}.sql` (el runner aplica
   estructura también en `*_select`).

## Riesgos y decisiones

- **Esquema compartido.** En sv-e cada instalación tiene su esquema (`H-dlbv`,
  `H-dlbf`); en comun hay uno solo (`H-dlb`). Si sf tuviera zonas propias, al mover se
  juntarían con las de sv. Se decide en el `--dry-run`.
- **Quién escribe.** Comun es escribible desde sf, así que «las zonas sólo se
  gestionan desde sv» pasa a ser una regla de aplicación (las pantallas están en el
  menú de sv). Si se quiere sujetar en la base, hace falta un `GRANT` específico.
- **Ventana.** La fase 1 requiere que nadie edite zonas mientras se copian, pero son
  pocas filas por delegación y el traslado es cuestión de segundos.
- **Tests de integración.** Los `PgZona*RepositoryTest` apuntan hoy a sv-e; al cambiar
  la conexión del repositorio pasan a comun sin tocar el test.

## Comprobaciones al cerrar cada fase

`php -l` de lo tocado, unitarios e integración de `zonassacd` y `misas`, `--dry-run`
del script con recuentos por esquema, y prueba manual de la pantalla `zona_ctr` y de
un plan de misas con centros de los dos lados.
