<?php
require_once('../cache/MemoryCache.php');
readfile('period.html');

$errors = [];

if (isset($_POST['submitButton'])) {
    if(!isset($_POST['taskValute'])) {
        $errors['taskValuteError'] = "Choose one valute: <br>1.Click on button(2 from left) <br>2.Choose valute";
    }
    if (!isset($_POST['datepicker_to']) || $_POST['datepicker_to'] == "") {
        $errors['datepicker_toError'] = "Choose date TO: <br>1.Click on text mm/dd/yyyy <br> 2.Choose date";
    }
    if (!isset($_POST['datepicker_from']) || $_POST['datepicker_from'] == "") {
        $errors['datepicker_fromError'] = "Choose date FROM: <br>1.Click on text mm/dd/yyyy <br> 2.Choose date";
    }
    if(isset($_POST['taskValute']) && (isset($_POST['datepicker_to']) && $_POST['datepicker_to'] != "") && (isset($_POST['datepicker_from']) && $_POST['datepicker_from'] != "")) {
        //сравнение дат
        $dateFrom = explode("/", $_POST['datepicker_from']);
        $dateTo   = explode("/", $_POST['datepicker_to']);
        $today    =  explode("/", date('m/d/Y'));

        if(DateComparisons($dateFrom, $dateTo) && DateComparisons($dateTo,$today) && DateComparisons($dateFrom, $today)) {

            $dateTo = $dateTo[1].'/'.$dateTo[0].'/'.$dateTo[2];
            $dateFrom = $dateFrom[1].'/'.$dateFrom[0].'/'.$dateFrom[2];

            PrintPeriodBD($_POST['taskValute'], $dateFrom, $dateTo, $errors);
        }
        else {
            $errors['formatDatesPickers'] = "Check the date range: <br>Entered dates must be before today's date (including) and Date From must be before Date TO";
        }
    }
    
    SendErrorsOrAccess($errors);
}

/**
 * Вывод таблицы БазыДанных
 * 
 * @param array $__valArr - массива валют, заданных пользователем
 * @param string $__dateFrom - выбранная дата начала
 * @param string $__dateTo - выбранная дата конца
 * @param array $__errors - массив ошибок
 */
function PrintPeriodBD($__valute, $__dateFrom, $__dateTo, &$__errors)
{

    $html = "<!DOCTYPE html>
    <html>
        <head>
            <style>
            .table{
            margin-top: -150px;
            position:absolute;
            margin-left:1000px;
            width: 500px;
            }
            </style>
        </head>

        <body>
        <table class='table table-bordered'>
        <thead>
            <tr>
                <th colspan = '2'>Курс валюты \"$__valute\" с $__dateFrom по $__dateTo</th>
            </tr>
            <tr>
                <th>Date</th>
                <th>Value</th>
            </tr>
        </thead>
      <tbody>";

    $db = new MemoryCache();
    $res = $db->GetPeriod($__valute, $__dateFrom, $__dateTo);
    
    $attention = "<!DOCTYPE html>
    <html>
        <head>
            <style>
            p{
                margin-left: 40px;
                color: #00533a;
            }
            </style>
        </head>
        <body>
        <p>Caution!</p>
        <p> For some dates, the central bank does not set <br>quotes (for example, weekends and Mondays), so <br> some dates may be skipped <br>
        </p>
        </body> </html>";
        
    print $attention;

    if($res == null) {
        $__errors['formatDatesPickers'] = "On these dates, the central bank did not set quotes, select other dates please";
    }
    else {

        foreach ($res as $key => $temp) {
            
            $date =  $temp["CurrencyDate"];
            $value = $temp["CurrencyValue"];
            $nominal = $temp["CurrencyNominal"]; 

            $res = $value/$nominal;
            $html.="<tr> <td> $date </td> <td> $res </td> </tr>";
        }
        $html .= "</tbody> </table> </body> </html>";
        print $html;
    }
}

/**
 * Функция сравнения двух строк
 * 
 * @param string $__earlier - первая строка 
 * @param string $__later - вторая строка
 * 
 * @return bool true - 1-ая < 2-ой : false 1-ая > 2-ой
 */
function DateComparisons($__earlier, $__later)
{
    if (intval($__earlier[2]) < intval($__later[2])) {
        return true;
    }
    else if (intval($__earlier[2]) == intval($__later[2])) {
        if (intval($__earlier[0]) < intval($__later[0])) {
           return true;
        }
        else if (intval($__earlier[0]) == intval($__later[0])) {
            if (intval($__earlier[1]) <= intval($__later[1])) {
                return true;
            }
            else {
                return false;
            }
        }
    }
}

/**
 * Отправка пользователю Сообщения об успехе или об наличии ошибок
 * 
 * @param array $__errors - массив ошибок
 */
function SendErrorsOrAccess($__errors){
    /*.p{
        color: 0001ee;
        font-size: 22px;
        }*/
    if(count($__errors)!=0 ) {
        $html = '<!DOCTYPE html>
        <html>
            <head>
                <style>
                .Warning{
                color: #fe0900;
                font-size: 22px;
                }
                </style>
            </head>
            <body>
            <p class = "Warning">Warning</p>
            ';
        
        foreach ($__errors as $key => $value) {
            if($key!='')
            $html.="<p>Error:$key <br> Note: <br> $value</p>";
        }

        $html.=  "</body> </html>";

        print $html;
    }
    else {
        $html = '<!DOCTYPE html>
        <html>
            <head>
                <style>
                .suc{
                    position:absolute;
                    color: #0001ee;
                }
                </style>
            </head>
            <body>
            <p class = "suc">Successfuly</p>
            </body> </html>';

        print $html;
    }
}
?>