<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabelle</title>
</head>
<body>
    <?php 
        $currentYear = date("Y");
        // $currentM = date("m");
        $currentM = "January";
        $t=date('d-m-Y');
        $currentD = strtolower(date("d",strtotime($t)));
        error_log("t " . $t);
        error_log("year " . $currentYear);
        error_log("month " . $currentM);
        error_log("day " . $currentD);

        //month array from current month, not from January
        $monthsOfYear = array();
        for ($i = 0; $i < 12; $i++) {
            $timestamp = mktime(0, 0, 0, date('n') + $i, 1);
            array_push($monthsOfYear, date('F', $timestamp));
        }

        $monthsOfYearAsNum = array('zero');//Month list but as number
        foreach($monthsOfYear as $monthName) {
            $monthNum = date('m', strtotime($monthName));
            array_push($monthsOfYearAsNum, $monthNum);
        }
        array_unshift($monthsOfYear,'zero');

        $yearTable = "<table class='myTable'>";
        $spanOld = 0;
        $withoutStart = false;
               
        for($month = 1; $month<=12; $month++){
            //month dates
            $rightMonth = $monthsOfYearAsNum[$month];
            if($rightMonth==1 And $currentM != 1){
                $currentYear++;
            }
            $numberOfdaysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthsOfYearAsNum[$month], $currentYear);
            $yearTable .= "<tr><th class='month'>" . ($monthsOfYear[(int)$month] . "</th>");
            for($day=1; $day<=$numberOfdaysInMonth; $day++){
                $yearTable .= "<th class='mday'>" . $day . "</th>";
            }
            //CW
            $yearTable .= "</tr><tr><th class='title-col'>CW</th>";
            $percentTable = "";
            $fullWeeks = 0;
            $startDat = 0;
            $spanDone = false;
            $counter = 0;
            if($rightMonth == $currentM){
                for($day=1; $day<=$numberOfdaysInMonth; $day++){  
                    $rightMonth = $monthsOfYearAsNum[$month];
                    $weekName = date("l", mktime(0,0,0,$rightMonth,$day,$currentYear));
                    if($weekName != "Monday"){
                        $counter ++;
                    }else{
                        break;
                    }
                }
            }
            $spanOld = $spanOld +$counter;
            for($day=1; $day<=$numberOfdaysInMonth; $day++){  
                $rightMonth = $monthsOfYearAsNum[$month];
                $weekName = date("l", mktime(0,0,0,$rightMonth,$day,$currentYear));

                if($withoutStart == true And $day == 1 And $weekName == "Monday"){
                    $fullWeeks = floor($numberOfdaysInMonth / 7);
                    $withoutStart = false;
                    $spanDone = true;
                }
                if($weekName != "Monday" And $spanDone == false){
                    if ($spanOld != 0 And $counter == 0){
                        $spanDone = true;
                        $span = 7 - $spanOld;
                        $startDat = $span;
                        $yearTable .= "<th class='kw' colspan='$span'>" . (int)date("W", mktime(0,0,0,$rightMonth,$day,$currentYear)) ."</th>";
                        $percentTable .= "<th id='noData' colspan='$span' class='".(int)date("W", mktime(0,0,0,$rightMonth,$day,$currentYear))."'></th>";
                        $fullWeeks = floor(($numberOfdaysInMonth - $startDat) / 7);
                    }else{   
                        $spanDone = true;
                        $span = $spanOld;
                        $startDat = $span;
                        $yearTable .= "<th class='kw' colspan='$span'>" . (int)date("W", mktime(0,0,0,$rightMonth,$day,$currentYear)) ."</th>";
                        $percentTable .= "<th id='noData' colspan='$span' class='".(int)date("W", mktime(0,0,0,$rightMonth,$day,$currentYear))."'></th>";
                        $fullWeeks = floor(($numberOfdaysInMonth - $startDat) / 7);
                    }
                }else{
                    if($weekName=="Monday" && $fullWeeks != 0){   
                        $fullWeeks--;
                        $yearTable .= "<th class='kw' colspan= '7'>" . (int)date("W", mktime(0,0,0,$rightMonth,$day,$currentYear)) . "</th>";//hour, minute, second, month, day, year
                        $percentTable .= "<th id='noData' colspan='7' class='".(int)date("W", mktime(0,0,0,$rightMonth,$day,$currentYear))."'></th>";
                        $spanOld = $numberOfdaysInMonth - $day + 1;
                        if($spanOld == 7){
                            $withoutStart = true;
                        }
                    }elseif ($fullWeeks == 0 && $weekName=="Monday"){
                        $yearTable .= "<th class='kw' colspan= '". strval($numberOfdaysInMonth - $day + 1) . "'>" . (int)date("W", mktime(0,0,0,$rightMonth,$day,$currentYear)) ."</th>";
                        $percentTable .= "<th id='noData' colspan='". strval($numberOfdaysInMonth - $day + 1) . "' class='".(int)date("W", mktime(0,0,0,$rightMonth,$day,$currentYear))."'></th>";
                        $spanOld = $numberOfdaysInMonth - $day + 1;
                        break;
                    }
                }  
            }
            //procent_id
            $yearTable .= "</tr><tr><th class='title-col'>Procent</th>";
            $yearTable .= $percentTable;
        }
        $yearTable .= "</tr></table>";
        echo $yearTable; 
    ?>
    <script>
        xhttp = new XMLHttpRequest();
        xhttp.open('GET', 'weeks.php?empid=181');
        xhttp.onload = () => {
            let response = xhttp.response;
            let jsonObj = JSON.parse(response);
            jsonObj.forEach((date) =>{
                console.log(date.WorkWeek, date.PlannedPercent);
                jsonObj.forEach(cw => {
                    var divs = document.getElementsByClassName(cw.WorkWeek);
                    [].slice.call(divs).forEach(function (div) {
                        div.innerHTML = cw.PlannedPercent;
                        div.id = 'monday-'+ cw.PlannedPercent;
                    });
                });
            });
        }
		xhttp.send();
    </script>
    <style>
        div.year{background: #476e97; size: 300px; border: none; font-size: 20px; color: white; padding: 10px 30px; width: min-content; position: relative; left: 0px; margin: 2px;}
        div.username{background: green; size: 300px; border: none; font-size: 20px; color: white; padding: 5px 30px; width: min-content; position: relative; left: 0px; margin: 2px;}
        button.close{ background: #f44336; border: none; font-size: 20px; border-radius: 10px; color: white; padding: 10px 30px; cursor: pointer;}
        /*th, td {border: 1px solid black;}*/
        th, td {min-width: 25px;}
        /* table{table-layout: fixed;width: 100%;} */
        /*https://colorhunt.co/palette/180289*/
        th.noBorder {border: 0px solid black;}
        th{font-family: sans-serif; font-size: 17px; height: 15px; background: #AB6FE3;}
        th.withBorder{border: 2px solid black;}
        th.mday{background: #0090ee; color: wheat; text-align: center; max-width: 20px;}/*th.mday{background: darkgray;}*/
        th.wname{background: #00909e; color: whitesmoke; text-align: center;}/*th.wname{background: gray;}*/
        th.kw{background: lightblue; text-align: center;}
        th.month{background: #142850; color: wheat; padding: 5px;} /*th.month{background: #BF8888; padding: 0px;}*/
        th.title-col{background: #27496d; color: #dae1e7;}/*th.title-col{background: silver;}*/
        th#monday-10{background: darkred;}
        th#monday-10-2{background: #B40000;}
        th#monday-20{background: red;}
        th#monday-20-2{background: #FF6666;}
        th#monday-30{background: darkorange;}
        th#monday-30-2{background: #FFA83D;}
        th#monday-30{background: orange;}
        th#monday-30-2{background: #FFB429;}
        th#monday-40{background: #d1b610;}
        th#monday-40-2{background: #D1C784;}
        th#monday-50{background: #FFFFE0;}
        th#monday-50-2{background: #FFFFE6;}
        th#monday-60{background: #FFFFE0;}
        th#monday-60-2{background: #FFFFE6;}
        th#monday-70{background: greenyellow;}
        th#monday-70-2{background: #CBFF7D;}
        th#monday-80{background: greenyellow;}
        th#monday-80-2{background: #CBFF7D;}
        th#monday-90{background: greenyellow;}
        th#monday-90-2{background: #CBFF7D;}
        th#monday-100{background: green;}
        th#monday-100-2{background: #6A806A;}
        th#monday-120{background: #006400;}
        th#monday-120-2{background: #356654;}
        th#noData{background: blueviolet;}
        th#noData-2{background: #AB6FE3;}
        /*th.monday{background: #f44336; padding: 0px;}*/
    </style>
</body>
</html>