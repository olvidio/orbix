<?php

namespace shared\infrastructure;

use DateInterval;
use shared\domain\repositories\ColaMailRepository;
use web\DateTimeLocal;

class consumirColaMail
{
    private $ColaMailRepository;
    public function __construct()
    {

        $this->ColaMailRepository = new ColaMailRepository();
    }
    public function purge()
    {
        // 1 mes
        $ahora = new DateTimeLocal();
        $ahora->sub(new DateInterval('P1M'));
        $date_iso = $ahora->getIso();
        $this->ColaMailRepository->deleteColaMails($date_iso);
    }

    public function seleccionar($limit)
    {
        $aWhere = ['sended' => 'x', '_limit' => $limit];
        $aOperador['sended'] = 'IS NULL';
        return $this->ColaMailRepository->getColaMails($aWhere, $aOperador);
    }

    public function enviar()
    {
        // de 5 en 5 para no consumir mucha memoria
        while ( $cMails = $this->seleccionar(5)) {
            $ahora = new DateTimeLocal();
            foreach ($cMails as $oMail) {
                //$uuid_item = $oMail->getUuid_item();
                $mail_to = $oMail->getMail_to();
                $subject = $oMail->getSubject();
                $message = $oMail->getMessage();
                $headers = $oMail->getHeaders();
                // para pruebas
                //$mail_to = 'dserrabou@gmail.com';
                mail($mail_to, $subject, $message, $headers);
                $oMail->setSended($ahora);
                $this->ColaMailRepository->Guardar($oMail);
            }
        }
    }
}