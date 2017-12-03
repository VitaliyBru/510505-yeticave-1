/* Существующий список категорий */
INSERT INTO
          categories
VALUES
    (NULL, 'Доски и лыжи'),
    (NULL, 'Крепления'),
    (NULL, 'Ботинки'),
    (NULL, 'Одежда'),
    (NULL, 'Инструменты'),
    (NULL, 'Разное');

/* Существующий список пользователей */
INSERT INTO
          users
VALUES
    (NULL, NOW() - INTERVAL 3 DAY, 'ignat.v@gmail.com', 'Игнат', '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka', NULL, 'Тел:'),
    (NULL, NOW() - INTERVAL 2 DAY, 'kitty_93@li.ru', 'Леночка', '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa', NULL, 'Тел:'),
    (NULL, NOW() - INTERVAL 1 DAY, 'warrior07@mail.ru', 'Руслан', '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW', NULL, 'Тел:');

/* Список объявлений */
INSERT INTO
          lots
VALUES
    (NULL, NOW(), '2014 Rossignol District Snowboard', '--Здесь описание сделанное пользователем--', 'img/lot-1.jpg', 10999, CURDATE() + INTERVAL 1 DAY, 250, 1, NULL, 1),
    (NULL, NOW(), 'DC Ply Mens 2016/2017 Snowboard', 'Легкий маневренный сноуборд, готовый дать жару в любом парке, растопив
                    снег мощным щелчкоми четкими дугами. Стекловолокно Bi-Ax, уложенное в двух направлениях, наделяет этот снаряд
                    отличной гибкостью и отзывчивостью, а симметричная геометрия в сочетании с классическим прогибом кэмбер
                    позволит уверенно держать высокие скорости. А если к концу катального дня сил совсем не останется, просто
                    посмотрите на Вашу доску и улыбнитесь, крутая графика от Шона Кливера еще никого не оставляла равнодушным.', 'img/lot-2.jpg', 159999, CURDATE() + INTERVAL 1 DAY, 1000, 2, NULL, 1),
    (NULL, NOW(), 'Крепления Union Contact Pro 2015 года размер L/XL', '--Здесь описание сделанное пользователем--', 'img/lot-3.jpg', 8000, CURDATE() + INTERVAL 1 DAY, 500, 3, NULL, 2),
    (NULL, NOW(), 'Ботинки для сноуборда DC Mutiny Charocal', '--Здесь описание сделанное пользователем--', 'img/lot-4.jpg', 10999, CURDATE() + INTERVAL 1 DAY, 400, 1, NULL, 3),
    (NULL, NOW(), 'Куртка для сноуборда DC Mutiny Charocal', '--Здесь описание сделанное пользователем--', 'img/lot-5.jpg', 7500, CURDATE() + INTERVAL 1 DAY, 500, 2, NULL, 4),
    (NULL, NOW(), 'Маска Oakley Canopy', '--Здесь описание сделанное пользователем--', 'img/lot-6.jpg', 5400, CURDATE() + INTERVAL 1 DAY, 100, 3, NULL, 6);

/* Добавьте пару ставок для любого объявления */
INSERT INTO
          bets
VALUES
    (NULL, NOW() - INTERVAL '08:17:05' HOUR_SECOND, 5400, 1, 6),
    (NULL, NOW() - INTERVAL '06:04:49' HOUR_SECOND, 5500, 2, 6);

/* получить список из всех категорий */
SELECT
  name
FROM
  categories;

/* получить самые новые, открытые лоты. Каждый лот должен включать название, стартовую цену, ссылку на изображение, цену, количество ставок, название категории */
SELECT
  name, price_origin, img_url, COALESCE(bet_price, price_origin) AS price, bet_count, categories.name
FROM (
  SELECT
    lots.id, name, price_origin, img_url, MAX(price) AS bet_price, COUNT(bets.lot_id) AS bet_count, categories.name
  FROM
    (lots LEFT JOIN categories ON lots.category_id = categories.id) LEFT JOIN bets ON bets.lot_id = lots.id
  WHERE date_end > CURDATE()
  GROUP BY lots.id
  ORDER BY lots.id DESC
) AS pre_table;

/* найти лот по его названию или описанию */
SELECT
  *
FROM
  lots
WHERE
  name LIKE '%something%'
OR
  description LIKE '%something%';

/* обновить название лота по его идентификатору */
UPDATE
  lots
SET
  name = 'something'
WHERE
  id=1;

/* получить список самых свежих ставок для лота по его идентификатору */
SELECT
  *
FROM
  bets
WHERE
  lot_id = 6
ORDER BY id DESC;