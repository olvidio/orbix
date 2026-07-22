-- Prefijo libre «?»: anteponer la sigla del esquema donde está la fila.
-- Ej.: en Aes-crAesv, «?» → «crAes ?».
--
-- Solo reescribe texto de `acta` en e_notas_otra_region_stgr.
-- No mueve filas ni toca el mapa. Idempotente (solo si el 1.er token es ?).
--
-- Serie sv. sf: mismo script (no-op si no hay filas).

DO $$
DECLARE
    origen text;
    base_esq text;
    sigla text;
    n_upd bigint;
    n_total bigint := 0;
BEGIN
    FOR origen IN
        SELECT n.nspname
        FROM pg_class c
        JOIN pg_namespace n ON n.oid = c.relnamespace
        WHERE c.relname = 'e_notas_otra_region_stgr'
          AND n.nspname NOT LIKE 'pg_%'
        ORDER BY 1
    LOOP
        base_esq := regexp_replace(origen, '[vf]$', '');
        sigla := NULL;

        IF position('-cr' IN lower(base_esq)) > 0 THEN
            sigla := 'cr' || substring(base_esq FROM position('-cr' IN lower(base_esq)) + 3);
        ELSIF split_part(base_esq, '-', 1) = split_part(base_esq, '-', 2)
              AND split_part(base_esq, '-', 1) <> '' THEN
            sigla := split_part(base_esq, '-', 1);
        ELSIF lower(split_part(base_esq, '-', 2)) LIKE 'dl%' THEN
            sigla := split_part(base_esq, '-', 2);
        END IF;

        IF sigla IS NULL OR sigla = '' THEN
            CONTINUE;
        END IF;

        EXECUTE format(
            $sql$
            UPDATE %I.e_notas_otra_region_stgr
            SET acta = %L || ' ' || trim(acta)
            WHERE id_situacion IS DISTINCT FROM 13
              AND trim(coalesce(acta, '')) <> ''
              AND trim(split_part(trim(acta), ' ', 1)) = '?'
            $sql$,
            origen,
            sigla
        );
        GET DIAGNOSTICS n_upd = ROW_COUNT;
        n_total := n_total + n_upd;
        IF n_upd > 0 THEN
            PERFORM public.migracion_aviso(format(
                '?→%s en %s: %s filas', sigla, origen, n_upd
            ));
        END IF;
    END LOOP;

    PERFORM public.migracion_aviso(format('reescribir acta ? sv: total=%s', n_total));
END $$;
