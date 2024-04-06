import os
import pandas as pd
from fuzzywuzzy import process
from datetime import datetime
from sqlalchemy import create_engine, text
from config import DB_CONNECTION_STRING  # Importing from config.py

# Create an SQLAlchemy engine
engine = create_engine(DB_CONNECTION_STRING)

# Function to execute SQL commands
def execute_sql(sql_commands):
    with engine.connect() as connection:
        with connection.begin() as transaction:
            try:
                for command in sql_commands:
                    connection.execute(command)
                transaction.commit()
                print("SQL commands executed successfully.")
            except Exception as e:
                transaction.rollback()
                print(f"An error occurred: {e}")

# Get the current year
current_year = datetime.now().year

# Assuming the script is running in the same directory as the 'rosters.xlsx' file
rosters_path = os.path.join(os.path.dirname(__file__), 'rosters.xlsx')

# Manager names and IDs mapping
manager_names_to_ids = {
    "Ken": 1, "Fran/Steve": 2, "Tony/Myron": 3, "V/J/C": 4, "Logan/Jeff": 5,
    "N2": 6, "Robert": 7, "Bobby2": 8, "O2": 9, "Jr.": 10
}

# Load the Excel file without treating any row as the header
all_sheets = pd.read_excel(rosters_path, sheet_name=None, header=None)

divisions_to_manager_info = {}

# Iterate through the sheets and perform fuzzy matching
for sheet_name, sheet_data in all_sheets.items():
    if not sheet_data.empty:
        # Extract the potential manager name from the first cell after removing the league prefix
        potential_manager_name_parts = str(sheet_data.iloc[0, 0]).split(':')
        if len(potential_manager_name_parts) > 1:
            potential_manager_name = potential_manager_name_parts[-1].strip()
            best_match, similarity = process.extractOne(potential_manager_name, list(manager_names_to_ids.keys()))
            similarity_threshold = 80
            if similarity > similarity_threshold:
                league = 'American' if sheet_name.startswith('A') else 'National'
                divisions_to_manager_info[sheet_name] = (manager_names_to_ids[best_match], league)

# Now we can generate the SQL insert statements
insert_statements = []
for division, (manager_id, league) in divisions_to_manager_info.items():
    statement = f"INSERT INTO divisions (name, manager_id, league, year) VALUES ('{division}', {manager_id}, '{league}', {current_year});"
    insert_statements.append(statement)



# Print the mapping for user confirmation
print("Division to Manager Mapping:")
# Invert the manager_names_to_ids dictionary for easy lookup of names by ID
id_to_manager_names = {id: name for name, id in manager_names_to_ids.items()}
for division_name, (manager_id, league) in divisions_to_manager_info.items():
    # Lookup the manager's name using the ID
    manager_name = id_to_manager_names.get(manager_id, "Unknown Manager")
    print(f"{league} {division_name}: {manager_name}")

# Create the SQL text command for insert
insert_sql = text("""
    INSERT INTO divisions (name, manager_id, league, year)
    VALUES (:division, :manager_id, :league, :year)
""")

# Iterate through the mapping and execute insert statements
confirmation = input("Does the mapping look correct? (yes/no): ").strip().lower()
# Iterate through the mapping and execute insert statements
if confirmation == 'yes':
    with engine.connect() as connection:
        try:
            for division, (manager_id, league) in divisions_to_manager_info.items():
                params = {
                    'division': division, 
                    'manager_id': manager_id, 
                    'league': league, 
                    'year': datetime.now().year
                }

                # Debugging query
                debug_query = f"INSERT INTO divisions (name, manager_id, league, year) VALUES ('{division}', {manager_id}, '{league}', {datetime.now().year});"
                print("Preparing to execute SQL:", debug_query)  # Print the query for debugging

                # Execute the actual prepared statement
                result = connection.execute(insert_sql, params)
                print("Query executed, rows affected:", result.rowcount)

            # Explicit commit, in case it's needed
            connection.commit()
            print("Transactions committed successfully.")

        except Exception as e:
            # Explicit rollback in case of error
            connection.rollback()
            print(f"An error occurred: {e}")
else:
    print("Aborted. The mapping was not confirmed.")
