import os, re
import pandas as pd
from sqlalchemy import create_engine, text
from fuzzywuzzy import process, fuzz
from config import DB_CONNECTION_STRING
from datetime import datetime

def get_division_id(division_name, engine):
    """
    Retrieves the ID of a division given its name.

    :param division_name: The name of the division to look up.
    :param engine: SQLAlchemy engine object for database connection.
    :return: The ID of the division or None if not found.
    """
    # Prepare the SQL query
    query = text("SELECT id FROM divisions WHERE name = :division_name AND year = YEAR(CURRENT_DATE)")
    
    # Execute the query
    with engine.connect() as conn:
        result = conn.execute(query, {"division_name": division_name}).fetchone()

    # Check if a result was found
    if result:
        return result[0]  # Return the division ID
    else:
        return None  # No matching division found

# Example usage:
# engine = create_engine(DB_CONNECTION_STRING)
# division_id = get_division_id("A1", engine)
# print(division_id)

def get_current_roster(division_id, engine):
    """
    Retrieves the current roster for a given division.

    :param division_id: The ID of the division.
    :param engine: SQLAlchemy engine object for database connection.
    :return: A set containing the IDs of players and pitching staffs in the current roster.
    """
    current_year = datetime.now().year
    roster = {
        'player_ids': set(),
        'pitching_ids': set()
    }

    # SQL query to find the latest transaction for each player/pitching staff in the division for the current year
    query = text("""
        SELECT t.player_id, t.pitching_id FROM transactions t
        INNER JOIN (
            SELECT player_id, MAX(id) as max_id FROM transactions
            WHERE division_id = :division_id AND year(transaction_date) = :year
            GROUP BY player_id
        ) latest ON t.player_id = latest.player_id AND t.id = latest.max_id
        WHERE t.transaction_type_id != 2  # Exclude 'drop' transactions
    """)

    with engine.connect() as conn:
        results = conn.execute(query, {"division_id": division_id, "year": current_year}).fetchall()

    for result in results:
        if result.player_id is not None:
            roster['player_ids'].add(result.player_id)
        if result.pitching_id is not None:
            roster['pitching_ids'].add(result.pitching_id)

    return roster


def match_team(team_name, engine):
    """
    Attempts to find a direct or fuzzy match for a team name in the teams table.

    :param team_name: The team name to match.
    :param engine: SQLAlchemy engine object for database connection.
    :return: The ID of the matched team or None if no match is found.
    """
    # Attempt direct match first
    direct_match_query = text("SELECT id FROM teams WHERE name = :team_name")
    with engine.connect() as conn:
        match_result = conn.execute(direct_match_query, {"team_name":team_name}).fetchone()

    if match_result:
        return match_result[0]

    # If no direct match, attempt fuzzy match
    teams_query = text("SELECT id, name FROM teams")
    with engine.connect() as conn:
        all_teams = conn.execute(teams_query).fetchall()
    team_mapping = {team[1]: team[0] for team in all_teams}
    team_names = [team[1] for team in all_teams]
    possible_matches = process.extract(team_name, team_names)

    best_match_team, score = max(possible_matches, key=lambda x: x[1])
    if score >= 80:
        return team_mapping[best_match_team]
    else:
        return None


def match_player(player_name, team_name, engine):
    """
    Fuzzy matches a player name from the sheet to a player in the players table,
    limited to players with a specific team ID.
    
    :param player_name: The name of the player to match.
    :param team_name: The team name associated with the player.
    :param engine: SQLAlchemy engine object for database connection.
    :return: The ID of the matched player or prompt for user input if unsure.
    """

    # Use regex to remove any text in parentheses from the player name
    cleaned_player_name = re.sub(r'\s*\(.*?\)\s*', '', player_name).strip()

    # Use match_team to find the team ID corresponding to the team name
    team_id = match_team(team_name, engine)

    if not team_id:
        print(f"No team found for name {team_name}")
        return None

    # Fetch player names and IDs from the players table for the given team
    player_query = text("SELECT id, CONCAT(first_name, ' ', last_name) as full_name FROM players WHERE team_id = :team_id")
    with engine.connect() as conn:
        players = conn.execute(player_query, {'team_id': team_id}).fetchall()

    # Check for a direct match first
    for player_id, full_name in players:
        if full_name == cleaned_player_name:
            return player_id

    # If no direct match found, use fuzzy matching
    player_mapping = {player[1]: player[0] for player in players}
    player_names = [player[1] for player in players]
    possible_matches = process.extract(player_name, player_names)

    if possible_matches:
        best_match_name, score = max(possible_matches, key=lambda x: x[1])
        if score >= 80:
            return player_mapping[best_match_name]
        else:
            print(f"Uncertain match for player '{player_name}' on team '{team_name}':")
            for match, score in possible_matches:
                print(f"- {match} (score: {score})")
            player_id_input = input("Enter the correct player ID or 'skip' to omit this player: ").strip()
            if player_id_input.isdigit():
                return int(player_id_input)
            elif player_id_input.lower() == 'skip':
                return None

    return None

# Example usage:
# engine = create_engine(DB_CONNECTION_STRING)
# player_id = match_player("Pete Alonso", "Team A", engine)
# print(player_id)


def compare_and_update_roster(sheet_roster, current_roster, division_id, engine):
    """
    Compares the roster from the Excel sheet with the current roster in the database,
    and updates the database accordingly.

    :param sheet_roster: Set of player or pitching staff IDs from the Excel sheet.
    :param current_roster: Set of player or pitching staff IDs from the current database roster.
    :param division_id: The ID of the division.
    :param engine: SQLAlchemy engine object for database connection.
    """
    # Determine players to add and to drop
    players_to_add = sheet_roster - current_roster
    players_to_drop = current_roster - sheet_roster

    # Prepare SQL commands
    add_sql = text("""
        INSERT INTO transactions (player_id, division_id, transaction_type_id, transaction_date)
        VALUES (:player_id, :division_id, 1, CURRENT_DATE)
    """)
    drop_sql = text("""
        INSERT INTO transactions (player_id, division_id, transaction_type_id, transaction_date)
        VALUES (:player_id, :division_id, 2, CURRENT_DATE)
    """)

    # Execute SQL commands
    with engine.connect() as connection:
        try:
            for player_id in players_to_add:
                connection.execute(add_sql, {"player_id": player_id, "division_id": division_id})
            
            for player_id in players_to_drop:
                connection.execute(drop_sql, {"player_id": player_id, "division_id": division_id})
            
            connection.commit()  # Commit all the changes
        except Exception as e:
            print(f"Error while updating roster: {e}")
            connection.rollback()  # Roll back if there's an error

# Example usage:
# engine = create_engine(DB_CONNECTION_STRING)
# compare_and_update_roster({1, 2, 3}, {2, 3, 4}, 1, engine)  # Example IDs and division ID

def compare_and_update_pitching_staff(sheet_pitching_staff, current_pitching_staff, division_id, engine):
    """
    Compares the pitching staff from the Excel sheet with the current pitching staff in the database,
    and updates the database accordingly.

    :param sheet_pitching_staff: Set of pitching staff team IDs from the Excel sheet.
    :param current_pitching_staff: Set of pitching staff team IDs from the current database roster.
    :param division_id: The ID of the division.
    :param engine: SQLAlchemy engine object for database connection.
    """
    # Determine pitching staff to add and to drop
    pitching_staff_to_add = sheet_pitching_staff - current_pitching_staff
    pitching_staff_to_drop = current_pitching_staff - sheet_pitching_staff

    # Prepare SQL commands
    add_pitching_sql = text("""
        INSERT INTO transactions (pitching_id, division_id, transaction_type_id, transaction_date)
        VALUES (:pitching_id, :division_id, 1, CURRENT_DATE)  # Assuming 1 is 'Add'
    """)
    drop_pitching_sql = text("""
        INSERT INTO transactions (pitching_id, division_id, transaction_type_id, transaction_date)
        VALUES (:pitching_id, :division_id, 2, CURRENT_DATE)  # Assuming 2 is 'Drop'
    """)

    # Execute SQL commands
    with engine.connect() as connection:
        try:
            for pitching_id in pitching_staff_to_add:
                connection.execute(add_pitching_sql, {"pitching_id": pitching_id, "division_id": division_id})
            
            for pitching_id in pitching_staff_to_drop:
                connection.execute(drop_pitching_sql, {"pitching_id": pitching_id, "division_id": division_id})
            
            connection.commit()  # Commit all the changes
        except Exception as e:
            print(f"Error while updating pitching staff: {e}")
            connection.rollback()  # Roll back if there's an error


def main():
    # Create SQLAlchemy engine
    engine = create_engine(DB_CONNECTION_STRING)
    rosters_path = os.path.join(os.path.dirname(__file__), 'rosters.xlsx')
    xls = pd.ExcelFile(rosters_path)

    for sheet_name in xls.sheet_names:
        print(f"Processing division: {sheet_name}")

        # Get the division ID from the database
        division_name = sheet_name.strip()
        division_id = get_division_id(division_name, engine)
        if division_id is None:
            print(f"Division {sheet_name} not found in the database.")
            continue

        # Get the current roster from the database
        current_roster = get_current_roster(division_id, engine)

        # Read the roster from the Excel sheet
        sheet_player_ids = set()
        sheet_pitching_ids = set()

        # Read the roster from the Excel sheet, skipping the header row
        sheet_df = pd.read_excel(rosters_path, sheet_name=sheet_name, skiprows=1)

        # Process players and teams
        for i, row in sheet_df.iterrows():
            # Skip empty rows rows
            if pd.isna(row[1]):
                continue

            if isinstance(row[0], str) and "PITCHING STAFF" in row[0]:
                # For each pitching staff entry, get the team ID and add it to the pitching_staff_ids set
                for team_col_index in range(1, 4):  # Assuming team names are in columns B, C, D
                    team_name = row[team_col_index]
                    if pd.notna(team_name):  # Check if the team_name cell is not empty
                        team_id = match_team(team_name, engine)
                        if team_id:
                            sheet_pitching_ids.add(team_id)
                        else:
                            # If the team wasn't found, prompt the user
                            response = input(f"Could not find a match for pitching staff '{team_name}'. Enter Team ID to add manually, or 'skip': ").strip()
                            if response.isdigit():
                                sheet_pitching_ids.add(int(response))
                            elif response.lower() == 'skip':
                                print(f"Skipped pitching staff for team {team_name}.")
                            else:
                                print(f"Invalid input. Skipped pitching staff for team {team_name}.")
                continue # Skip to the next row after processing pitching staff
            
            # Extract player names and teams for positions
            player_names = [row[1], row[2], row[3]]  # Player names in columns B, C, D
            team_names = [sheet_df.iloc[i+1, 1], sheet_df.iloc[i+1, 2], sheet_df.iloc[i+1, 3]]  # Team names in the next row

            # Call the match_player function for each player
            for player_name, team_name in zip(player_names, team_names):
                if pd.isna(player_name) or pd.isna(team_name):
                    continue  # Check if player_name is not NaN
                player_id = match_player(player_name, team_name, engine)
                if player_id:
                    sheet_player_ids.add(player_id)
                else:
                    # Prompt for manual intervention if match_player returned None
                    response = input(f"Could not find a match for player '{player_name}'. Enter Player ID to add manually, or 'skip': ").strip()
                    if response.isdigit():
                        sheet_player_ids.add(int(response))
                    elif response.lower() == 'skip':
                        print(f"Skipped {player_name}.")
                    else:
                        print(f"Invalid input. Skipped {player_name}.")

        # Inside the main function after processing each sheet
        current_player_ids = current_roster['player_ids']
        current_pitching_ids = current_roster['pitching_ids']
        compare_and_update_roster(sheet_player_ids, current_player_ids, division_id, engine)
        compare_and_update_pitching_staff(sheet_pitching_ids, current_pitching_ids, division_id, engine)

        print(f"Division {sheet_name} processed successfully.")

if __name__ == "__main__":
    main()
