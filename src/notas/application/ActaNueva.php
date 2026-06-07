<?php

namespace src\notas\application;

use function src\shared\domain\helpers\input_string;
use function src\shared\domain\helpers\input_int;

use src\shared\config\ConfigGlobal;
use src\notas\application\support\ActaDlGuard;
use src\notas\application\support\ActaTribunalSync;
use src\notas\domain\contracts\ActaDlRepositoryInterface;
use src\notas\domain\entity\Acta;
use src\shared\domain\value_objects\DateTimeLocal;

final class ActaNueva
{

    public function __construct(
        private readonly ActaDlRepositoryInterface $actaDlRepository,
        private readonly ActaTribunalSync $actaTribunalSync,
    ) {
    }

    /**
     * @param array<string, mixed> $input
     */
    public function execute(array $input): string
    {
        $acta = input_string($input, 'acta');
        $aSel = (array)($input['sel'] ?? []);
        if ($aSel !== []) {
            $sel0 = $aSel[0];
            if (is_string($sel0)) {
                $part = strtok($sel0, '#');
                $acta = urldecode(is_string($part) ? $part : '');
            }
        }

        $miDele = ConfigGlobal::mi_delef();
        $err = ActaDlGuard::ensureOwnership($acta, $miDele, 'nueva');
        if ($err !== '') {
            return $err;
        }

        $oF_acta = null;
        $f_acta = input_string($input, 'f_acta');
        if (!empty($f_acta)) {
            $oF_acta = DateTimeLocal::createFromLocal($f_acta);
        }

        $repo = $this->actaDlRepository;
        $oActa = new Acta();
        $oActa->setActa($acta);
        $oActa->setId_asignatura(input_int($input, 'id_asignatura'));
        $oActa->setId_activ(input_int($input, 'id_activ'));
        $oActa->setF_acta($oF_acta);
        // La fecha debe fijarse antes para que inventarActa tenga referencia.
        $valor = Acta::inventarActa($acta, $oF_acta);
        $oActa->setActa($valor);
        $oActa->setLibro(input_int($input, 'libro'));
        $oActa->setPagina(input_int($input, 'pagina'));
        $oActa->setLinea(input_int($input, 'linea'));
        $oActa->setLugar(input_string($input, 'lugar'));
        $oActa->setObserv(input_string($input, 'observ'));

        $error = '';
        if ($repo->Guardar($oActa) === false) {
            $error .= _("hay un error, no se ha guardado");
            $error .= "\n" . $repo->getErrorTxt();
        }

        $examinadores = [];
        foreach ((array) ($input['examinadores'] ?? []) as $ex) {
            if (is_string($ex) && $ex !== '') {
                $examinadores[] = $ex;
            }
        }
        $error .= $this->actaTribunalSync->rebuild($valor, $examinadores);

        return $error;
    }
}
