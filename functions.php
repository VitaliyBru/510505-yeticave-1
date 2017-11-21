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
        Первая не единица (может отсутствовать) следующая 0 или от 5 до 9 (для «часов» или «минут») */
        '/^(1\d)?([^1]?[05-9])?$/'
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
        $rus_case = ['Минуту', '$0 минуту', '$0 минуты', '$0 минут', 'меньше минуты' ];
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