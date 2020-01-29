# NBA Analysis

Web application built to analyze NBA stats built with [Laravel](https://laravel.com/docs).

## Seeding

Seeding teams is done using the [API-NBA API hosted on RapidAPI](https://rapidapi.com/api-sports/api/api-nba). Once you have a RapidAPI key, set your `RAPID_API_KEY` environment variable (see `.env.example`).

Seeding play-by-play data (possessions) is done using the exports offered by [BigDataBall](https://www.bigdataball.com/nba-historical-playbyplay-dataset/). Create a directory `storage/app/bigdataball` and place the .csv files for each game in the new folder. (Do not place the huge .csv file that includes all possessions)

After the above steps are taken, seed your database with:

`php artisan migrate:refresh --seed`
