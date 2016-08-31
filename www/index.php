<?php
require_once('../config.php');
require_once('../data/pages.php');

$page = isset($_GET['page']) ? $_GET['page'] : key($pages); // Проверяю переменную page в запросе
$content = loadPage($page, $pages); // Вызываю функцию загрузки контента страницы
$content['menu'] = generateMenu($pages); // Генерация массива menu
$content['form'] = generateForm($pages); // Генерация массива form

echo render($page, $content); // Вывод контента

?>
