<?php
/**
 * @param string $template_name
 * @param array $data
 *
 * @return string
 */
function templateEngine(string $template_name, $data = array())
{
    $path_file = 'templates/' . $template_name . '.php';
    $out = '';
    if (file_exists($path_file)) {
        if (!empty($data)) {
            extract($data);
        }
        ob_start();
        require_once($path_file);
        $out = ob_get_clean();
    }
    return $out;
}

/**
 * Возвращает время прошедшее с момента $_ts (time stamp совершения ставки) до настоящего
 * в формате «ч часов (или м минут) назад» или «дд.мм.гг в чч:мм»
 *
 * @param int $_ts принимает time stamp
 *
 * @return string
 */
function betTime($_ts)
{
    /** @var string $result итоговый результат */
    $result = '';
    /** @var array $pattern паттерны для применеия склонений */
    $pattern = [
        // Равно единице (для «Час» или «Минуту»)
        '/^1$/',
        // Первая не единица, а следующая единица (для «час» или «минуту»)
        '/^[^1]1$/',
        // Первая не единица (может отсутствовать) следующая от 2 до 4 (для «часа» или «минуты»)
        '/^[^1]?[2-4]$/',
        /* Первая единица вторая любая десятичная цифра или
        Первая не единица следующая [0 или от 5 до 9] или
        От 5 до 9 (для «часов» или «минут») */
        '/^(1\d)?([^1][05-9])?([5-9])?$/'
    ];
    $dt = strtotime('now') - $_ts;
    if ($dt >= 86400) {
        $result = date('d.m.y в H:i', $_ts);
    } elseif ($dt >= 3600) {
        $result = (string)floor($dt / 3600);
        /** @var array $rus_case содержит склонение слова «час» */
        $rus_case = ['Час', '$0 час', '$0 часа', '$0 часов'];
        $result = preg_replace($pattern, $rus_case, $result) . ' назад';
    } else {
        $result = (string)floor($dt / 60);
        /** @var array $rus_case содержит склонение слова «минута» */
        $rus_case = ['Минуту', '$0 минуту', '$0 минуты', '$0 минут', 'Меньше минуты' ];
        // Добавляем условие если «0» то «меньше минуты»
        $pattern[] = '/^0/';
        $result = preg_replace($pattern, $rus_case, $result) . ' назад';
    }
    return $result;
}

/**
 * Защита от XSS атак
 *
 * @param string $user_data
 *
 * @return string
 */
function secure(string $user_data)
{
    return htmlspecialchars($user_data);
}

function lotTimeRemaining ()
{
    // временная метка для полночи следующего дня
    $tomorrow = strtotime('tomorrow midnight');

// временная метка для настоящего времени
    $now = strtotime('now');

// далее нужно вычислить оставшееся время до начала следующих суток и записать его в переменную $lot_time_remaining
    $delta_time_in_minutes = floor(($tomorrow - $now) / 60);
    $hours = sprintf("%02d", floor($delta_time_in_minutes / 60));
    $minutes = sprintf("%02d", ($delta_time_in_minutes % 60));
    return $hours . ":" . $minutes;
}


/**
 * Возвращает false если число больше ноля
 *
 * @param mixed $value число или строка с числом
 *
 * @return bool
 */
function isNotPositiveNumber($value)
{
    return $value != (int)$value or $value <= 0;
}

/**
 * Возвращает true если строка пустая
 *
 * @param string $value экзаминуемая строка
 *
 * @return bool
 */
function isEmpty(string $value)
{
    return $value == '';
}

/**
 * Возвращает true если строка не соответствует формату email адреса
 *
 * @param string $value тестируемая строка
 *
 * @return bool
 */
function isNotEmail(string $value)
{
    if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
        return false;
    } else {
        return true;
    }
}

/**
 * Возвращает true если значение не содержится в массиве
 *
 * @param array $complex принимает массив с искомым значением и массивом в котором будет происходить поиск
 *
 * @return bool
 */
function isNotCategory($complex = [])
{
    $answer = true;
    foreach ($complex['categories'] as $value){
        if ($complex['value'] == $value['name']) {
            $answer = false;
            break;
        }
    }
    return $answer;
}

/**
 * Возвращает false если дата в требуемом формате и еще не наступила
 *
 * @param string $value дата в формате «дд.мм.гггг»
 *
 * @return bool
 */
function isNotFutureDate(string $value)
{
    $answer = true;
    if ($date = DateTime::createFromFormat('d.m.Y', $value)) {
        $answer = ($date->format('d.m.Y') != $value or $date->getTimestamp() < strtotime('now'));
    }
    return $answer;
}

/**
 * Проверяет переданный методом POST файл на соответствие MIME типу, перемещает в указанную директорию и
 * возвращает путь к файлу если операция прошла успешно, или пустую строку в случае неудачи.
 *
 * @param string $uploading_name значение «name» в теге <input>
 * @param string $directories папка в которую будет перемещен файл в формате «/folder_name/»
 * где folder_name относительный путь к папке
 *
 * @return string
 */
function getImageFromForm(string $uploading_name, string $directories = '/img/')
{
    $img_url = '';
    if (isset($_FILES[$uploading_name]) && $_FILES[$uploading_name]['tmp_name']) {
        $file_name = $_FILES[$uploading_name]['tmp_name'];
        $mime = mime_content_type($file_name);
        $accepted_type = ['image/jpeg' => '.jpg', 'image/png' => '.png'];
        if (array_key_exists($mime, $accepted_type)) {
            $file_path = $directories . uniqid('img_', true);
            $file_path .= $accepted_type[$mime];
            if (move_uploaded_file($file_name, __DIR__ . $file_path)) {
                $img_url = $file_path;
            }
        }
    }
    return $img_url;
}

/**
 * Возвращает true когда тестируемая ставка соответствует требованиям
 *
 * @param $bet_sent величена ставки
 * @param $not_less ограничение по минимальной величине
 *
 * @return bool
 */
function isBetCorrect ($bet_sent, $not_less)
{
    if (!isNotPositiveNumber($bet_sent)) {
        if ($bet_sent >= $not_less) {
            return true;
        }
    }
    return false;
}

/**
 * Возвращает массив с текущей стоимостью лота и минимально допустимой величиной ставки
 *
 * @param array $_lot массив с данными по лоту
 * @param array $_bets массив с данныи о ставках
 *
 * @return mixed
 */
function getBetAmounts($_lot, $_bets)
{
    if (isset($_bets[0]['price'])) {
        $bet_amount['current'] = max(array_column($_bets, 'price'));
        $bet_amount['not_less'] = $bet_amount['current'] + $_lot['price_step'];
    } else {
        $bet_amount['current'] = $bet_amount['not_less'] = $_lot['price_origin'];
    }
    return $bet_amount;
}

/**
 * Возвращает мссив с данными пользователя из массива пользователей по его email
 * или пустой массив если пользователь не найден
 *
 * @param string $email адрес пользователя
 * @param array $users массив пользователей
 *
 * @return array
 */
function getUser($email, $users)
{
    foreach ($users as $user) {
        if ($user['email'] == $email) {
            return $user;
        }
    }
    return [];
}