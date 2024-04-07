import json, re, requests, os
from pybaseball import *
from datetime import timedelta, datetime
import pandas as pd
from sqlalchemy import create_engine, text, Table, MetaData, select, delete, or_
from sqlalchemy.exc import IntegrityError
from config import DB_CONNECTION_STRING, API_KEY
import logging

d = datetime.today()
year = d.year


# Create engine
try:
    engine = create_engine(DB_CONNECTION_STRING)
except Exception as e:
    logging.critical("Failed to create engine")
    logging.critical(e)
else:
    logging.debug("Successfully created engine")

# Get teams from database
sql = "SELECT sportsradar_id FROM teams"

with engine.connect() as conn:
    result = conn.execute(text(sql))
    teams = result.all()

# Create directory for json files
directory = f"{year}-{d.month}-{d.day}"
path = os.path.join(os.path.dirname(__file__), directory)
os.mkdir(path)

# For each team in database
for team in teams:
    teamid = team[0]
    # Get season splits for team from SportRadar
    url = f'http://api.sportradar.us/mlb/trial/v7/en/seasons/{year}/REG/teams/{teamid}/splits.json?api_key={API_KEY}'
    try:
        response = requests.get(url)
        team_json = json.loads(response.content)
    except Exception as e:
        logging.critical(e)
        raise Exception(e)

    # Save json to file for archive
    archive_file_path = f"{path}/{year}-{d.month}-{d.day}_{teamid}_splits.json"
    with open(archive_file_path, "w") as outfile:
        outfile.write(response.content.decode())

    # For each player on team
    for player in team_json['players']:

        # Skip pitchers
        if 'hitting' not in player['splits']:
            continue

        splits = player['splits']['hitting']['overall'][0]
        

        # PITCHER HAND SPLITS
        if 'pitcher_hand' in splits:
            for hand in splits['pitcher_hand']:
                split = {
                        "srid": player['id'],
                        "year": year,
                        "ab": hand['ab'],
                        "hits": hand['h'],
                        "runs": hand['runs'],
                        "s": hand['s'],
                        "d": hand['d'],
                        "t": hand['t'],
                        "hr": hand['hr'],
                        "rbi": hand['rbi'],
                        "bb": hand['bb'],
                        "avg": float(hand['avg']),
                        "obp": hand['obp'],
                        "slg": hand['slg'],
                        "ops": hand['ops']
                    }
                
                if hand['value'] == 'R':
                    split.update({"split_type": 1})
                    
                elif hand['value'] == 'L':
                    split.update({"split_type": 2})

            
                # Delete existing split in database
                try:
                    sql = f"DELETE FROM player_splits WHERE srid = '{split['srid']}' AND year = {year} AND split_type = {split['split_type']}"
                    with engine.connect() as conn:
                        conn.execute(text(sql))
                        conn.commit()
                except Exception as e:
                    print(f"An error occurred: {e}")
                else:
                    print(f"Deleted record for {split['srid']}")

                # Convert split dictionary to pandas dataframe
                split_df = pd.DataFrame(split, index=[0])

                # Update database with dataframe
                try:
                    split_df.to_sql('player_splits', con=engine, if_exists='append', index=False)
                except Exception as e:
                    print(f"An error occurred: {e}")
                    raise Exception(e)
                else:
                    print(f"Inserted split for {split['srid']} and split_type {split['split_type']}")
            

        # VENUE SPLITS
        if 'venue' in splits:
            for venue in splits['venue']:
                split = {
                    "srid": player['id'],
                    "year": year,
                    "split_type": 5,
                    "venue_id": venue['id'],
                    "ab": venue['ab'],
                    "hits": venue['h'],
                    "runs": venue['runs'],
                    "s": venue['s'],
                    "d": venue['d'],
                    "t": venue['t'],
                    "hr": venue['hr'],
                    "rbi": venue['rbi'],
                    "bb": venue['bb'],
                    "avg": float(venue['avg']),
                    "obp": venue['obp'],
                    "slg": venue['slg'],
                    "ops": venue['ops']
                }

                # Delete existing split in database
                try:
                    sql = f"DELETE FROM player_splits WHERE srid = '{split['srid']}' AND year = {year} AND split_type = {split['split_type']} AND venue_id = '{split['venue_id']}'"
                    with engine.connect() as conn:
                        conn.execute(text(sql))
                        conn.commit()
                except Exception as e:
                    print(f"An error occurred: {e}")
                    raise Exception(e)
                else:
                    print(f"Deleted record for {split['srid']}")

                # Convert split dictionary to pandas dataframe
                split_df = pd.DataFrame(split, index=[0])

                # Update database with dataframe
                try:
                    split_df.to_sql('player_splits', con=engine, if_exists='append', index=False)
                except Exception as e:
                    raise Exception(e)
                else:
                    print(f"Inserted split for {split['srid']} and split_type {split['split_type']}")
            

        # HOME/AWAY SPLITS
        if 'home_away' in splits:
            for home_away in splits['home_away']:
                split = {
                    "srid": player['id'],
                    "year": year,
                    "ab": home_away['ab'],
                    "hits": home_away['h'],
                    "runs": home_away['runs'],
                    "s": home_away['s'],
                    "d": home_away['d'],
                    "t": home_away['t'],
                    "hr": home_away['hr'],
                    "rbi": home_away['rbi'],
                    "bb": home_away['bb'],
                    "avg": float(home_away['avg']),
                    "obp": home_away['obp'],
                    "slg": home_away['slg'],
                    "ops": home_away['ops']
                }
                if home_away['value'] == 'home':
                    split.update({"split_type": 3})
                elif home_away['value'] == 'away':
                    split.update({"split_type": 4})

                # Delete existing split in database
                try:
                    sql = f"DELETE FROM player_splits WHERE srid = '{split['srid']}' AND year = {year} AND split_type = {split['split_type']}"
                    with engine.connect() as conn:
                        conn.execute(text(sql))
                        conn.commit()
                except Exception as e:
                    print(f"An error occurred: {e}")
                else:
                    print(f"Deleted record for {split['srid']}")

                # Convert split dictionary to pandas dataframe
                split_df = pd.DataFrame(split, index=[0])

                # Update database with dataframe
                try:
                    split_df.to_sql('player_splits', con=engine, if_exists='append', index=False)
                except Exception as e:
                    print(f"An error occurred: {e}")
                    raise Exception(e)
                else:
                    print(f"Inserted split for {split['srid']} and split_type {split['split_type']}")


        # DAY/NIGHT SPLITS
        if 'day_night' in splits:
            for day_night in splits['day_night']:
                split = {
                    "srid": player['id'],
                    "year": year,
                    "ab": day_night['ab'],
                    "hits": day_night['h'],
                    "runs": day_night['runs'],
                    "s": day_night['s'],
                    "d": day_night['d'],
                    "t": day_night['t'],
                    "hr": day_night['hr'],
                    "rbi": day_night['rbi'],
                    "bb": day_night['bb'],
                    "avg": float(day_night['avg']),
                    "obp": day_night['obp'],
                    "slg": day_night['slg'],
                    "ops": day_night['ops']
                }
                if day_night['value'] == 'day':
                    split.update({"split_type": 6})
                elif day_night['value'] == 'night':
                    split.update({"split_type": 7})

                # Delete existing split in database
                try:
                    sql = f"DELETE FROM player_splits WHERE srid = '{split['srid']}' AND year = {year} AND split_type = {split['split_type']}"
                    with engine.connect() as conn:
                        conn.execute(text(sql))
                        conn.commit()
                except Exception as e:
                    print(f"An error occurred: {e}")
                    raise Exception(e)
                else:
                    print(f"Deleted record for {split['srid']}")

                # Convert split dictionary to pandas dataframe
                split_df = pd.DataFrame(split, index=[0])

                # Update database with dataframe
                try:
                    split_df.to_sql('player_splits', con=engine, if_exists='append', index=False)
                except Exception as e:
                    print(f"An error occurred: {e}")
                    raise Exception(e)
                else:
                    print(f"Inserted split for {split['srid']} and split_type {split['split_type']}")