<?php 

class Request
{
    private $url;
    
    const URL_VALUTE_DAILY = 'http://www.cbr.ru/scripts/XML_daily.asp';
	const URL_VALUTE_PERIOD = 'http://www.cbr.ru/scripts/XML_dynamic.asp';

    public function __construct($url, $dateReq)
    {
        foreach ($dateReq as $key => $value) {
            if(empty($value)) {
                unset($dateReq[$key]);
            }
        }

        $this->url = $url.((empty($dateReq)) ? '' : '?'.http_build_query($dateReq));
    }

    /**
     * Отправка запроса при помощи создания нового сеанса cURL и загрузки веб-страницы
     */
    public function sendRequest()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
		$info   = curl_getinfo($ch);
		$error  = curl_error($ch);

        curl_close($ch);

        if($error) {
            throw new Exception($error);
        }

        if($info['http_code'] == 404) {
            throw new Exception('Неверный URL');
        }

        return $result;
    }
}