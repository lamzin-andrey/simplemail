<?php

namespace Landlib;

class SimpleMail
{
	//errors
	private  $isUnknownDestination = true;	//false when recipient is set. когда получатель письма указан
	private  $isUnknownSender      = true;	//false when sender is set. когда отправитель письма указан
	
	private  $contentType          = 'text/plain';
	private  $encoding             = 'UTF-8';
	private  $subject              = '';
	private  $body                 = '';
	private  $fromSection          = '';  
	private  $toSection            = '';
	private  $boundary             = null;  
	private  $encodedFiles         = [];
	private $_fromSecurityHeader   = ''; //Use for header '-fname@host.com'
	/*
	 * @param string $charset email encoding. Recomended UTF-8
	 * Принимает кодировку письма. По умолчанию стоит UTF-8.
	 * Тестировался также с WINDOWS-1251, но рекомендуется использовать UTF-8
	 * Все текстовые аргументы методам класса должны передаваться в той же кодировке
	 * что была передана в конструктор
	* */
	public function __construct($charset = 'UTF-8')
	{
		$this->encoding = $charset;
		$this->boundary  = md5(uniqid(time()));
	}
	/*
	 * Mail subject
	 * Установка темы письма
	*/
	public function setSubject($subject)
	{
		$this->subject = $subject;
	}
	/*
	 * Set html text
	*/
	public function setHtmlText($htmlText)
	{
		$this->body        = $htmlText;
		$this->contentType = 'text/html';
	}
	/**
	 * Compatible with Swift_Mailer.
	 * If $contentType != 'text/html' alwaus will using 'text/plain'
	 * @param string $body
	 * @param string $contentType
	 * @param string $charset
	*/
	public function setBody($body, $contentType = null, $charset = null)
	{
		$this->encoding = $charset;
		if ($contentType == 'text/html') {
			$this->setHtmlText($body);
		} else {
			$this->setTextWithImages($body);
		}
	}
	/*
	 * Mail sender address. 
	 * @param mixed $address supporeted formats:
	 *          ['johnsmith@gmail.com' => 'John Smith'] //It recomended
	 * 			'vasyavetrov@mail.ru'
	 * 			['johnsmith@gmail.com' => 'John Smith', 'vv@gmail.com' => 'Vladimir V']
	 * 			'vasyavetrov@mail.ru, vv@gmail.com'
	 * Адрес отправителя (отправителей) можeт быть установлен в одном из следующих форматов:
	 * (рекомендуемый) массив array("vasyavetrov@gmail.com"=>"John Smith")
	 * 'vasyavetrov@mail.ru'
	 *['johnsmith@gmail.com' => 'John Smith', 'vv@gmail.com' => 'Vladimir V']
	 *'vasyavetrov@mail.ru, vv@gmail.com'
	*/
	public function setAddressFrom($address)
	{
		$result = $this->createMailsArray($address);
		if (count($result) > 0) 
		{
			$this->_setFrom($result);
			$this->isUnknownSender = false;		
		}
		else $this->isUnknownSender = true;
	}
	/*
	 * Mail sender address. (Compatible with Swift_Mailer interface)
	 * @param string $sAddress
	 * @param string $sName = null
	*/
	public function setFrom($sAddress, $sName = null)
	{
		$arg = [$sAddress => $sAddress];
		if ($sName) {
			$arg = [$sAddress => $sName];
		}
		$this->setAddressFrom($arg);
	}
	 /*
	  * Mail recipient (recipients) address (addresses).
	  * @param mixed $address supporeted formats:
	  * Адрес получателя (получателей) можeт быть установлен в одном из следующих форматов:
	  * 
	  *  ['vasyavetrov@mail.ru' => 'Vlad Zepesh', 'johnsmith@mail.ru'=>'John Smith'] // (is recommended / рекомендуемый)
	  *  'vasyavetrov@mail.ru'
	  *  'vasyavetrov@mail.ru,lusyavetrova@mail.ru'
	  *  ['vasyavetrov@mail.ru', 'lusyavetrova@mail.ru']
	* */ 
	public function setAddressTo($address)
	{
		$result = $this->createMailsArray($address);
		if (count($result) > 0) 
		{
			$this->_setTo($result);
			$this->isUnknownDestination = false;		
		}
		else $this->isUnknownDestination = true;
	}
	/*
	 * Mail recipients address. (Compatible with Swift_Mailer interface)
	 * @param string $sAddress
	 * @param string $sName = null
	*/
	public function setTo($sAddress, $sName = null)
	{
		$arg = [$sAddress => $sAddress];
		if ($sName) {
			$arg = [$sAddress => $sName];
		}
		$this->setAddressTo($arg);
	}
	/*
	 * Text can containts user tags for insertinline images 
	 * Текст может содержать произвольные уникальные тэги для вставки inline изображений
	 * @param string $sText
	 * Example / Пример:
	 * setTextWithImages('Hello, it smile {smile1},
	 * 				 and it smile too {smile2}", 
	 * [
	 * 	'{smile1}' => 'absolute/path/to/image/on/hard/drive', //абсолютный_путь_к_вашему_изображению_на_жестком диске
	 *  '{smile2}' => 'absolute/path/to/second/image/on/hard/drive' //абсолютный_путь_к_вашему_вторму_изображению_на_жестком диске,
	 * ]
	 * )
	 * @param array $images
	*/
	public function setTextWithImages($text, $images = null)
	{
		$contentType = 'text/plain';
		if (is_array($images))
		{
			$contentType = 'text/html';
			foreach ($images as $tpl=>$img)
			{
				if (strpos($text, $tpl) !== false)
				{
					if (file_exists($img))
					{
						$cid = $this->embed($img);
						$img = "<img src = 'cid:$cid' />"; 
						$text = str_replace($tpl, $img, $text);
					}
					else $text = str_replace($tpl, "", $text);
				}
			}
			$text = str_replace("\r", "", $text);
			$text = str_replace("\n", "<br>", $text);
			$text = "<html><body>$text</body></html>";
		}
		$this->contentType = $contentType;
		$this->body        = $text;
	}
	/*
	 * Add file as attachment
	 * @param string $pathToFile
	 * @param string $disposition = 'attachment' //or 'inline'
	 * Прикрепить файл как attachment. Можно сменить тип disposition на inline, по идее тогда должен вернуть cid
	*/
	public function attachFile($pathToFile, $disposition = 'attachment')
	{
		if(!file_exists($pathToFile)) return "file not found";
		if (($disposition != "inline")&&($disposition != "attachment")) return "unknown type disposition";
		$cid = null;
		$file = $pathToFile;
		$fileT = explode("/", $file);
		$fileT = $fileT[count($fileT) - 1];
		$boundary = $this->boundary;
		$mimeType  = $this->mimeType($file);
		$filePart  = "--$boundary\n";
		$filePart .= "Content-Disposition: $disposition; filename=$fileT\n";
		$filePart .= "Content-Type: $mimeType; name=$fileT\n";
		if ($disposition == "inline") 
		{
			$cid      = uniqid("$fileT@", true);
			$filePart .= "Content-ID: <$cid>\n";	
		}
		$filePart .= "Content-Transfer-Encoding: base64\r\n\r\n";
		$filePart .= chunk_split($this->encodeFileBase64($file));
		$this->encodedFiles[] = $filePart;
		return $cid;
	}
	/*
	 * Send email
	 * Собственно, отправка письма 
	 * @return mixed string with error message or bool true if send success or bool false if send fail
	*/
	public function send()
	{
		if ($this->isUnknownDestination) {
			return 'unknown recepient';
		}
		if ($this->isUnknownSender) {
			return 'unknown sender';
		}
		try {
			return $this->buildMail();
		} catch(Exception $exc) {
			echo $exc->getMessage()."\r\n";
		}
		return false;
	}
	//-----------------------------------------------------
	
	private function createMailsArray($address)
	{
		$result = array();
		if (is_array($address))
		{
			
			foreach ($address as $addr=>$recipient)
			{
				if ($this->checkMail($addr))
				{
					$result[$addr] = $recipient;
				}
				else if ($this->checkMail($recipient)) $result[] = $recipient;
			}
		}
		elseif (strpos($address, ",") !== false)
		{
			$result = array();
			$list   = explode(",", $address);
			foreach ($list as $i)
			{
				if ($this->checkMail($i)) $result[] = $i;
			}
		}
		elseif ($this->checkMail($address)) $result[] = $address;
		return $result;	
	}
	
	private function checkMail($candidateToEmailAddress)
	{		
		/*$ea = new CEmailAttribute();
		$v  = new CValidationContext();
		$v->SetValue($candidateToEmailAddress);
		if ($ea->IsValid($v)) return true;
		return false;*/
		return true;
	}
	
	private function _setTo($addresses)
	{
		$this->parseMailsArray("to", $addresses);
	}
	
	private function _setFrom($addresses)
	{
		$this->parseMailsArray("from", $addresses);
	}
	
	private function parseMailsArray($typeSection, $addresses)
	{
		$r2 = array();	//составные адресаты (email=> Имя получателя)
		$r1 = array();	//
		foreach($addresses as $key=>$item)
		{
			if ($this->checkMail($key))
			{
				if ($typeSection == "from") {
					if (!$this->_fromSecurityHeader) {
						$this->_fromSecurityHeader = '-f' . $key;
					}
					$r2[] = "=?$this->encoding?Q?".str_replace("+", " ", str_replace("%", "=",urlencode($item)))."?= <$key>";				
				}
				else if ($typeSection == "to") $r2[] = "=?$this->encoding?Q?".str_replace("+", " ", str_replace("%", "=",urlencode($item)))."?=\n <$key>";				
			}
			else if ($this->checkMail($item)) $r1[] = $item; 
		}
		if (count($r2) != 0)
		{
			if ($typeSection == "from")$this->fromSection = join ("        ,", $r2);
			else if ($typeSection == "to")$this->toSection = join ("        ,", $r2);
		}
		else if (count($r1) != 0) 
		{
			if ($typeSection == "from")$this->fromSection = join (" ,", $r1);
			else if ($typeSection == "to") $this->toSection = join (" ,", $r1);
		}
	}
	
	private function buildMail()
	{	
		$boundary = $this->boundary;
		//Не надо удалять комментарий в следующей строчке, он мне нравится сильно
		//$this->toSection = "=?UTF-8?Q?".str_replace("%", "=",urlencode("Андрею"))."?=\n <lamzin@benefis.ru>";	
		$header  = "From: $this->fromSection\n";
		$header .= "To:   $this->toSection"."\n";
		$header .= "Mime-Version: 1.0\n";
		$header .= "Content-Type: multipart/mixed; boundary=$boundary\n\n";		
		$bodyHeader  = "--$boundary\n";
		$bodyHeader .= "Content-type: $this->contentType; charset=$this->encoding\n";
		$bodyHeader .= "Content-Transfer-Encoding: base64\r\n\r\n";
		//Далее код прикрепляющий файлы
		$files = "";
		if (count($this->encodedFiles) > 0) $files =  join("\r\n\r\n", $this->encodedFiles)."\r\n\r\n";
		return mail("",
				"=?$this->encoding?B?" . base64_encode($this->subject) . "?=",
				$bodyHeader ."\r\n\r\n". chunk_split(base64_encode($this->body)).$files,
				$header, $this->_fromSecurityHeader);
		/*
		 * mail(
				$address->Email,
				"=?UTF-8?B?" . base64_encode($message->Subject) . "?=",
				$message->TplBodyHeader ."\r\n\r\n". chunk_split(base64_encode($messageBody)). $message->TplEncodeFiles,
				$header);
		 * */		
	} 

	private function embed($img)
	{
		$imgT = explode("/", $img);
		$imgT = $imgT[count($imgT) - 1];
		$boundary = $this->boundary;
		$cid      = uniqid("$imgT@", true);
		// заголовки и данные прикрепленных файлов
		$mimeType  = $this->mimeType($img);
		$filePart  = "--$boundary\n";
		$filePart .= "Content-Disposition: inline\n";
		$filePart .= "Content-Type: $mimeType\n";
		$filePart .= "Content-ID: <$cid>\n";
		$filePart .= "Content-Transfer-Encoding: base64\r\n\r\n";
		$filePart .= chunk_split($this->encodeFileBase64($img));
		//$filePart .= "--$boundary--";
		$this->encodedFiles[] = $filePart;
		return $cid;
	}
	
	private  function encodeFileBase64($filePath) 
	{
		if(is_file($filePath)) 
		{
			$fh = fopen($filePath,"rb");
			$encodeFile = base64_encode(fread($fh, filesize($filePath)));
			return $encodeFile;
		}
		return null;
	}
	
	private function mimeType($file) 
	{
		switch (pathinfo($file, PATHINFO_EXTENSION))
		{
			case "jpg":
			case "jpeg":
				return image_type_to_mime_type(IMAGETYPE_JPEG);
			case "gif":
				return image_type_to_mime_type(IMAGETYPE_GIF);
			case "png":
				return image_type_to_mime_type(IMAGETYPE_PNG);
			case "bmp":
				return image_type_to_mime_type(IMAGETYPE_BMP);
			default:
				return "application/octet-stream";
		}
	}
}//end class definition
