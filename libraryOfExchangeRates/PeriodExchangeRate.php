<?php

class PeriodExchangeRate
{
    private $dateFrom;
    private $dateTo;
    private $valute;
    private $result;

    /**
     * Установка даты начала периода
     * @param string $dateFrom - дата начала d/m/Y
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    }
    
    /**
     * Установка даты конца периода
     * @param string $dateTo - дата конца d/mY
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    }

    /**
     * Установка валюты для который смотреть
     * @param string $valute - чаркод валюты
     */
    public function setValute($valute)
    {
        $this->valute = $valute;
    }

    /**
     * Отправка запроса ЦБ
     */
    public function sendRequest()
    {
        $this->result = (new Request(Request::URL_VALUTE_PERIOD, [
			'date_req1' => $this->dateFrom,
			'date_req2' => $this->dateTo,
			'VAL_NM_RQ' => $this->valute
		]))->sendRequest();
    }

    /**
     * Получение результата
     */
    public function getResult()
    {
        // для кэширования т.к запрос не предоставляет название валюты и её код :)
        $EQUIVALENCE = array(
            "R01010"  => array('036','AUD','Австралийский доллар'),
            "R01020A" => array('944','AZN','Азербайджанский манат') ,
            "R01035"  => array('826','GBP','Фунт стерлингов Соединенного королевства') ,
            "R01060"  => array('051','AMD','Армянский драм') ,
            "R01090B" => array('933','BYN','Белорусский рубль') ,
            "R01100"  => array('975','BGN','Болгарский лев') ,
            "R01115"  => array('986','BRL','Бразильский реал') ,
            "R1135"   => array('348','HUF','Венгерский форинт') ,
            "R1200"   => array('344','HKD','Гонконгский доллар') ,
            "R1215"   => array('208','DKK','Датская крона') ,
            "R01235"  => array('840','USD','Доллар США') ,
            "R01239"  => array('978','EUR','Евро') ,
            "R01270"  => array('356','INR','Индийский рупий') ,
            "R01335"  => array('398','KZT','Казахстанский тенге') ,
            "R01350"  => array('124','CAD','Канадский доллар') ,
            "R01370"  => array('417','KGS','Киргизский сом') ,
            "R01375"  => array('156','CNY','Китайский юань') ,
            "R01500"  => array('498','MDL','Молдавский леей') ,
            "R01535"  => array('578','NOK','Норвежская крона') ,
            "R01565"  => array('985','PLN','Польский злотый') ,
            "R01585F" => array('945','RON','Румынский лей') ,
            "R01589"  => array('960','XDR','СДР (специальные права заимствования)') ,
            "R01625"  => array('702','SGD','Сингапурский доллар') ,
            "R01670"  => array('972','TJS','Таджикский сомони') ,
            "R01700J" => array('949','TRY','Турецкая лира') ,
            "R01710A" => array('934','TMT','Новый туркменский манат') ,
            "R01717"  => array('860','UZS','Узбекский сум') ,
            "R01720"  => array('980','UAH','Украинская гривна') ,
            "R01760"  => array('203','CZK','Чешская крона') ,
            "R01770"  => array('752','SEK','Шведская крона') ,
            "R01775"  => array('756','CHF','Швейцарский франк') ,
            "R01810"  => array('710','ZAR','Южноафриканский рэнд') ,
            "R01815"  => array('410','KRW','Вона Республики Корея') ,
            "R01820"  => array('392','JPY','Японская иена') 
        );

        $xml = new \SimpleXMLElement($this->result);

        $xpath = $xml->xpath('Record');

        $result=[];

        foreach ($xpath as $el) {
            $date = (string)$el->attributes()['Date'];

            $realDate = (new DateTime())->setTimestamp(strtotime($date))->format('d/m/Y');

            $eqv = [];

            foreach ($EQUIVALENCE as $key => $value) {
                if($key == (string)$el->attributes()['Id'])
                    $eqv = $value;
            }

            $result[$realDate] = [
                'CurrencyDate'     => $realDate,
                'CurrencyId'       => (string)$el->attributes()['Id'] ,
                'CurrencyNumCode'  => $eqv[0] ,
                'CurrencyCharCode' => $eqv[1] ,
                'CurrencyName'     => $eqv[2] ,
                'CurrencyValue'    => (float)(str_replace(',','.',$el->Value)) ,
                'CurrencyNominal'  => (int)$el->Nominal
            ];
        }

        return $result;
    }
}