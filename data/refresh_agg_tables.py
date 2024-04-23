from sqlalchemy import create_engine, text
from config import DB_CONNECTION_STRING

tables = ["agg_stats_ytd",
          "agg_stats_yesterday",
          "agg_stats_last30",
          "agg_stats_last15",
          "agg_stats_last7",
          "agg_stats_last3"]

engine = create_engine(DB_CONNECTION_STRING)

with engine.connect() as connection:
    for table in tables:
        truncate_query = text(f"TRUNCATE TABLE {table}")
        connection.execute(truncate_query)
        call_proc = text(f"CALL `update_{table}`()")
        connection.execute(call_proc)

    connection.commit()
