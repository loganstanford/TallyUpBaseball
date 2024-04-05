import json, re, requests
from pybaseball import *
from datetime import timedelta, datetime
import pandas as pd
from sqlalchemy import create_engine, text
from sqlalchemy.exc import IntegrityError
import pymysql

d = datetime.today() - timedelta(days=1)

# Create engine
engine = create_engine('mysql+pymysql://c0_baseball:3!cZ6QfrREkoT@192.168.1.80:3306/c0baseball')

# SportsRadar functions
key = 'dce2xmaf6x9z7cp25tz7msse'

def get_sportsradar_games_daily(date):
    day = date.strftime('%d')
    month = date.strftime('%m')
    year = date.year
    uri = f'http://api.sportradar.us/mlb/trial/v7/en/games/{year}/{month}/{day}/summary.json?api_key={key}'

    response = requests.get(uri)
    games_json = json.loads(response.content)['league']['games']

    return games_json

def get_batter_box(p):
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

def get_pitcher_box(p):
    s = p['statistics']['pitching']['overall']
    outs = s['ip_1']
    hits = s['onbase']['h']
    bbs = s['onbase']['bb']
    runs = s['runs']['earned']
    ks = s['outs']['ktotal']

    pitcher_box = {
        "game_id": [game_id],
        "game_date": [game_date],
        "player_sportsradar_id": p['id'],
        "pitch_outs": [outs],
        "pitch_hits": [hits],
        "pitch_walks": [bbs],
        "pitch_runs": [runs],
        "pitch_ks": [ks]
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
        bonus_points: 2

    # 1 run given up
    if runs == 1:
        bonus_points = 3
    
    # Shutout
    if so == 1 and cg == 0:
        bonus_points = 5

    # Compelte game shutout
    if cg == 1 and so == 1:
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
    curYear = datetime.date.today().year
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

def update_player_db(p, teamid):

    # Get player from databse if exist
    id = p['id']
    sql = f"SELECT * FROM players WHERE sportsradar_id = '{id}'"
    with engine.connect() as conn:
        result = conn.execute(text(sql))
        r = result.all()

    # Get player data
    #uri = f"https://api.sportradar.com/mlb/trial/v7/en/players/{id}/profile.json?api_key={key}"
    #prof = requests.get(uri)
    #pjson = json.loads(prof.content)['player']
    first = p['preferred_name'].replace("'", "''") if "'" in p['preferred_name'] else p['preferred_name']
    last = p['last_name'].replace("'", "''") if "'" in p['last_name'] else p['last_name']
    jersey = p['jersey_number']
    pos = p['primary_position']
    status = p['status']


    # Get team id from database
    curTeamsql = f"SELECT * FROM teams WHERE sportsradar_id = '{teamid}'"
    with engine.connect() as conn:
        result_team = conn.execute(text(curTeamsql))
        tr = result_team.all()
    
    # INSERT if player doesn't exist, UPDATE if player does exist
    if r is None:
        ids = update_bbrefid(first, last)
        if not ids == 'N/A':
            sql = f"INSERT INTO players (first_name, last_name, jersey_no, bats, throws, team_id, primary_position, status, bbref_id, mlbam_id, fangraphs_id, retro_id) VALUES ('{first}, '{last}', '{jersey}', {tr[0].id}, '{pos}', '{status}', '{ids['bbref_id']}', '{ids['mlbam_id']}', '{ids['fangraphs_id']}', '{ids['retro_id']}')"
        else:
            sql = f"INSERT INTO players (first_name, last_name, jersey_no, bats, throws, team_id, primary_position, status) VALUES ('{first}, '{last}', '{jersey}',  {tr[0].id}, '{pos}', '{status}')"
    
    else:
        sql = f"UPDATE players SET first_name = '{first}', last_name = '{last}', jersey_no = '{jersey}', team_id = {tr[0].id}, primary_position = '{pos}', status = '{status}' WHERE sportsradar_id = '{id}'"

    with engine.connect() as conn:
        conn.execute(text(sql))
        conn.commit()

games_json = get_sportsradar_games_daily(d)
for g in games_json:
    game = g['game']

    if game['status'] not in ['closed', 'maintenance']:
        continue

    game_id = game['id']
    game_date = (datetime.strptime(game['scheduled'], "%Y-%m-%dT%H:%M:%S%z") - timedelta(hours=5)).strftime("%Y-%m-%d %H:%M:%S")
    teams = ['home', 'away']

    for t in teams:
        isHome = 1 if t == 'home' else 0
        team_id = game[t]['id']
        players = game[t]['players']

        # Get team pitching stats
        team_list = get_team_pitching_box(game[t])
        team_df = pd.DataFrame(team_list, index=[0]) # <--------- START HERE FINISH ADDING GAME DATA TO PITCH BOX BEFORE COMMITTING
        try:
            team_df.to_sql('boxscores', con=engine, if_exists='append', index=False)
        except IntegrityError as ie:
            print(f"Duplicate record for {game_id} and {team_list['player_sportsradar_id']}")
        except Exception as e:
            print("SOME OTHER ERROR OCCURED")
        else:
            print(f"Inserted team pitching data for for {game_id} and {team_list['player_sportsradar_id']}")

        for p in players:

            # Update player data in database
            update_player_db(p, team_id)

            # If player has both hitting and pitching stats (aka Ohtani)
            if 'hitting' in p['statistics'] and 'pitching' in p['statistics']:
                player_box = get_pitcher_box(p)
                player_box.update(get_batter_box(p))

            # If player is pitcher, get pitching stats
            if 'pitching' in p['statistics'] and 'hitting' not in p['statistics']:
                player_box = get_pitcher_box(p)

            # If player is hitter, get hitter stats
            if 'hitting' in p['statistics'] and 'pitching' not in p['statistics']:
                player_box = get_batter_box(p)
            
            player_box['isHome'] = isHome

            # Convert dict to dataframe
            player_df = pd.DataFrame(player_box)
            try:
                player_df.to_sql('boxscores', con=engine, if_exists='append', index=False)
            except IntegrityError as ie:
                print(f"Duplicate record for {game_id} and {player_box['player_sportsradar_id']}")
            except Exception as e:
                print("SOME OTHER ERROR OCCURRED")
            else:
                print(f"Inserted data for {player_box['player_sportsradar_id']} for game {game_id}")


