<?php

namespace shared\domain\entity;
	use shared\domain\ColaMailId;
    use web\DateTimeLocal;
	use web\NullDateTimeLocal;
/**
 * Clase que implementa la entidad cola_mails
 *
 * @package orbix
 * @subpackage model
 * @author Daniel Serrabou
 * @version 2.0
 * @created 1/3/2024
 */
class ColaMail {

	/* ATRIBUTOS ----------------------------------------------------------------- */

	 private  ColaMailId $uuid_item;
	/**
	 * Mail_to de ColaMail
	 *
	 * @var string|null
	 */
	 private string|null $smail_to = null;
	/**
	 * Message de ColaMail
	 *
	 * @var string|null
	 */
	 private string|null $smessage = null;
	/**
	 * Subject de ColaMail
	 *
	 * @var string|null
	 */
	 private string|null $ssubject = null;
	/**
	 * Headers de ColaMail
	 *
	 * @var string|null
	 */
	 private string|null $sheaders = null;
	/**
	 * Writed_by de ColaMail
	 *
	 * @var string|null
	 */
	 private string|null $swrited_by = null;
	/**
	 * Sended de ColaMail
	 *
	 * @var DateTimeLocal|null
	 */
	 private DateTimeLocal|null $dsended = null;

	/* MÉTODOS PÚBLICOS ----------------------------------------------------------*/

	/**
	 * Establece el valor de todos los atributos
	 *
	 * @param array $aDatos
	 * @return ColaMail
	 */
	public function setAllAttributes(array $aDatos): ColaMail
	{
		if (array_key_exists('uuid_item',$aDatos))
		{
            $ColaMailId = new ColaMailId($aDatos['uuid_item']);
			$this->setUuid_item($ColaMailId);
		}
		if (array_key_exists('mail_to',$aDatos))
		{
			$this->setMail_to($aDatos['mail_to']);
		}
		if (array_key_exists('message',$aDatos))
		{
			$this->setMessage($aDatos['message']);
		}
		if (array_key_exists('subject',$aDatos))
		{
			$this->setSubject($aDatos['subject']);
		}
		if (array_key_exists('headers',$aDatos))
		{
			$this->setHeaders($aDatos['headers']);
		}
		if (array_key_exists('writed_by',$aDatos))
		{
			$this->setWrited_by($aDatos['writed_by']);
		}
		if (array_key_exists('sended',$aDatos))
		{
			$this->setSended($aDatos['sended']);
		}
		return $this;
	}
	public function getUuid_item(): ColaMailId
	{
		return $this->uuid_item;
	}
	/**
	 *
	 * @param  $uuid_item
	 */
	public function setUuid_item(ColaMailId $uuid_item): void
	{
		$this->uuid_item = $uuid_item;
	}
	/**
	 *
	 * @return string|null $smail_to
	 */
	public function getMail_to(): ?string
	{
		return $this->smail_to;
	}
	/**
	 *
	 * @param string|null $smail_to
	 */
	public function setMail_to(?string $smail_to = null): void
	{
		$this->smail_to = $smail_to;
	}
	/**
	 *
	 * @return string|null $smessage
	 */
	public function getMessage(): ?string
	{
		return $this->smessage;
	}
	/**
	 *
	 * @param string|null $smessage
	 */
	public function setMessage(?string $smessage = null): void
	{
		$this->smessage = $smessage;
	}
	/**
	 *
	 * @return string|null $ssubject
	 */
	public function getSubject(): ?string
	{
		return $this->ssubject;
	}
	/**
	 *
	 * @param string|null $ssubject
	 */
	public function setSubject(?string $ssubject = null): void
	{
		$this->ssubject = $ssubject;
	}
	/**
	 *
	 * @return string|null $sheaders
	 */
	public function getHeaders(): ?string
	{
		return $this->sheaders;
	}
	/**
	 *
	 * @param string|null $sheaders
	 */
	public function setHeaders(?string $sheaders = null): void
	{
		$this->sheaders = $sheaders;
	}
	/**
	 *
	 * @return string|null $swrited_byd
	 */
	public function getWrited_by(): ?string
	{
		return $this->swrited_by;
	}
	/**
	 *
	 * @param string|null $swrited_by
	 */
	public function setWrited_by(?string $swrited_by = null): void
	{
		$this->swrited_by = $swrited_by;
	}
	/**
	 *
	 * @return DateTimeLocal|NullDateTimeLocal|null $dsended
	 */
	public function getSended(): DateTimeLocal|NullDateTimeLocal|null
	{
        return $this->dsended?? new NullDateTimeLocal;
	}
	/**
	 * 
	 * @param DateTimeLocal|null $dsended
	 */
	public function setSended(DateTimeLocal|null $dsended = null): void
	{
        $this->dsended = $dsended;
	}
}