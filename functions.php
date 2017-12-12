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
 * @param mixed $user_data
 *
 * @return string
 */
function secure($user_data)
{
    $user_data = (string)$user_data;
    return htmlspecialchars($user_data);
}

/**
 * Возвращает время чч:мм до закрытия торгов по лоту
 *
 * @param int $date_end timestamp окончания действия лота
 *
 * @return string
 */
function lotTimeRemaining (int $date_end)
{
    // временная метка для настоящего времени
    $now = strtotime('now');

    $delta_time_in_minutes = floor(($date_end - $now) / 60);
    $hours = sprintf("%02d", floor($delta_time_in_minutes / 60));
    $minutes = sprintf("%02d", ($delta_time_in_minutes % 60));
    return $hours . ":" . $minutes;
}

/**
 * Возвращает css класс для категории по ее id
 *
 * @param int $category_id id категории
 *
 * @return string
 */
function getIndexPhpPromoListLiClass(int $category_id)
{
    $class_ending = ['boards', 'attachment', 'boots', 'clothing', 'tools', 'other'];
    return "promo__item promo__item--" . $class_ending[$category_id - 1];
}

/**
 * Возвращает false если это число и оно больше ноля
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
    return empty($value);
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
    $answer = !in_array($complex['id'], array_column($complex['categories'], 'id'));
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
 * @param string $directories папка в которую будет перемещен файл в формате «folder_name/»
 * где folder_name относительный путь к папке
 *
 * @return string
 */
function getImageFromForm(string $uploading_name, string $directories = 'img/')
{
    $img_url = '';
    if (isset($_FILES[$uploading_name]) && $_FILES[$uploading_name]['tmp_name']) {
        $file_name = $_FILES[$uploading_name]['tmp_name'];
        $mime = mime_content_type($file_name);
        $accepted_type = ['image/jpeg' => '.jpg', 'image/png' => '.png'];
        if (array_key_exists($mime, $accepted_type)) {
            $file_path = $directories . uniqid('img_', true);
            $file_path .= $accepted_type[$mime];
            if (move_uploaded_file($file_name, __DIR__ . "/$file_path")) {
                $img_url = $file_path;
            }
        }
    }
    return $img_url;
}

/**
 * Возвращает true когда тестируемая ставка соответствует требованиям
 *
 * @param string|int $bet_sent величена ставки
 * @param string|int $not_less ограничение по минимальной величине
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
 * @param mysqli $_link Идентификатор соединения
 * @param string $_email адрес пользователя
 *
 * @throws Exception
 * @return array
 */
function getUser(mysqli $_link, string $_email)
{
    /** @var string $query sql запрос на пролучение данных из бд */
    $query = "
    SELECT
      id, 
      name, 
      password, 
      avatar
    FROM
      users
    WHERE 
      email = ?";
    try {
        $user = getExecutedQueryResult($_link, $query, [$_email]);
    } catch (Exception $e) {
        throw new Exception($e->getMessage(), $e->getCode());
    }
    return isset($user[0]) ? $user[0] : [];
}

/**
 * Возвращает массив категорий полученый от субд или
 * пустой массив в случае не удачи.
 *
 * @param mysqli $_link Идентификатор соединения
 *
 * @throws Exception
 * @return array|null
 */
function getCategories(mysqli $_link)
{
    $query = 'SELECT * FROM categories';
    try {
        $categories = mysqli_query_fetch_all($_link, $query);
    } catch (Exception $e) {
        Throw new Exception($e->getMessage(), $e->getCode());
    }
    return $categories;
}

/**
 * Возвращает результат sql запроса
 *
 * @param mysqli $_link Идентификатор соединения
 * @param string $_query сторока sql команды
 *
 * @return array|null
 * @throws Exception
 */
function mysqli_query_fetch_all(mysqli $_link, string $_query)
{
    $result_obj = mysqli_query($_link, $_query);
    $result = [];
    if ($result_obj) {
        $result = mysqli_fetch_all($result_obj, MYSQLI_ASSOC);
    }
    if (mysqli_errno($_link)) {
        Throw new Exception(mysqli_error($_link), mysqli_errno($_link));
    }
    return $result;
}

/**
 * Выводит на экран текст описания возникшей ошибки
 *
 * @param Exception $_error массив с описанием ошибки
 */
function showErrors(Exception $_error)
{
    $main_content = templateEngine(
        'error',
        [
            'error' => [$_error->getCode(), $_error->getMessage()]
        ]
    );
    echo templateEngine(
        'layout',
        [
            'nav_panel' => '',
            'title' => 'Ошибка',
            'is_auth' => false,
            'user_name' => '',
            'user_avatar' => '',
            'main_content' => $main_content
        ]
    );
}

/**
 * Возвращает список активных лотов
 *
 * @param mysqli $_link Идентификатор соединения
 * @param int $offset Смещение в выдаче лотов для пагинации
 * @param int $limit Максимальое количество лотов на страницу для пагинации
 *
 * @return array|null
 * @throws Exception
 */
function getActiveLots(mysqli $_link, int $offset = 0, int $limit = 3)
{
    $query = "
SELECT 
  SQL_CALC_FOUND_ROWS
  lots.id AS id, 
  lots.name AS name, 
  description, 
  price_origin, 
  UNIX_TIMESTAMP(date_end) AS date_end, 
  categories.name AS category, 
  img_url
FROM 
  lots LEFT JOIN categories ON lots.category_id = categories.id 
WHERE 
  date_end > CURRENT_DATE
ORDER BY lots.id DESC
LIMIT ? OFFSET ?
";
    try {
        $lots = getExecutedQueryResult($_link, $query, [$limit, $offset]);
    } catch (Exception $e) {
        Throw new Exception($e->getMessage(), $e->getCode());
    }
    return $lots;
}

/**
 * Возвращает список лотов и ставок пользователя на них
 *
 * @param mysqli $_link Идентификатор соединения
 * @param int $_user_id Идентификатор пользователя
 *
 * @return array|null
 * @throws Exception
 */
function getLotsWithUsersBets(mysqli $_link, int $_user_id)
{
    $query = "
    SELECT
      my_bets.id,
      lots.id AS lot_id, 
      lots.name, 
      UNIX_TIMESTAMP(date_end) AS date_end, 
      price, 
      UNIX_TIMESTAMP(date) AS ts,
      img_url,
      categories.name AS category,
      (lots.winner_id <=> my_bets.user_id) AS winner,
      contact
    FROM 
      (SELECT 
        * 
      FROM 
        bets
      WHERE
        user_id = ?) AS my_bets
    LEFT JOIN lots ON my_bets.lot_id = lots.id 
    LEFT JOIN categories ON lots.category_id = categories.id
    LEFT JOIN users ON lots.author_id = users.id
    ORDER BY my_bets.id DESC
    ";
    try {
        $lots = getExecutedQueryResult($_link, $query, [$_user_id]);
    } catch (Exception $e) {
        Throw new Exception($e->getMessage(), $e->getCode());
    }
    return $lots;
}

/**
 * Возвращает данные лота по его id
 *
 * @param mysqli $_link Идентификатор соединения
 * @param int $lot_id идентификатор лота в бд
 *
 * @return array|null
 * @throws Exception
 */
function getOneLot(mysqli $_link, int $lot_id)
{
    $query = "
    SELECT
      lots.id AS id,
      lots.name AS name,
      categories.name AS category,
      description,
      img_url,
      price_origin,
      price_step,
      UNIX_TIMESTAMP(date_end) AS date_end,
      author_id
    FROM
      lots
    LEFT JOIN categories ON lots.category_id = categories.id
    WHERE 
      lots.id = ?
    ";
    try {
        $lot = getExecutedQueryResult($_link, $query, [$lot_id]);
    } catch (Exception $e) {
        Throw new Exception($e->getMessage(), $e->getCode());
    }
    return isset($lot[0]) ? $lot[0] : [];
}

/**
 * Возвращает массив ставок по выбранному лоту
 *
 * @param mysqli $_link Идентификатор соединения
 * @param int $lot_id Идентификатор лота
 *
 * @return array|null
 * @throws Exception
 */
function getBetsForLot(mysqli $_link, int $lot_id)
{
    $query = "
    SELECT
      bets.id,
      name,
      price,
      UNIX_TIMESTAMP(date) AS ts,
      user_id
    FROM
      bets
    LEFT JOIN users ON bets.user_id = users.id
    WHERE 
     lot_id = ?
    ORDER BY bets.id DESC 
     ";
    try {
        $bets = getExecutedQueryResult($_link, $query, [$lot_id]);
    } catch (Exception $e) {
        Throw new Exception($e->getMessage(), $e->getCode());
    }
    return $bets;
}

/**
 * Заносит данные в таблицу бд
 *
 * @param mysqli $_link Идентификатор соединения
 * @param array $column_data Ассоциативный массив с именами ячеек и данными для них
 * @param string $table_name Име таблици в которую проводится запись
 *
 * @return mixed
 * @throws Exception
 */
function setInTable(mysqli $_link, array $column_data, string $table_name)
{
    $query = "INSERT INTO $table_name(";
    $values = "VALUES (";
    $first = true;
    $data = [];
    foreach ($column_data as $column => $_data) {
        if($first) {
            $query .= $column;
            $values .= "?";
        } else {
            $query .= ",$column";
            $values .= ",?";
        }
        $data[] = $_data;
        $first = false;
    }
    $query .= ") $values)";
    try {
        $stmt = db_get_prepare_stmt($_link, $query, $data);
    } catch (Exception $e) {
        throw new Exception("Ошибка: " . $e->getMessage(), $e->getCode());
    }
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_errno($stmt)) {
        throw new Exception("Ошибка: " . mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
    }
    return mysqli_stmt_insert_id($stmt);
}

/**
 * Возвращает колличество строк найденных функцией getActiveLots()
 *
 * @param mysqli $_link Идентификатор соединения
 *
 * @return int
 * @throws Exception
 */
function getTotalNumberFoundRows(mysqli $_link)
{
    $query = "
    SELECT FOUND_ROWS()
    ";
    $result_obj = mysqli_query($_link, $query);
    $result[] = -1;
    if ($result_obj) {
        $result = mysqli_fetch_row($result_obj);
    }
    if (mysqli_errno($_link)) {
        throw new Exception(mysqli_error($_link), mysqli_errno($_link));
    }
    if ($result[0] === -1) {
        throw new Exception("Неизвестная ошибка: не удалось получить количество найденных записей");
    }
    return (int) $result[0];
}

/**
 * Возвращает список лотов по поисковому запросу в режиме FULLTEXT
 *
 * @param mysqli $_link Идентификатор соединения
 * @param int $offset Смещение в выдаче лотов для пагинации
 * @param int $limit Максимальое количество лотов на страницу для пагинации
 * @param string $search техт поискового запроса
 *
 * @return array
 * @throws Exception
 */
function getFoundLots(mysqli $_link, int $offset = 0, int $limit = 3, string $search)
{
    /** @var string $query sql запрос на пролучение данных из бд */
    $query = "
    SELECT SQL_CALC_FOUND_ROWS 
      lots.id AS id, 
      lots.name AS name,
      price_origin,
      img_url,
      UNIX_TIMESTAMP(date_end) AS date_end,
      categories.name AS category
    FROM
      lots LEFT JOIN categories ON lots.category_id = categories.id
    WHERE 
      date_end > CURRENT_DATE
      AND 
        (lots.name LIKE ? 
        OR 
        description LIKE ?)
    LIMIT ? OFFSET ?
";
    try {
        $lots =getExecutedQueryResult($_link, $query, [$search, $search, $limit, $offset]);
    } catch (Exception $e) {
        throw new Exception($e->getMessage(), $e->getCode());
    }
    return $lots;
}

/**
 * Возвращает двумерный массив результатов выполнения стороки sql запроса
 *
 * @param mysqli $_link Идентификатор соединения
 * @param string $query Строка состоящая из sql запроса
 * @param array $data_array Данные для подстановки в строку запроса
 *
 * @return array
 * @throws Exception
 */
function getExecutedQueryResult(mysqli $_link, string $query, $data_array = array())
{
    try {
        $stmt = db_get_prepare_stmt($_link, $query, $data_array);
    } catch (Exception $e) {
        throw new Exception($e->getMessage(), $e->getCode());
    }
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_errno($stmt)) {
        throw new Exception(mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
    }
    $anchor = array();
    $data = array();
    $meta = mysqli_stmt_result_metadata($stmt);
    while ($field = mysqli_fetch_field($meta)) {
        $anchor[] = &$data[$field->name];
    }
    $value = array_merge([$stmt], $anchor);
    call_user_func_array('mysqli_stmt_bind_result', $value);
    $result = array();
    $i = 0;
    while (mysqli_stmt_fetch($stmt)) {
        foreach ($data as $col => $datum ) {
            $result[$i][$col] = $datum;
        }
        $i++;
    }
    return $result;
}

/**
 * Возвращает список активных лотов в выбранной категории
 *
 * @param mysqli $_link Идентификатор соединения
 * @param int $offset Смещение в выдаче лотов для пагинации
 * @param int $limit Максимальое количество лотов на страницу для пагинации
 * @param int $category_id идентификационный номер категории
 *
 * @return array|null
 * @throws Exception
 */
function getActiveLotsFromCategory(mysqli $_link, int $offset = 0, int $limit = 3, int $category_id)
{
    $query = "
SELECT 
  SQL_CALC_FOUND_ROWS
  lots.id AS id, 
  lots.name AS name, 
  description, 
  price_origin, 
  UNIX_TIMESTAMP(date_end) AS date_end, 
  categories.name AS category, 
  img_url
FROM 
  lots LEFT JOIN categories ON lots.category_id = categories.id 
WHERE 
  date_end > CURRENT_DATE
  AND 
  category_id = ?
ORDER BY lots.id DESC
LIMIT ? OFFSET ?
";
    try {
        $lots = getExecutedQueryResult($_link, $query, [$category_id, $limit, $offset]);
    } catch (Exception $e) {
        Throw new Exception($e->getMessage(), $e->getCode());
    }
    return $lots;
}

/**
 * Возвращает наименование категории по ее id номеру
 *
 * @param int $category_id идентификационный номер категории
 * @param array $categories массив категорий
 *
 * @return null|string
 */
function getCategoryName(int $category_id, $categories = [])
{
    foreach ($categories as $row) {
        if ((int)$row['id'] === $category_id) {
            return $row['name'];
        }
    }
    return null;
}

/**
 * Возвращает список ставок на лоты по окончии торгов
 *
 * @param mysqli $_link Идентификатор соединения
 *
 * @return array|null
 * @throws Exception
 */
function getBetsOnClosedLotsWithoutWinner(mysqli $_link)
{
    $query = "
    SELECT
      lot_id,
      price,
      user_id
    FROM
      bets
    LEFT JOIN lots ON bets.lot_id = lots.id
    WHERE 
     winner_id IS NULL 
     AND 
     date_end <= CURDATE()
    ORDER BY lots.id, price DESC 
     ";
    try {
        $bets = mysqli_query_fetch_all($_link, $query);
    } catch (Exception $e) {
        Throw new Exception($e->getMessage(), $e->getCode());
    }
    return $bets;
}

/**
 * Возвращает список выигравших ставок
 *
 * @param array $_bets список ставок
 *
 * @return array
 */
function getListWinnerBets($_bets)
{
    $bet_winner = [];
    foreach ($_bets as $bet) {
        $lot_id = $bet['lot_id'];
        if (!isset($bet_winner[$lot_id])) {
            $bet_winner[$lot_id] = $bet;
        }
        if ($bet_winner[$lot_id]['price'] < $bet['price']) {
            $bet_winner[$lot_id] = $bet;
        }
    }
    return $bet_winner;
}

/**
 * Записывает в таблицу лота победившего пользователя
 *
 * @param mysqli $_link Идентификатор соединения
 * @param int $_user_id номер пользователя в бд
 * @param int $_lot_id номер лота в бд
 *
 * @return bool
 * @throws Exception
 */
function setWinnerId(mysqli $_link, int $_user_id, int $_lot_id)
{
    $query = "UPDATE `yeticave`.`lots` SET `winner_id` = $_user_id WHERE  `id` = ?";
    try {
        $stmt = db_get_prepare_stmt($_link, $query, [$_lot_id]);
    } catch (Exception $e) {
        throw new Exception("Ошибка: " . $e->getMessage(), $e->getCode());
    }
    $success = mysqli_stmt_execute($stmt);
    if (mysqli_stmt_errno($stmt)) {
        throw new Exception("Ошибка: " . mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
    }
    return $success;
}

/**
 * Возвращает данные о пользователе по его идентификационному номеру
 *
 * @param mysqli $_link Идентификатор соединения
 * @param int $_user_id номер пользователя в бд
 *
 * @return array
 * @throws Exception
 */
function getUserFromId(mysqli $_link, int $_user_id)
{
    /** @var string $query sql запрос на пролучение данных из бд */
    $query = "
    SELECT
      name, 
      email
    FROM
      users
    WHERE 
      id = ?";
    try {
        $stmt = db_get_prepare_stmt($_link, $query, [$_user_id]);
    } catch (Exception $e) {
        throw new Exception($e->getMessage(), $e->getCode());
    }
    mysqli_stmt_execute($stmt);
    if (mysqli_stmt_errno($stmt)) {
        throw new Exception(mysqli_stmt_error($stmt), mysqli_stmt_errno($stmt));
    }
    $user = ['name' => '', 'email' => ''];
    mysqli_stmt_bind_result($stmt, $user['name'], $user['email']);
    if (mysqli_stmt_fetch($stmt)) {
        return $user;
    }
    return [];
}

/**
 * Запускает сессию (если еще не запущена) и передает данные о пользователе
 * в массив $_SESSION['user']
 *
 * @param array $user массив с данными залогиневшегося пользователя
 */
function setSessionAndStart($user = array())
{
    $secure_key = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }
    $_SESSION['user'] = [
        'secure_key' => $secure_key,
        'id' => $user['id'],
        'name' => $user['name'],
        'avatar' => $user['avatar']
    ];
}

/**
 * Возвращает массив с данными о пользователе если email и пароль верны
 * если нет то пустой массив
 *
 * @param mysqli $_link Идентификатор соединения
 * @param array $_login Массив с данными из формы входа
 * @param array $errors Ссылка на массив с флагами ошибок
 *
 * @return array
 */
function getAuthenticUser(mysqli $_link, $_login, &$errors)
{
    $user = [];
    //проверяем корректность написания email адреса и его соответствие с пользователем
    if (isNotEmail($_login['email'])) {
        $errors['email']['isWrong'] = true;
    } else {
        $user = getUser($_link, $_login['email']);
        $errors['email']['isWrong'] = empty($user);
    }

    //если пользователь найден проверяем пароль
    if (!$errors['email']['isWrong']) {
        $errors['password']['isWrong'] = !password_verify($_login['password'], $user['password']);
    }

    if ($errors['password']['isWrong']) {
        $user = [];
    }

    return $user;
}