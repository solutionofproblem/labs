<?php
echo "<table border='1' style='border-collapse: collapse'>";
echo "<tr><th>&times;</th>";

// Заголовок таблицы
for ($i = 0; $i <= 10; $i++) {
    echo "<th>$i</th>";
}
echo "</tr>";

// Заполнение таблицы
for ($i = 0; $i <= 10; $i++) {
    echo "<tr>";
    echo "<th>$i</th>";
    for ($j = 0; $j <= 10; $j++) {
        echo "<td>" . ($i * $j) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";
?>  