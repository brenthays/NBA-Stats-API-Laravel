# NBA Stats API Laravel

API built to analyze NBA player stats built with [Laravel](https://laravel.com/docs).

## Seeding

Seeding teams is done using the [API-NBA API hosted on RapidAPI](https://rapidapi.com/api-sports/api/api-nba). Once you have a RapidAPI key, set your `RAPID_API_KEY` environment variable (see `.env.example`).

Seeding play-by-play data (possessions) is done using the exports offered by [BigDataBall](https://www.bigdataball.com/nba-historical-playbyplay-dataset/). Create a directory `storage/app/bigdataball` and place the .csv files for each game in the new folder. (Do not place the huge .csv file that includes all possessions)

After the above steps are taken, seed your database with:

`php artisan migrate:refresh --seed`

## API Documentation

The following endpoints can be accessed using a `GET` request

### Get Conferences

/api/conference

| Parameter     | Description   |
| ------------- | ------------- |
| id            | Filters results by id, comma separated (e,g: `1,2`)                           |
| orderBy       | Orders results by given attribute and direction (e,g. `id,asc`, `id,desc`)    |
| not           | Excludes results by id, comma separated                                       |

### Get Divisions

/api/division

| Parameter     | Description   |
| ------------- | ------------- |
| id            | Filters results by id, comma separated (e,g: `1,2`)                           |
| orderBy       | Orders results by given attribute and direction (e,g. `id,asc`, `id,desc`)    |
| not           | Excludes results by id, comma separated                                       |
| conference_id | Filters results by conference, comma separated                                |

### Get Games

/api/game

| Parameter     | Description   |
| ------------- | ------------- |
| id            | Filters results by id, comma separated (e,g: `1,2`)                           |
| orderBy       | Orders results by given attribute and direction (e,g. `id,asc`, `id,desc`)    |
| not           | Excludes results by id, comma separated                                       |
| season_id     | Filters results by season, comma separated                                    |
| team_id       | Filters results by team, comma separated                                      |
| date          | Filters results by date                                                       |

### Get Players

/api/player

| Parameter     | Description   |
| ------------- | ------------- |
| id            | Filters results by id, comma separated (e,g: `1,2`)                           |
| orderBy       | Orders results by given attribute and direction (e,g. `id,asc`, `id,desc`)    |
| not           | Excludes results by id, comma separated                                       |
| team_id       | Filters results by team, comma separated                                      |
| name          | Filters results by name                                                       |
| with_stats    | If set to 1, results will includ player stats for specified game/season       |
| game_id       | `game_id` or `season_id` parameters required with `with_stats` parameter      |
| season_id     | `game_id` or `season_id` parameters required with `with_stats` parameter      |

### Get Seasons

/api/season

| Parameter     | Description   |
| ------------- | ------------- |
| id            | Filters results by id, comma separated (e,g: `1,2`)                           |
| orderBy       | Orders results by given attribute and direction (e,g. `id,asc`, `id,desc`)    |
| not           | Excludes results by id, comma separated                                       |

### Get Teams

/api/team

| Parameter     | Description   |
| ------------- | ------------- |
| id            | Filters results by id, comma separated (e,g: `1,2`)                           |
| orderBy       | Orders results by given attribute and direction (e,g. `id,asc`, `id,desc`)    |
| not           | Excludes results by id, comma separated                                       |
| city          | Filters results by city                                                       |
| division_id   | Filters results by division                                                   |
| short_name    | Filters results by short name (e,g. `HOU`)                                    |
