<?php
require_once('../cache/MemoryCache.php');
readfile('day.html');

$errors = [];

if (isset($_POST['submitButton'])) {

    if (!isset($_POST['valutesArr'])) {
        $errors['valutesArrError'] = "Choose one or more valute: <br>1.Click on button Choose Valutes <br>2.Choose valutes";
    }

    if (!isset($_POST['datepicker']) || $_POST['datepicker'] == "") {
        $errors['datepickerError'] = "Choose date: <br>1.Click on text mm/dd/yyyy <br> 2.Choose date";
    }

    if(isset($_POST['valutesArr']) && (isset($_POST['datepicker']) && $_POST['datepicker'] != ""))
    {
        $today = date('m/d/Y');  

        $todayDate = explode("/", $today);

        $givenDate = explode("/", $_POST['datepicker']);

        $date = ReverseDate($_POST['datepicker']);

        if (intval($givenDate[2]) < intval($todayDate[2])) {
            PrintOneDayBD($_POST['valutesArr'], $date, $errors);
        }
        else if (intval($givenDate[2]) == intval($todayDate[2])) {
            if (intval($givenDate[0]) < intval($todayDate[0])) {
                PrintOneDayBD($_POST['valutesArr'], $date, $errors);
            }
            else if (intval($givenDate[0]) == intval($todayDate[0])) {
                if (intval($givenDate[1]) <= intval($todayDate[1])) {
                    PrintOneDayBD($_POST['valutesArr'], $date, $errors);
                }
                else {
                    $errors['datepickerError'] = "Wrong date <br>1.Select a date before today(including)";
                }
            }
        }
    }
    SendErrorsOrAccess($errors);
}

/**
 * Реверсирует дату
 * 
 * @param string $__date - дата
 * @param string $separator - разделитель
 * 
 * @return string преобразованная дата
 */
function ReverseDate($__date, $__separator="/")
{
    $date    = explode($__separator, $__date);

    $newDate = $date[1].'/'.$date[0].'/'.$date[2];

    return $newDate;
}

/**
 * Вывод таблицы БазыДанных
 * 
 * @param array $__valArr - массива валют, заданных пользователем
 * @param string $__date - выбранная дата
 * @param array $__errors - массив ошибок
 */
function PrintOneDayBD($__valArr, $__date, &$__errors){
    $html = '<!DOCTYPE html>
    <html>
        <head>
            <style>
            .table{
            margin-top: -205px;
            position:absolute;
            margin-left:700px;
            width: 1200px;
            }
            </style>
        </head>

        <body>
        <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>ID</th>
                <th>NumCode</th>
                <th>CharCode</th>
                <th>Name</th>
                <th>Value</th>
            </tr>
        </thead>
      <tbody>';

    $size = count($__valArr);

    $db = new MemoryCache();
    
    $entry = true;

    for($index = 0; $index < $size; $index++) {
        $html.="<tr> <th scope = 'row'> $index </th>";

        $sizeToDeleteIfNotExist = strlen("<tr> <th scope = 'row'> $index </th>");

        $obj = $db->GetOneDay($__valArr[$index], $__date);
        if ($obj==false) {
            $__errors['ValuteDontExistAtThisDate'] .= "the API of the CBRF dosen`t contain <br> the rate of this currency = $__valArr[$index](#$index) for the given day = $__date <br>";
            
            $html = substr($html, 0, -$sizeToDeleteIfNotExist);
        }
        else {
            if($entry == true) {
                $date    = explode('-', $obj['CurrencyDate']);

                $realDate = $date[2].'/'.$date[1].'/'.$date[0];

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
                    <p>As a result, the rate update date may not coincide <br> with the one you choose (on weekends, monday, <br> for example, rates are not updated) script choose last update date<br><br>
                    Your date:           $__date;<br>
                    Lastest update date: $realDate.
                    </p>
                    </body> </html>";
                    
                print $attention;

                $entry = false;
            }
            $sizeOfRow = count($obj);

            $nominalsArr = [];

            $counter = 0;

            foreach($obj as $key => $value){
                if ($key == 'CurrencyNominal') {
                    $nominalsArr[$counter] = $value;
                    $counter++;
                }
            }

            $counter = 0;

            foreach($obj as $key => $value){
                    if ($key == 'CurrencyNominal') {
                        continue;
                    }
                    if ($key == 'CurrencyValue'){
                        $value = $value/$nominalsArr[$counter];
                        $counter++;
                    }
                    $html.="<td>$value</td>";
                }
            $html .= "</tr>";
        }
    }
    $html .= "</tbody> </table> </body> </html>";
    print $html;
}

/**
 * Отправка пользователю Сообщения об успехе или об наличии ошибок
 * 
 * @param array $__errors - массив ошибок
 */
function SendErrorsOrAccess($__errors){
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