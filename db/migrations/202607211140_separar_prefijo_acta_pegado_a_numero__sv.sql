-- Separar prefijo de acta pegado al número: «dlb156/93» → «dlb 156/93».
-- Si el resto tiene espacios espurios («M1 3/20», «Eu4 75/12»), los elimina:
--   → «M 13/20», «Eu 475/12».
--
-- Prefijo = el más largo de public.mapa_prefijo_acta_esquema que encaje
--   lower(trim(acta)) ~ '^pref[0-9]'.
-- Idempotente: tras el espacio, deja de coincidir.
-- No toca placeholders (id_situacion = 13).
--
-- Orden: después de 211100 (mapa), antes de 211150 / 211250 / 211300 / 222000
-- (para que repatriar/mover usen el prefijo ya separado).
-- Serie sv.

DO $$
DECLARE
    r RECORD;
    pref text;
    nueva text;
    n_ok bigint := 0;
    n_mapa bigint := 0;
BEGIN
    SELECT count(*) INTO n_mapa FROM public.mapa_prefijo_acta_esquema;
    IF n_mapa < 1 THEN
        RAISE EXCEPTION
            'Falta public.mapa_prefijo_acta_esquema (ejecutar 202607211100 antes)';
    END IF;

    FOR r IN
        SELECT n.acta, n.tableoid, n.ctid
        FROM publicv.e_notas n
        WHERE n.id_situacion IS DISTINCT FROM 13
          AND nullif(trim(n.acta), '') IS NOT NULL
          AND EXISTS (
              SELECT 1
              FROM public.mapa_prefijo_acta_esquema m
              WHERE lower(trim(n.acta)) ~ ('^' || m.pref || '[0-9]')
          )
        ORDER BY n.acta
    LOOP
        SELECT m.pref
          INTO pref
        FROM public.mapa_prefijo_acta_esquema m
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
            UPDATE publicv.e_notas
            SET acta = nueva
            WHERE tableoid = r.tableoid
              AND ctid = r.ctid;
            n_ok := n_ok + 1;
        END IF;
    END LOOP;

    PERFORM public.migracion_aviso(format(
        'separar prefijo acta pegado sv: actualizadas=%s', n_ok
    ));
END $$;
