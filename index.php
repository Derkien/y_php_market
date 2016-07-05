<?php
use Db\DatabaseService;

require 'autoloader.php';

/** @var DatabaseService $dbService */
$dbService = DatabaseService::getInstance();

$dbCon = $dbService->getConnection();
// создаем табличку, если ее нет
$dbCon->query("
    CREATE TABLE IF NOT EXISTS `goods` (
      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(64) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8
");
// без фанатизма...
if (array_key_exists('more', $_GET) && 1 == $_GET['more']) {
    // кладем фрукты
    $dbCon->query("INSERT INTO `goods`(`name`) VALUES ('Яблоки'),('Яблоки'),('Груши'),('Яблоки'),('Апельсины'),('Груши')");
    header("Location: /");
    exit();
}
echo '<pre><a href="/?more=1">ещё фруктов</a></pre>';
echo '<pre style="max-height: 100px; overflow-y: auto">';
// это всё, что у нас есть
foreach ($dbCon->query("
    SELECT `id`, `name`
    FROM `goods` g
") as $item) {
    echo 'id: ', $item['id'], ' name: ', $item['name'], "\r\n";
}
echo '</pre>';

$queries = array();
//1
$queries[] = "SELECT g.`id` AS `one`, gg.`id` AS `two`
FROM `goods` g
INNER JOIN `goods` gg ON g.`name` = gg.`name` AND g.`id` < gg.`id`
";
//2
$queries[] = "SELECT g.`id` AS `one`, gg.`id` AS `two`
FROM `goods` g
INNER JOIN `goods` gg ON g.`name` = gg.`name`
WHERE g.`id` < gg.`id`
";
//3
$queries[] = "SELECT DISTINCT (CASE WHEN g.`id` > gg.`id` THEN g.`id` ELSE gg.`id` END) as `one`,
(CASE WHEN g.`id` < gg.`id` THEN g.`id` ELSE gg.`id` END) as `two`
FROM `goods` g
INNER JOIN `goods` gg ON g.`name` = gg.`name` AND g.`id` <> gg.`id`
";
//4
$queries[] = "SELECT (CASE WHEN g.`id` > gg.`id` THEN g.`id` ELSE gg.`id` END) as `one`,
(CASE WHEN g.`id` < gg.`id` THEN g.`id` ELSE gg.`id` END) as `two`
FROM `goods` g
INNER JOIN `goods` gg ON g.`name` = gg.`name` AND g.`id` <> gg.`id`
GROUP BY `one`, `two`
";
// запросы в цикле... ай-ай-ай... ))
foreach ($queries as $sql) {
    echo "<pre>Результат для \r\n" . $sql . "</pre>";
    $print = '';
    foreach ($dbCon->query($sql) as $item) {
        $print .= '(' . $item['one'] . ',' . $item['two'] . '), ';
    }
    echo rtrim($print, ', ');
}
