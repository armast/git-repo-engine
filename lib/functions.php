<?php
$cachedTemplates = [];
//Константы ошибок
define('ERROR_NOT_FOUND', 1);
define('ERROR_TEMPLATE_EMPTY', 2);

/*
* Обрабатывает указанный шаблон, подставляя нужные переменные
 *В функции добавлено кэширование
*/

function render($file, $variables = [], $useCache = true)
{
    $file = TPL_DIR . '/' . $file . '.html';
    if (!is_file($file)) {
      	echo 'Template file "' . $file . '" not found';
      	exit(ERROR_NOT_FOUND);
    }

    if (filesize($file) === 0) {
      	echo 'Template file "' . $file . '" is empty';
      	exit(ERROR_TEMPLATE_EMPTY);
    }
    global $cachedTemplates;
    $templateKey = md5($file);
    if (isset($cachedTemplates[$templateKey]) && $useCache) {
      $templateContent = $cachedTemplates[$templateKey];
    } else {
      $templateContent = file_get_contents($file);
      $cachedTemplates[$templateKey] = $templateContent;
    }

    // если переменных для подстановки не указано, просто
    // возвращает шаблон как есть
  	foreach ($variables as $key => $value) {
        if ($value != null) {
          // собираем ключи
          $key = '{{' . strtoupper($key) . '}}';

      		// заменяем ключи на значения в теле шаблона
      		$templateContent = str_replace($key, $value, $templateContent);
        }
    }
    return $templateContent;
}

/*
 * Загружаем контент страницы
 */

function loadPage($index, &$pages)
{
  $page = isset($pages[$index]) ? $pages[$index] : false;
  isValid($page); // Проверка валидности страницы
  if (isset($page['isCustom']) && $page['isCustom']) {
    return loadCustomPage($index, $pages); // Отображаем страницу с генерируемым контентом
  } else {
      // Показываем обычную страницу
      return [
          'title' => $page['title'],
          'content' => $page['description']
      ];
  }
}

/*Функция генерация меню */

function generateMenu($pages)
{
    $menu ='';
    foreach ($pages as $url => $data) {
        if ($url == "index") {
            $menu .= render('menu', ['url' => $url, 'title' => $data['title']]);
        } else {
            $menu .= render('menu_else', ['url' => $url, 'title' => $data['title']]);
        }
    }
    return $menu;
}

/*Проверка валидности страницы*/
function isValid($page)
{
  // Если массив пуст или передан не массив - ошибка
  if (!is_array($page) || empty($page)) {
    error404();
  }
}

/*
 * Загружаем страницу из функции
 */

function loadCustomPage($index, &$pages)
{
  $page_function = 'page_' . $index;

  // Если такая функция существует
  if (function_exists($page_function)) {
    // Возвращаем результат её работы
    return $page_function($pages[$index]);
  }else {
      // Иначе ошибка
      error404();
  }
}

/*
 * Отображаем HTTP-код ошибки
 */

function error404()
{
  header('HTTP/1.0 404 Not Found');
  echo 'Страница не найдена';
  exit;
}

/*
* Функция генерации и добавления формы в шаблон 
*/

function generateForm($data)
{
    session_start();
    $content = '';
    if (isset($_POST["send"])) {
        $name = htmlspecialchars($_POST['name']);
        $_SESSION['name'] = $name;

        $content .= render('form', ['value' => '<?='. $_SESSION['name'].'?>']);
    } else {
        $content .= render('form', ['value' => '']);
    }
    return $content;
}

/*
* Функция отображения главной страницы 
*/

function page_index($data)
{
    return [
        'title' => $data['title'],
        'content' =>''
        ];
}



?>
