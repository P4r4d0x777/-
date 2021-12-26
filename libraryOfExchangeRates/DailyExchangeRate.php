<?php

class DailyExchangeRate
{
	private $date;
	private $valutes;
	private $realDate;
	private $result;

    /**
     * Установка даты 
     * 
     * @param $__date - дата d/mY
     */
	public function setDate(string $date)
	{
		$this->date = $date;
	}

    /**
     * Получение реальной даты установки
     */
	public function getRealDate() : string
	{
		return ($this->realDate == null) ? "Date don`t set yet" : $this->realDate->format('d/m/Y');
	}

    /**
     * Добавление массива валют
     * 
     * @param array $valutes - массива валют
     */
	public function setValutes($valutes)
	{
		if (!is_array($valutes)) {
			$valutes = [$valutes];
		}

		$this->valutes = $valutes;
	}

    /**
     * Отправка запроса ЦБ
     */
	public function sendRequest()
	{
		$this->result = (new Request(Request::URL_VALUTE_DAILY, [
			'date_req' => ((empty($this->date)) ? null : $this->date)
		]))->sendRequest();
	}
	
    /**
     * Получение результата
     */
    public function getResult()
    {
        $xml = new SimpleXMLElement($this->result);

        $date = (string)$xml->attributes()['Date'];

        $this->realDate = (new DateTime())->setTimestamp(strtotime($date));

        $length = count($this->valutes);

        $valutes = 'CharCode = "'.$this->valutes[0].'"';
        for ($i = 1; $i < $length; $i++) {
            $valutes = ' or CharCode = "'.$this->codes[$i].'"';
        }

        $dateToDB = $this->realDate->format('d/m/Y');

        $xpath = $xml->xpath('Valute['.$valutes.']'); 

        $result = [];

        foreach($xpath as $el) {
            $valute = (string)$el->CharCode;
            $result[$valute] = [
                'CurrencyDate'     => $dateToDB ,
                'CurrencyId'       => (string)$el->attributes()['ID'] ,
                'CurrencyNumCode'  => (string)$el->NumCode ,
                'CurrencyCharCode' => (string)$el->CharCode ,
                'CurrencyName'     => (string)$el->Name ,
                'CurrencyValue'    => (float)(str_replace(',','.',$el->Value)) ,
                'CurrencyNominal'  => (int)$el->Nominal
            ];
        }

        return $result;
    }
}
