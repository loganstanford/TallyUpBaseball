import json, re, requests, os
from config import DB_CONNECTION_STRING, API_KEY
from pybaseball import *
from datetime import timedelta, datetime
import pandas as pd
from sqlalchemy import create_engine, text
from sqlalchemy.exc import IntegrityError
import logging

d = datetime.today() - timedelta(days=6)
update_bbrefid_enabled = False

log_path = os.path.join(os.path.dirname(__file__), 'logs/boxscore_daily.log')

logging.basicConfig(filename=log_path, filemode='a', level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')
logging.debug("Script started for %s", d)

# Create engine
try:
    engine = create_engine(DB_CONNECTION_STRING)
except Exception as e:
    logging.critical("Failed to create engine")
    logging.critical(e)
else:
    logging.debug("Successfully created engine")



def get_sportsradar_games_daily(date):
    day = date.strftime('%d')
    month = date.strftime('%m')
    year = date.year
    uri = f'http://api.sportradar.us/mlb/trial/v7/en/games/{year}/{month}/{day}/summary.json?api_key={API_KEY}'
    logging.debug('GET from %s', uri)
    try:
        response = requests.get(uri)
        games_data = json.loads(response.content)
        if 'league' in games_data and 'games' in games_data['league']:
            games_json = games_data['league']['games']
        else:
            games_json = []  # No games found
            logging.debug("No games found for the specified date")
    except Exception as e:
        logging.critical(e)
        games_json = []  # Return an empty list if an error occurs
    return games_json

def get_batter_box(p, game_id, game_date):
    player_stats = p['statistics']['hitting']['overall']
    avg = float(player_stats['avg'])
    obp = player_stats['obp']
    slg = player_stats['slg']
    ops = player_stats['ops']
    babip = player_stats['babip']
    at_bats = player_stats['ab']
    rbi = player_stats['rbi']
    sb = player_stats['steal']['stolen']
    runs = player_stats['runs']['total']
    ob_stats = player_stats['onbase']
    hits = ob_stats['h']
    singles = ob_stats['s']
    doubles = ob_stats['d']
    triples = ob_stats['t']
    homeruns = ob_stats['hr']
    total_bases = ob_stats['tb']
    walks = ob_stats['bb'] + ob_stats['ibb']
    hbp = ob_stats['hbp']
    sf = player_stats['outs']['sacfly'] + player_stats['outs']['sachit']

    # Calculate total fantasy points
    total_points = rbi + runs + singles + (doubles*2) + (triples*3) + (homeruns*4) + walks

    player_box = {
        "game_id": [game_id],
        "game_date": [game_date],
        "player_sportsradar_id": p['id'],
        "at_bats": [at_bats],
        "rbi": [rbi],
        "hits": [hits],
        "sb": [sb],
        "runs": [runs],
        "singles": [singles],
        "doubles": [doubles],
        "triples": [triples],
        "hr": [homeruns],
        "tb": [total_bases],
        "bb": [walks],
        "avg": [avg],
        "obp": [obp],
        "slg": [slg],
        "ops": [ops],
        "babip": [babip],
        "hbp": [hbp],
        "sf": [sf],
        "total_points": [total_points]
    }
    return player_box

def get_pitcher_box(p, game_id, game_date):
    s = p['statistics']['pitching']['overall']
    outs = s['ip_1']
    hits = s['onbase']['h']
    bbs = s['onbase']['bb']
    runs = s['runs']['earned']
    ks = s['outs']['ktotal']

    pitcher_box = {
        "game_id": game_id,
        "game_date": game_date,
        "player_sportsradar_id": p['id'],
        "pitch_outs": outs,
        "pitch_hits": hits,
        "pitch_walks": bbs,
        "pitch_runs": runs,
        "pitch_ks": ks
    }

    return pitcher_box

def get_team_pitching_box(pitch_team):

    s = pitch_team['statistics']['pitching']['overall']
    
    # Calculate pitching points
    """
    3 runs given up 1 bonus point
    2 runs given up 2 bonus points
    1 run given up 3 bonus points
    SHUTOUT-0 runs given up 5 bonus points
    Complete Game Shutout 8 bonus points
    Complete Game No Hitter 10 bonus points
    """
    cg = s['games']['complete']
    so = s['games']['shutout']
    hits = s['onbase']['h']
    runs = s['runs']['total']
    bonus_points = 0

    # 3 runs given up
    if runs == 3:
        bonus_points = 1

    # 2 runs given up
    if runs == 2:
        bonus_points = 2

    # 1 run given up
    if runs == 1:
        bonus_points = 3
    
    # Shutout
    if runs == 0 and cg == 0:
        bonus_points = 5

    # Compelte game shutout
    if cg == 1 and runs == 0:
        bonus_points = 8

    # Complete game no hitter
    if cg == 1 and hits == 0:
        bonus_points = 10

    total_points = 10 - runs + bonus_points

    team_pitch_dict = {
        "game_id": game_id,
        "game_date": game_date,
        "player_sportsradar_id": pitch_team['id'],
        "pitch_outs": s['ip_1'],
        "pitch_hits": hits,
        "pitch_walks": s['onbase']['bb'],
        "pitch_runs": runs,
        "pitch_ks": s['outs']['ktotal'],
        "total_points": total_points
    }

    team_pitch_dict['isHome'] = 1 if t == 'home' else 0
    
    
    return team_pitch_dict 

def update_bbrefid(fname, lname):
    if update_bbrefid_enabled:
        curYear = datetime.now().year
        plu = playerid_lookup(lname, fname.replace('.', '. ').strip(), fuzzy=True).query("mlb_played_last != ''").query("mlb_played_last >= @curYear - 1")
        if not plu.empty:
            newids = {
                "bbref_id": plu.iloc[0].key_bbref,
                "mlbam_id": plu.iloc[0].key_mlbam,
                "retro_id": plu.iloc[0].key_retro,
                "fangraphs_id": plu.iloc[0].key_fangraphs
            }
            return newids
        else:
            return 'N/A'
    else:
        return 'N/A'

def update_player_db(p, teamid, conn):
    try:
        # Player details
        player_id = p['id']
        first = p['preferred_name'].replace("'", "''")
        last = p['last_name'].replace("'", "''")
        jersey = p.get('jersey_number', '')
        pos = p.get('primary_position', '')
        status = p.get('status', '')

        # Check if player exists in database
        player_check_sql = text("SELECT * FROM players WHERE sportsradar_id = :player_id")
        result = conn.execute(player_check_sql, {'player_id': player_id})
        player_exists = result.fetchone() is not None

        # Get team id from database
        team_check_sql = text("SELECT * FROM teams WHERE sportsradar_id = :teamid")
        result_team = conn.execute(team_check_sql, {'teamid': teamid})
        team_record = result_team.fetchone()
        team_db_id = team_record.id if team_record else None

        # Construct SQL based on whether player exists
        if not player_exists:
            ids = update_bbrefid(first, last)
            if ids != 'N/A':
                insert_sql = text("""
                    INSERT INTO players 
                    (first_name, last_name, jersey_no, bats, throws, team_id, primary_position, status, bbref_id, mlbam_id, fangraphs_id, retro_id, sportsradar_id) 
                    VALUES (:first, :last, :jersey, :bats, :throws, :team_id, :pos, :status, :bbref_id, :mlbam_id, :fangraphs_id, :retro_id, :player_id)
                """)
                params = {
                    'first': first, 'last': last, 'jersey': jersey, 'bats': '', 'throws': '',
                    'team_id': team_db_id, 'pos': pos, 'status': status, 'bbref_id': ids['bbref_id'],
                    'mlbam_id': ids['mlbam_id'], 'fangraphs_id': ids['fangraphs_id'], 'retro_id': ids['retro_id'], 'player_id': player_id
                }
            else:
                insert_sql = text("""
                    INSERT INTO players 
                    (first_name, last_name, jersey_no, bats, throws, team_id, primary_position, status, sportsradar_id) 
                    VALUES (:first, :last, :jersey, :bats, :throws, :team_id, :pos, :status, :player_id)
                """)
                params = {'first': first, 'last': last, 'jersey': jersey, 'bats': '', 'throws': '', 'team_id': team_db_id, 'pos': pos, 'status': status, 'player_id': player_id}
            conn.execute(insert_sql, params)
        else:
            update_sql = text("""
                UPDATE players 
                SET first_name = :first, last_name = :last, jersey_no = :jersey, team_id = :team_id, primary_position = :pos, status = :status 
                WHERE sportsradar_id = :player_id
            """)
            params = {'first': first, 'last': last, 'jersey': jersey, 'team_id': team_db_id, 'pos': pos, 'status': status, 'player_id': player_id}
            conn.execute(update_sql, params)

        # Commit transaction
        conn.commit()
        logging.debug("Successfully updated player database for %s", player_id)
    except Exception as e:
        logging.critical("Error updating player database: %s", e)

def process_lineups(game, t, game_id, team_id, conn):
    try:
        lineup = {
            "game_id": game_id,
            "team_id": team_id,
            "l1": game[t]['lineup'][1]['id'] if 1 in game[t]['lineup'] else '',
            "l2": game[t]['lineup'][2]['id'] if 2 in game[t]['lineup'] else '',
            "l3": game[t]['lineup'][3]['id'] if 3 in game[t]['lineup'] else '',
            "l4": game[t]['lineup'][4]['id'] if 4 in game[t]['lineup'] else '',
            "l5": game[t]['lineup'][5]['id'] if 5 in game[t]['lineup'] else '',
            "l6": game[t]['lineup'][6]['id'] if 6 in game[t]['lineup'] else '',
            "l7": game[t]['lineup'][7]['id'] if 7 in game[t]['lineup'] else '',
            "l8": game[t]['lineup'][8]['id'] if 8 in game[t]['lineup'] else '',
            "l9": game[t]['lineup'][9]['id'] if 9 in game[t]['lineup'] else ''
        }
        if 'probable_pitcher' in game[t]:
            lineup["probable_starter"] = game[t]['probable_pitcher']['id']
            if 'era' in game[t]['probable_pitcher']:
                lineup["probable_starter_era"] = game[t]['probable_pitcher']['era']
        if 'starting_pitcher' in game[t]:
            lineup["starter"] = game[t]['starting_pitcher']['id']

        logging.debug("Successfully created lineup dict: %s", lineup)

        # Deleting existing record if it exists
        delete_sql = text("DELETE FROM mlb_lineups WHERE game_id = :game_id AND team_id = :team_id")
        conn.execute(delete_sql, {'game_id': game_id, 'team_id': team_id})
        conn.commit()

        # Inserting new lineup record
        lineup_df = pd.DataFrame(lineup, index=[0])
        lineup_df.to_sql('mlb_lineups', con=conn, if_exists='append', index=False)
        logging.debug("Successfully inserted lineup dataframe to database for game %s, team %s", game_id, team_id)

    except IntegrityError as ie:
        logging.warning("Record already exists for game %s, team %s: %s", game_id, team_id, ie)
    except Exception as e:
        logging.critical("Error processing lineup for game %s, team %s: %s", game_id, team_id, e)

def process_weather_data(game, game_id, conn):
    try:
        fc = game['weather']['forecast']
        fc_dict = {
            "game_id": game_id,
            "temp_f": fc.get('temp_f', ''),
            "humidity": fc.get('humidity', ''),
            "description": fc.get('condition', ''),
            "wind_speed": fc.get('wind', {}).get('speed_mph', ''),
            "wind_direction": fc.get('wind', {}).get('direction', '')
        }
        logging.debug("Successfully created forecast dict: %s", fc_dict)

        # Delete existing weather data for the game
        weather_delete_sql = text("DELETE FROM forecasts WHERE game_id = :game_id")
        conn.execute(weather_delete_sql, {'game_id': game_id})
        conn.commit()
        logging.debug("Successfully deleted existing weather data for game_id: %s", game_id)

        # Insert new weather data
        weather_df = pd.DataFrame([fc_dict])
        weather_df.to_sql('forecasts', con=conn, if_exists='append', index=False)
        logging.debug("Successfully inserted forecast dataframe for game_id: %s", game_id)

    except IntegrityError as ie:
        logging.warning("Record already exists for game_id: %s, %s", game_id, ie)
    except Exception as e:
        logging.critical("Error processing weather data for game_id: %s, %s", game_id, e)

def process_probable_pitcher(game, t, game_id, team_id, conn):
    try:
        pitch_dict = {
            "game_id": game_id,
            "team_id": team_id,
            "probable_starter": game[t]['probable_pitcher'].get('id', '')
        }
        if 'era' in game[t]['probable_pitcher']:
            pitch_dict["probable_starter_era"] = game[t]['probable_pitcher']['era']

        logging.debug("Successfully created probable pitcher dict: %s", pitch_dict)

        # Deleting existing probable pitcher data
        delete_sql = text("DELETE FROM mlb_lineups WHERE game_id = :game_id AND team_id = :team_id")
        conn.execute(delete_sql, {'game_id': game_id, 'team_id': team_id})
        conn.commit()
        logging.debug("Successfully deleted existing probable pitcher data for game %s, team %s", game_id, team_id)

        # Inserting new probable pitcher data
        pitcher_df = pd.DataFrame([pitch_dict])
        pitcher_df.to_sql('mlb_lineups', con=conn, if_exists='append', index=False)
        logging.debug("Successfully inserted probable pitcher dataframe for game %s, team %s", game_id, team_id)

    except IntegrityError as ie:
        logging.warning("Record already exists for game %s, team %s: %s", game_id, team_id, ie)
    except Exception as e:
        logging.critical("Error processing probable pitcher for game %s, team %s: %s", game_id, team_id, e)

def process_team_pitching_stats(game, t, game_id, conn):
    try:
        team_list = get_team_pitching_box(game[t])
        team_del_sql = text("DELETE FROM boxscores WHERE game_id = :game_id AND player_sportsradar_id = :player_id")
        conn.execute(team_del_sql, {'game_id': game_id, 'player_id': team_list['player_sportsradar_id']})
        conn.commit()
        logging.debug("Successfully executed delete for team %s in game %s", t, game_id)

        team_df = pd.DataFrame([team_list])
        team_df.to_sql('boxscores', con=conn, if_exists='append', index=False)
        logging.debug("Successfully inserted team pitching dataframe for game %s", game_id)

    except IntegrityError as ie:
        logging.critical(f"Duplicate record for game {game_id} and player {team_list['player_sportsradar_id']}: {ie}")
    except Exception as e:
        logging.critical(f"Error processing team pitching stats for game {game_id}: {e}")

def process_player_data(game, t, game_id, team_id, conn, isHome):
    game_date = (datetime.strptime(game['scheduled'], "%Y-%m-%dT%H:%M:%S%z") - timedelta(hours=5)).strftime("%Y-%m-%d %H:%M:%S")

    if 'players' in game[t]:
        players = game[t]['players']
        for p in players:
            try:
                # Update player data in database
                update_player_db(p, team_id, conn)

                # Determine player stats based on available data
                if 'hitting' in p['statistics'] and 'pitching' in p['statistics']:
                    player_box = get_pitcher_box(p, game_id, game_date)
                    player_box.update(get_batter_box(p, game_id, game_date))
                elif 'pitching' in p['statistics']:
                    player_box = get_pitcher_box(p, game_id, game_date)
                elif 'hitting' in p['statistics']:
                    player_box = get_batter_box(p, game_id, game_date)
                else:
                    continue  # Skip if no relevant stats

                player_box['isHome'] = isHome
                logging.debug("Successfully created player_box dict: %s", player_box)

                # Process player_box data
                process_player_box_data(player_box, game_id, conn)

            except Exception as e:
                logging.warning("Error processing player %s: %s", p.get('id', 'unknown'), e)

def process_player_box_data(player_box, game_id, conn):
    player_delete_sql = text("DELETE FROM boxscores WHERE game_id = :game_id AND player_sportsradar_id = :player_id")
    player_params = {'game_id': game_id, 'player_id': player_box['player_sportsradar_id']}
    
    try:
        conn.execute(player_delete_sql, player_params)
        conn.commit()
        logging.debug("Successfully executed delete for player %s in game %s", player_box['player_sportsradar_id'], game_id)

        # Insert player_box data
        player_df = pd.DataFrame([player_box])
        player_df.to_sql('boxscores', con=conn, if_exists='append', index=False)
        logging.debug("Successfully inserted player_box dataframe for player %s", player_box['player_sportsradar_id'])

    except IntegrityError as ie:
        logging.critical(f"Duplicate record for game {game_id} and player {player_box['player_sportsradar_id']}: {ie}")
    except Exception as e:
        logging.critical(f"Error inserting player_box data for game {game_id}, player {player_box['player_sportsradar_id']}: {e}")

games_json = get_sportsradar_games_daily(d)
for g in games_json:
    game = g['game']
    game_id = game['id']
    game_date = (datetime.strptime(game['scheduled'], "%Y-%m-%dT%H:%M:%S%z") - timedelta(hours=5)).strftime("%Y-%m-%d %H:%M:%S")
    teams = ['home', 'away']

    with engine.connect() as conn:
        # Delete existing game record
        try:
            game_sql = text("DELETE FROM mlb_schedule WHERE srid = :game_id")
            conn.execute(game_sql, {'game_id': game_id})
            conn.commit()
            logging.debug("Successfully deleted game record for %s", game_id)
        except Exception as e:
            logging.critical("Error deleting game record for %s: %s", game_id, e)
        
        # Insert to mlb_schedule
        game_dict = {
            "srid": game['id'],
            "status": game['status'],
            "home_team": game['home_team'],
            "away_team": game['away_team'],
            "venue": game['venue']['id'],
            "day_night": game['day_night'],
            "date": (datetime.strptime(game['scheduled'], "%Y-%m-%dT%H:%M:%S%z") - timedelta(hours=4)).strftime("%Y-%m-%d %H:%M:%S")
        }
        game_df = pd.DataFrame(game_dict, index=[0])
        try:
            game_df.to_sql('mlb_schedule', con=conn, if_exists='append', index=False)
            logging.debug("Successfully inserted game record for %s", game_id)
        except Exception as e:
            logging.critical("Error inserting game record for %s: %s", game_id, e)

        if 'weather' in game:
            process_weather_data(game, game_id, conn)

        for t in teams:
            isHome = 1 if t == 'home' else 0
            team_id = game[t]['id']

            # Process probable pitcher
            if 'probable_pitcher' in game[t]:
                process_probable_pitcher(game, t, game_id, team_id, conn)

            # Submit lineups and starters
            if 'lineup' in game[t]:
                process_lineups(game, t, game_id, team_id, conn)

            # Get team pitching stats
            if 'statistics' in game[t] and 'pitching' in game[t]['statistics']:
                process_team_pitching_stats(game, t, game_id, conn)

            # Process player data
            if 'players' in game[t]:
                process_player_data(game, t, game_id, team_id, conn, isHome)
