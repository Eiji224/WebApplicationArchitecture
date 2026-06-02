from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from datetime import datetime
import aiomysql
from contextlib import asynccontextmanager
import asyncio, json
import redis.asyncio as aioredis

import logging

from services.database import get_db
from routers import comments, ws

logger = logging.getLogger("uvicorn.error")


REDIS_URL = 'redis://redis:6379'


async def redis_subscriber():
    while True:
        client = None
        pubsub = None
        try:
            logger.info("[redis] Connecting...")
            client = aioredis.from_url(REDIS_URL)
            pubsub = client.pubsub()

            # Подписываемся
            await pubsub.subscribe('new_post', 'user.renamed')
            logger.info("[redis] Subscribed successfully.")

            while True:
                # Ждем сообщение 1 секунду
                message = await pubsub.get_message(ignore_subscribe_messages=True, timeout=1.0)

                if message is None:
                    # Чтобы видеть, что цикл жив, можно раскомментировать строку ниже (но будет много спама)
                    # logger.debug("[redis] No messages yet...")
                    continue

                # === МЫ ПОЛУЧИЛИ СООБЩЕНИЕ ===
                logger.info(f"[redis] RAW MESSAGE RECEIVED: {message}")

                try:
                    channel = message['channel'].decode()
                    logger.info(f"[redis] Channel: {channel}")

                    data = json.loads(message['data'])
                    logger.info(f"[redis] Data parsed: {data}")

                    if channel == 'new_post':
                        from routers import ws
                        await ws.manager.broadcast({'type': 'new_post', 'post': data})
                        logger.info("[redis] Broadcast done!")

                    elif channel == 'user.renamed':
                        from routers import ws

                        logger.info(f"Меняем пользователя")

                        conn = await get_db()
                        async with conn.cursor(aiomysql.DictCursor) as cur:
                            await cur.execute(
                                'UPDATE comments SET author_name=%s WHERE author_id=%s',
                                (data['new_name'], data['id'])
                            )
                        await conn.commit()
                        conn.close()

                        await ws.manager.broadcast({
                            'type': 'user_renamed',
                            'user_id': data['id'],
                            'new_name': data['new_name'],
                        })

                except Exception as e:
                    logger.error(f"[redis] Processing error: {e}", exc_info=True)

        except asyncio.CancelledError:
            break
        except Exception as e:
            logger.error(f"[redis] Connection error: {e}. Reconnecting...", exc_info=True)
            await asyncio.sleep(2)
        finally:
            if pubsub: await pubsub.aclose()
            if client: await client.aclose()


@asynccontextmanager
async def lifespan(app: FastAPI):
    task = asyncio.create_task(redis_subscriber())
    yield
    task.cancel()
    try:
        await task
    except asyncio.CancelledError:
        pass


app = FastAPI(title='Boardy API', version='0.2.0', lifespan=lifespan)
app.add_middleware(
    CORSMiddleware,
    allow_origin_regex=r"https?://.*",
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

app.include_router(comments.router)
app.include_router(ws.router)


@app.get('/status')
async def status():
    return {'status': 'ok', 'time': str(datetime.now())}


@app.get('/messages')
async def get_messages():
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT posts.body AS message, users.name, '
            'posts.created_at FROM posts '
            'JOIN users ON posts.author_id = users.id '
            'ORDER BY posts.created_at DESC'
        )
        messages = await cur.fetchall()
    await conn.close()

    for m in messages:
        m['created_at'] = str(m['created_at'])
    return {'messages': messages, 'count': len(messages)}


@app.get('/users')
async def get_users():
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT id, name, email, created_at FROM users'
        )
        users = await cur.fetchall()
    await conn.close()

    for u in users:
        u['created_at'] = str(u['created_at'])
    return {'users': users, 'count': len(users)}