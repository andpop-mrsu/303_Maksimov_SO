<?php

$pdo = new PDO('sqlite:students.db');

$r = iconv('CP866', 'utf-8',chr(197));
$r2 = iconv('CP866', 'utf-8',chr(196));
$r3 = iconv('CP866', 'utf-8',chr(179));
$g1 = iconv('CP866', 'utf-8',chr(218));
$g2 = iconv('CP866', 'utf-8',chr(194));
$g3 = iconv('CP866', 'utf-8',chr(191));
$lg = iconv('CP866', 'utf-8',chr(195));
$rg = iconv('CP866', 'utf-8',chr(180));
$gb1 = iconv('CP866', 'utf-8',chr(192));
$gb2 = iconv('CP866', 'utf-8',chr(193));
$gb3 = iconv('CP866', 'utf-8',chr(217));
$p = iconv('CP866', 'utf-8',chr(32));

$split_str = "\n".$lg.str_repeat($r2, 7).$r.str_repeat($r2, 39).$r.str_repeat($r2, 15).$r.str_repeat($r2, 15).$r.str_repeat($r2, 15).$r.str_repeat($r2, 8).$r.str_repeat($r2, 15).$r.str_repeat($r2, 9).$rg."\n";
$top_split = "\n".$g1.str_repeat($r2, 7).$g2.str_repeat($r2, 39).$g2.str_repeat($r2, 15).$g2.str_repeat($r2, 15).$g2.str_repeat($r2, 15).$g2.str_repeat($r2, 8).$g2.str_repeat($r2, 15).$g2.str_repeat($r2, 9).$g3."\n";
$bottom_split = "\n".$gb1.str_repeat($r2, 7).$gb2.str_repeat($r2, 39).$gb2.str_repeat($r2, 15).$gb2.str_repeat($r2, 15).$gb2.str_repeat($r2, 15).$gb2.str_repeat($r2, 8).$gb2.str_repeat($r2, 15).$gb2.str_repeat($r2, 9).$gb3."\n";

$v1 = sprintf(" %' -6s", "group");
$v2 = sprintf(" %' -36s\t", "specialty");
$v3 = sprintf(" %' -10s\t", "surname");
$v4 = sprintf(" %' -13s\t", "name");
$v5 = sprintf(" %' -10s\t", "patronymic");
$v6 = sprintf(" %' -7s", "gender");
$v7 = sprintf(" %' -14s", "birthday");
$v8 = sprintf(" %' -8s", "id");
$top_table = $r3.$v1.$r3.$v2.$r3.$v3.$r3.$v4.$r3.$v5.$r3.$v6.$r3.$v7.$r3.$v8.$r3;

echo "Группы, имеющиеся в базе:\n";
$query = "select distinct group_on_course from groups join admission on admission.student_id = groups.student_id where cast(round((julianday('now') - julianday(admission.date))/360 + 1) as integer) < 5;";
$statement = $pdo->query($query);
$rows = $statement->fetchAll();
foreach ($rows as $row) {echo $row['group_on_course'] . "  "; }
echo "\n";

$groups = $rows;
$statement->closeCursor();

$number = "";
while($number != "e"){
    echo "\nВведите номер нужной группы или нажмите 'Enter' для вывода информации по всем группам (для выхода введите 'e'): ";
    $number = readline("\n");

    $check = 0;
    foreach ($groups as $row) {
        if ($row['group_on_course'] == $number) {
            $check = 1; 
        }
    }
    if ($check == 0 and $number == "") {
        $check = 2;
    }
    if ($check == 0 and $number == "e") {
        $check = 3;
    }

    if ($check == 2) {
        $find_students =
            "select groups.group_on_course, specialty.title, students.surname, students.name, students.patronymic, students.gender, students.birthday, students.id as student_card  " .
            "from groups join students on groups.student_id = students.id join specialty on  groups.specialty_id = specialty.id " .
            "join admission on admission.student_id = students.id where cast(round((julianday('now') - julianday(admission.date))/360 + 1) as integer) < 5 " . 
            "order by groups.group_on_course, students.surname;";

            $statement = $pdo->prepare($find_students);
            $statement->execute();
            $rows = $statement->fetchAll();
    
            echo $top_split;
            echo $top_table;
            echo $split_str;
    
            $counter = 0;
            foreach($rows as $row){
                $value1 = sprintf(" %' -4d\t", $row['group_on_course']);
                $value2 = sprintf(" %' -56s\t", $row['title']);
                $value3 = sprintf(" %' -15s\t", $row['surname']);
                $value4 = sprintf(" %' -13s\t", $row['name']);
                $value5 = sprintf(" %' -20s\t", $row['patronymic']);
                $value6 = sprintf(" %' -7s", $row['gender']);
                $value7 = sprintf(" %' -14s", $row['birthday']);
                $value8 = sprintf(" %' -8d", $row['student_card']);
                echo $r3,$value1,$r3, $value2,$r3, $value3,$r3, $value4,$r3, $value5,$r3, $value6,$r3, $value7,$r3,$value8, $r3;
    
                if(++$counter==count($rows)) {echo $bottom_split;}
                else {echo $split_str;}
            }
    }
    else if ($check == 1) {
        $find_students =
            "select groups.group_on_course, specialty.title, students.surname, students.name, students.patronymic, students.gender, students.birthday, students.id as student_card  " .
            "from groups join students on groups.student_id = students.id join specialty on  groups.specialty_id = specialty.id " .
            "join admission on admission.student_id = students.id where cast(round((julianday('now') - julianday(admission.date))/360 + 1) as integer) < 5 and groups.group_on_course = {$number} " . 
            "order by groups.group_on_course, students.surname;";
        
        $statement = $pdo->prepare($find_students);
        $statement->execute();
        $rows = $statement->fetchAll();

        echo $top_split;
        echo $top_table;
        echo $split_str;

        $counter = 0;
        foreach($rows as $row){
            $value1 = sprintf(" %' -4d\t", $row['group_on_course']);
            $value2 = sprintf(" %' -56s\t", $row['title']);
            $value3 = sprintf(" %' -15s\t", $row['surname']);
            $value4 = sprintf(" %' -13s\t", $row['name']);
            $value5 = sprintf(" %' -20s\t", $row['patronymic']);
            $value6 = sprintf(" %' -7s", $row['gender']);
            $value7 = sprintf(" %' -14s", $row['birthday']);
            $value8 = sprintf(" %' -8d", $row['student_card']);
            echo $r3,$value1,$r3, $value2,$r3, $value3,$r3, $value4,$r3, $value5,$r3, $value6,$r3, $value7,$r3,$value8, $r3;

            if(++$counter==count($rows)) {echo $bottom_split;}
            else {echo $split_str;}
        }
    }
    else if ($check == 3) {
        echo "ВЫХОД!\n";
    }
    else {
        echo "Номер группы введен не корректно!\n";
    }
}
?>