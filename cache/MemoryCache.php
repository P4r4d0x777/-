<?php
require('../libraryOfExchangeRates/Autoloader.php');
class MemoryCache
{
    private $tableName;
    private $pdo;

    public function __construct() 
    {
        $iniArray = parse_ini_file("../config.ini", false);
        $this->CreateDB($iniArray);
        $this->CreateTable($iniArray);
    }

    /**
     * Создание базы данных
     * 
     * @param array $__iniArray - массив настроек БД
     */
    private function CreateDB($__iniArray)
    {
        try {
            $pdo = new PDO("mysql:host={$__iniArray['serverName']}", $__iniArray["userName"], $__iniArray["password"]);

            $sql = "CREATE DATABASE IF NOT EXISTS {$__iniArray['dbName']}";

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
            $pdo->exec($sql);
        }
        catch (PDOException $e) {
            print "Database error: " . $e->getMessage();
        }
    }

    /**
     * Создание таблицы в БД
     * 
     * @param array $__iniArray - массив настроек БД
     */
    private function CreateTable($__iniArray) 
    {
        try {

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $pdo = new PDO("mysql:host={$__iniArray['serverName']};dbname={$__iniArray['dbName']}", $__iniArray["userName"], $__iniArray["password"], $options);

            $sql = "CREATE TABLE IF NOT EXISTS {$__iniArray['tableName']}(
                CurrencyDate DATE ,
                CurrencyId CHAR(10) ,
                CurrencyNumCode CHAR(3) ,
                CurrencyCharCode CHAR(3) ,
                CurrencyName CHAR(100) ,
                CurrencyValue FLOAT(7,4) ,
                CurrencyNominal INT(7)
                ) ENGINE = MEMORY";
            $pdo->exec($sql);

            $this->tableName = $__iniArray["tableName"];
            $this->pdo = $pdo;
        }
        catch(PDOException $e) {
                echo "Database error: " . $e->getMessage();
        }
        
    }

    /**
     * Проверка есть ли валюта с заданной данной в таблице БД
     * 
     * @param string $__valute - чаркод валюты
     * @param string $__date   - дата за которую надо смотреть
     * 
     * @return false - в случае, если валюты нет в БД
     * @return array - в случае, если валюта нашлась 
     */
    private function Check($__valute, &$__date)
    {
        $__date = explode("/", $__date);

        $__date = $__date[2].'-'.$__date[1].'-'.$__date[0];

        $stmt = $this->pdo->prepare("SELECT * FROM $this->tableName WHERE CurrencyCharCode=:__valute AND CurrencyDate=:__date");

        $stmt->execute(array('__valute' => $__valute, '__date' => $__date));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result == false) {
            return false;
        }
        else {
            return $result;
        }
    }

    /**
     * Получение валюты за заданный день
     * 
     * @param string $__valute - чаркод валюты
     * @param string $__date   - дата за которую надо смотреть
     * 
     * @return array 
     */
    public function GetOneDay($__valute, $__date)
    {
        $valExist = true;

        $ex_date = $__date;
        $obj = $this->Check($__valute, $ex_date);

        if ($obj == false) {
            $realDate = $this->SetOneDay($__valute, $__date, $valExist);

            if ($valExist == false) {
                return false;
            }

            $obj = $this->Check($__valute, $realDate);
        }
        return $obj;
    }

    /**
     * Получение массива из БД для заданного периода и заданной валюты
     * 
     * @param string $__valute - чаркод валюты
     * @param string $__dateFrom   - дата начала
     * @param string $__dateTo   - дата конца
     * 
     * @return array 
     */
    public function GetPeriod($__valute, $__dateFrom, $__dateTo)
    {
        $handler = new PeriodExchangeRate();
        $handler->setDateFrom($__dateFrom);
        $handler->setDateTo($__dateTo);
        $handler->setValute($__valute);
        $handler->sendRequest();

        $result = $handler->getResult();

        if ($result==null) {
            return null; 
        }
        else {
            $this->SetPeriod($result);
        }
        return $result;
    }
    
    /**
     * Установка значений в БД для периода
     * 
     * @param array $__obj - чаркод валюты
     */
    private function SetPeriod($__obj)
    {
        foreach ($__obj as $key => $val) {
            $existInDB = $this->Check($val['CurrencyCharCode'], $val['CurrencyDate']);
            if($existInDB == false) {
                $this->AddtoDB($val);
            }
        }
    }

    /**
     * Установка значения для заданной валюты в заданный день, возрващает дату реальной установки
     * 
     * @param string $__valute  - чаркод валюты
     * @param string $__datem   - дата установки
     * @param bool $__valExist  - существует или нет валюта?
     * 
     * @return string 
     */
    private function SetOneDay($__valute, $__date, &$__valExist)
    {
        $handler = new DailyExchangeRate();

        $handler->setDate($__date);

        $handler->setValutes($__valute);

        $handler->sendRequest();

        $res = $handler->getResult()[$__valute];
        if ($res == null) {
            $__valExist = false;
            return false;
        }
        $dateToDB  = explode("/", $res['CurrencyDate']);

        $res['CurrencyDate'] = $dateToDB[2].'-'.$dateToDB[1].'-'.$dateToDB[0];

        $this->AddToDB($res);
        
        $realDate = $handler->getRealDate();

        return $realDate;
    }
    
    
    /**
     * Добавление в БД
     * 
     * @param array $__obj - чаркод валюты
     */
    private function AddtoDB($__obj)
    {
        $stmt = $this->pdo->prepare("INSERT INTO $this->tableName VALUES(:CurrencyDate, :CurrencyId, :CurrencyNumCode, :CurrencyCharCode, :CurrencyName, :CurrencyValue, :CurrencyNominal)");

        $stmt->execute($__obj);
    }
}
?>