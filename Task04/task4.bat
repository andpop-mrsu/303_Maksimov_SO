#!/bin/bash
chcp 65001

sqlite3 movies_rating.db < db_init.sql

echo 1. Найти все пары пользователей, оценивших один и тот же фильм. Устранить дубликаты, проверить отсутствие пар с самим собой. Для каждой пары должны быть указаны имена пользователей и название фильма, который они ценили.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT (user1.name || ' , ' || user2.name) AS usernames, movies.title AS movie_title FROM ratings r1 JOIN ratings r2 ON r1.movie_id == r2.movie_id AND r1.id < r2.id JOIN users user1 ON user1.id == r1.user_id JOIN users user2 ON user2.id == r2.user_id JOIN movies ON movies.id == r1.movie_id;"
echo " "

echo 2. Найти 10 самых старых оценок от разных пользователей, вывести названия фильмов, имена пользователей, оценку, дату отзыва в формате ГГГГ-ММ-ДД.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT movies.title, users.name, rating, DATE(timestamp, 'unixepoch') AS date FROM ratings JOIN movies ON movies.id == ratings.movie_id JOIN users ON ratings.user_id == users.id GROUP BY users.name HAVING MIN(timestamp) ORDER BY timestamp LIMIT 10;"
echo " "

echo 3. Вывести в одном списке все фильмы с максимальным средним рейтингом и все фильмы с минимальным средним рейтингом. Общий список отсортировать по году выпуска и названию фильма. В зависимости от рейтинга в колонке 'Рекомендуем' для фильмов должно быть написано 'Да' или 'Нет'.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT title, year, avg_rating, CASE average.avg_rating WHEN max_or_min.max_avg_rating then 'Yes' ELSE 'No' END AS Recommend FROM (SELECT movies.title AS title, movies.year AS year, AVG(rating) AS avg_rating FROM ratings JOIN movies ON movies.id = movie_id GROUP BY movie_id) AS average JOIN (SELECT MAX(avg_rating) AS max_avg_rating, MIN(avg_rating) AS min_avg_rating FROM (SELECT avg(rating) AS avg_rating FROM ratings JOIN movies ON movies.id = movie_id GROUP BY movie_id)) AS max_or_min ON average.avg_rating = max_or_min.max_avg_rating OR average.avg_rating = max_or_min.min_avg_rating ORDER BY average.year, average.title;"
echo " "

echo 4. Вычислить количество оценок и среднюю оценку, которую дали фильмам пользователи-мужчины в период с 2011 по 2014 год.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT COUNT(*) AS number, AVG(rating) AS avg FROM ratings JOIN users ON users.id == user_id AND users.gender == 'male' AND DATETIME(timestamp, 'unixepoch') BETWEEN '2011-01-01 00:00:00' AND '2013-12-31 23:59:59';"
echo " "

echo 5. Составить список фильмов с указанием средней оценки и количества пользователей, которые их оценили. Полученный список отсортировать по году выпуска и названиям фильмов. В списке оставить первые 20 записей.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT title, year, AVG(ratings.rating) AS avg_rating, COUNT(DISTINCT ratings.user_id) AS number_of_ratings FROM movies JOIN ratings ON movies.id == ratings.movie_id GROUP BY movies.id ORDER BY year, title LIMIT 20"
echo " "

echo 6. Определить самый распространенный жанр фильма и количество фильмов в этом жанре. Отдельную таблицу для жанров не использовать, жанры нужно извлекать из таблицы movies.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT genre, max(movies_number) AS movies_number FROM (WITH divided_genres(genre, combined_genres) AS (SELECT NULL, genres FROM movies UNION ALL SELECT CASE WHEN INSTR(combined_genres, '|') == 0 THEN combined_genres ELSE SUBSTR(combined_genres, 1, INSTR(combined_genres, '|') - 1) END, CASE WHEN INSTR(combined_genres, '|') == 0 THEN NULL ELSE SUBSTR(combined_genres, INSTR(combined_genres, '|') + 1) END FROM divided_genres WHERE combined_genres IS NOT NULL) SELECT genre, COUNT(*) AS movies_number FROM divided_genres WHERE genre IS NOT NULL GROUP BY genre);"
echo " "

echo 7. Вывести список из 10 последних зарегистрированных пользователей в формате "Фамилия Имя|Дата регистрации" (сначала фамилия, потом имя).
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "SELECT substr(name, instr(name, ' ') + 1) || ' ' || substr(name, 1, instr(name, ' ')  - 1) AS name, register_date FROM users ORDER BY register_date DESC LIMIT 10;"
echo " "

echo 8. С помощью рекурсивного CTE определить, на какие дни недели приходился ваш день рождения в каждом году.
echo --------------------------------------------------
sqlite3 movies_rating.db -box -echo "WITH RECURSIVE birthday(date, day) AS (SELECT '2000-12-10', STRFTIME('%%w', '2000-12-10') UNION ALL SELECT DATE(date, '+1 year'), STRFTIME('%%w', DATE(date, '+1 year')) FROM birthday WHERE date < '2021-12-10') SELECT date, CASE day WHEN '0' THEN 'Sunday' WHEN '1' THEN 'Monday' WHEN '2' THEN 'Tuesday' WHEN '3' THEN 'Wednesday' WHEN '4' THEN 'Thursday' WHEN '5' THEN 'Friday' ELSE 'Saturday' END 'day_of_week' FROM birthday;"


pause