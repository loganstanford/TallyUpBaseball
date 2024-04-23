import requests
from bs4 import BeautifulSoup
from sqlalchemy import create_engine, text
import json
from datetime import datetime
from config import DB_CONNECTION_STRING

engine = create_engine(DB_CONNECTION_STRING)

def fetch_player_ids():
    with engine.connect() as connection:
        result = connection.execute(text("SELECT sportsradar_id, bbref_id FROM players"))
        return [(row['sportsradar_id'], row['bbref_id']) for row in result]

players = fetch_player_ids()

def get_player_appearances(bbref_id):
    url = f"https://www.baseball-reference.com/players/{bbref_id[0]}/{bbref_id}.shtml"
    response = requests.get(url)
    soup = BeautifulSoup(response.text, 'html.parser')

    appearances_data = {}
    table = soup.find('table', id='appearances')
    
    if table:
        rows = table.find_all('tr')
        for row in rows:
            if row.th and 'csk' in row.th.attrs:  # to skip header rows
                year = row.th['csk']
                data = {td['data-stat']: td.get_text() for td in row.find_all('td')}
                appearances_data[year] = data

    return appearances_data


def check_qualification(appearances_data, current_year):
    previous_year = str(int(current_year) - 1)
    position_mapping = {'P': 1, 'C': 2, '1B': 3, '2B': 4, '3B': 5, 'SS': 6, 'LF': 7, 'CF': 8, 'RF': 9, 'DH': 10}
    qualified_positions = []

    for position, pos_id in position_mapping.items():
        position_key = f'G_{position.lower()}'
        current_year_appearances = int(appearances_data.get(current_year, {}).get(position_key, 0))
        previous_year_appearances = int(appearances_data.get(previous_year, {}).get(position_key, 0))

        if current_year_appearances >= 20 or previous_year_appearances >= 20:
            qualified_positions.append(pos_id)

    return qualified_positions

def update_player_record(sportsradar_id, qualified_positions):
    qualified_positions_json = json.dumps(qualified_positions)
    with engine.connect() as connection:
        update_query = """
            UPDATE players
            SET qualified_positions = :positions
            WHERE sportsradar_id = :sportsradar_id
        """
        connection.execute(update_query, {'sportsradar_id': sportsradar_id, 'positions': qualified_positions_json})


def main():
    players = fetch_player_ids()
    current_year = str(datetime.now().year)
    for sportsradar_id, bbref_id in players:
        if bbref_id:  # Ensure there is a bbref_id before attempting to scrape
            appearances = get_player_appearances(bbref_id)
            qualified_positions = check_qualification(appearances, current_year)  # assuming current year processing
            update_player_record(sportsradar_id, qualified_positions)

if __name__ == "__main__":
    main()

