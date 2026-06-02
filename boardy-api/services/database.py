import aiomysql

DB_CONFIG = {
    'host': 'mysql',
    'port': 3306,
    'user': 'boardy',
    'password': 'boardy_password',
    'db': 'boardy_api',
    'charset': 'utf8mb4',
}

async def get_db():
    return await aiomysql.connect(**DB_CONFIG)