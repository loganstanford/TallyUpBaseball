import requests
import sqlalchemy
from sqlalchemy import create_engine, text
from config import DB_CONNECTION_STRING, API_KEY

# Set up your database connection
engine = create_engine(DB_CONNECTION_STRING)


def fetch_team_mappings(engine):
    team_query = text("SELECT id, sportsradar_id FROM teams")  # Adjust column names as needed
    with engine.connect() as conn:
        teams = conn.execute(team_query).fetchall()
    return {team[1]: team[0] for team in teams}

def add_new_players_from_api(api_url, team_mappings, engine):
    response = requests.get(api_url)
    data = response.json()

    for team in data.get("teams", []):
        team_sportsradar_id = team["id"]
        db_team_id = team_mappings.get(team_sportsradar_id)

        if db_team_id:
            for position in team.get("positions", []):
                for player in position.get("players", []):
                    add_player_if_not_exists(player, db_team_id, engine)
        else:
            print(f"Team with Sportsradar ID {team_sportsradar_id} not found in database.")


def add_player_if_not_exists(player_data, team_id, engine):
    # Check if the player already exists
    check_player_query = text("SELECT id FROM players WHERE sportsradar_id = :player_id")
    player_id = player_data["id"]

    with engine.connect() as conn:
        result = conn.execute(check_player_query, {"player_id": player_id}).fetchone()

        # If the player doesn't exist, add them
        if not result:
            # Use preferred_name as first_name and concatenate last_name with suffix
            first_name = player_data["preferred_name"]
            last_name = player_data["last_name"]
            if "suffix" in player_data and player_data["suffix"]:
                last_name += f" {player_data['suffix']}"

            # Insert the new player into the database
            insert_player_query = text("""
                INSERT INTO players 
                (sportsradar_id, first_name, last_name, jersey_no, primary_position, status, team_id) 
                VALUES 
                (:sportsradar_id, :first_name, :last_name, :jersey_number, :primary_position, :status, :team_id)
            """)

            sql_params = {
                "sportsradar_id": player_id,
                "first_name": first_name,
                "last_name": last_name,
                "jersey_number": player_data.get("jersey_number"),
                "primary_position": player_data["primary_position"],
                "status": player_data["status"],
                "team_id": team_id
            }

            try:
                conn.execute(insert_player_query, sql_params)
                conn.commit()  # Explicit commit
                print(f"Added player {player_data['full_name']} to the database.")
            except Exception as e:
                print(f"Error adding player {player_data['full_name']}: {e}")
                conn.rollback()

# Example usage
api_url = "https://api.sportradar.com/mlb/trial/v7/en/league/depth_charts.json" + f"?api_key={API_KEY}"
team_mappings = fetch_team_mappings(engine)
add_new_players_from_api(api_url, team_mappings, engine)
