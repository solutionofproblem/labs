<!DOCTYPE html>
<html>
<head>
    <title>Календарь</title>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; margin: 20px auto; }
        td, th { padding: 5px 10px; text-align: center; }
        th { background-color: #f0f0f0; }
        form { text-align: center; margin: 20px; }
    </style>
</head>
<body>
<?php
function showCalendar($month = null, $year = null) {
    // Если параметры не заданы, используем текущую дату
    if ($month === null) $month = date('n');
    if ($year === null) $year = date('Y');
    
    // Праздничные дни (можно дополнить)
    $holidays = array(
        '01-01', // Новый год
        '02-23', // День защитника отечества
        '03-08', // Международный женский день
        '05-01', // Праздник весны и труда
        '05-09', // День победы
        '06-12', // День России
        '11-04'  // День народного единства
    );
    
    $firstDay = mktime(0, 0, 0, $month, 1, $year);
    $daysInMonth = date('t', $firstDay);
    $dayOfWeek = date('w', $firstDay);
    $monthName = date('F Y', $firstDay);
    
    echo "<h2>$monthName</h2>";
    echo "<table border='1' style='border-collapse: collapse'>";
    echo "<tr>
            <th>Пн</th>
            <th>Вт</th>
            <th>Ср</th>
            <th>Чт</th>
            <th>Пт</th>
            <th style='color: red'>Сб</th>
            <th style='color: red'>Вс</th>
          </tr>";
    
    // Корректировка для воскресенья (0 -> 7)
    if ($dayOfWeek == 0) $dayOfWeek = 7;
    
    echo "<tr>";
    // Пустые ячейки до первого дня месяца
    for ($i = 1; $i < $dayOfWeek; $i++) {
        echo "<td></td>";
    }
    
    // Заполнение календаря
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $currentDayOfWeek = ($dayOfWeek + $day - 1) % 7;
        if ($currentDayOfWeek == 0) $currentDayOfWeek = 7;
        
        $date = sprintf("%02d-%02d", $month, $day);
        $style = "";
        
        // Выходные дни
        if ($currentDayOfWeek >= 6) {
            $style = "color: red;";
        }
        
        // Праздничные дни
        if (in_array($date, $holidays)) {
            $style = "background-color: #ffcccb; color: red;";
        }
        
        echo "<td style='$style'>$day</td>";
        
        if ($currentDayOfWeek == 7) {
            echo "</tr>";
            if ($day != $daysInMonth) {
                echo "<tr>";
            }
        }
    }
    
    echo "</table>";
}
?>

<form method="GET">
    <select name="month">
        <?php
        for ($m = 1; $m <= 12; $m++) {
            $selected = (isset($_GET['month']) && $_GET['month'] == $m) ? 'selected' : '';
            echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 1)) . "</option>";
        }
        ?>
    </select>
    <select name="year">
        <?php
        $currentYear = date('Y');
        for ($y = $currentYear - 5; $y <= $currentYear + 5; $y++) {
            $selected = (isset($_GET['year']) && $_GET['year'] == $y) ? 'selected' : '';
            echo "<option value='$y' $selected>$y</option>";
        }
        ?>
    </select>
    <input type="submit" value="Показать">
</form>

<?php
$month = isset($_GET['month']) ? (int)$_GET['month'] : null;
$year = isset($_GET['year']) ? (int)$_GET['year'] : null;
showCalendar($month, $year);
?>

</body>
</html> 