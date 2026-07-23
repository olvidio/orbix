-- Separar prefijo de acta pegado al número: «dlb156/93» → «dlb 156/93».
-- Si el resto tiene espacios espurios («M1 3/20», «Eu4 75/12»), los elimina:
--   → «M 13/20», «Eu 475/12».
--
-- Prefijo = el más largo del snapshot publicf._mig_mapa_prefijo_acta_esquema
--   (SSOT en BD comun; cargado por 211120) que encaje lower(trim(acta)) ~ '^pref[0-9]'.
-- Idempotente: tras el espacio, deja de coincidir.
-- No toca placeholders (id_situacion = 13).
--
-- Orden: 211100(comun) → 211110(export) → 211120(snapshot) → 211140 → …
-- Serie sf.

DO $$
DECLARE
    r RECORD;
    pref text;
    nueva text;
    n_ok bigint := 0;
    n_mapa bigint := 0;
BEGIN
    IF to_regclass('publicf._mig_mapa_prefijo_acta_esquema') IS NULL THEN
        RAISE EXCEPTION
            'Falta publicf._mig_mapa_prefijo_acta_esquema (ejecutar 211120 antes)';
    END IF;

    SELECT count(*) INTO n_mapa FROM publicf._mig_mapa_prefijo_acta_esquema;
    IF n_mapa < 1 THEN
        RAISE EXCEPTION
            'Snapshot mapa vacío (ejecutar 211100+211110 comun y 211120 sf)';
    END IF;

    FOR r IN
        SELECT n.acta, n.tableoid, n.ctid
        FROM publicf.e_notas n
        WHERE n.id_situacion IS DISTINCT FROM 13
          AND nullif(trim(n.acta), '') IS NOT NULL
          AND EXISTS (
              SELECT 1
              FROM publicf._mig_mapa_prefijo_acta_esquema m
              WHERE lower(trim(n.acta)) ~ ('^' || m.pref || '[0-9]')
          )
        ORDER BY n.acta
    LOOP
        SELECT m.pref
          INTO pref
        FROM publicf._mig_mapa_prefijo_acta_esquema m
        WHERE lower(trim(r.acta)) ~ ('^' || m.pref || '[0-9]')
        ORDER BY length(m.pref) DESC
        LIMIT 1;

        nueva := left(trim(r.acta), length(pref))
            || ' '
            || regexp_replace(
                   substr(trim(r.acta), length(pref) + 1),
                   '[[:space:]]+',
                   '',
                   'g'
               );

        IF nueva IS DISTINCT FROM r.acta THEN
            UPDATE publicf.e_notas
            SET acta = nueva
            WHERE tableoid = r.tableoid
              AND ctid = r.ctid;
            n_ok := n_ok + 1;
        END IF;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'separar prefijo acta pegado sf: actualizadas=%s', n_ok
    ));
END $$;
