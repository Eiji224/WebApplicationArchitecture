from fastapi import APIRouter, HTTPException, Depends
from pydantic import BaseModel, Field
import aiomysql

from routers.ws import manager
from services.database import get_db
from services.auth import get_current_user

class CommentCreate(BaseModel):
    body: str = Field(..., min_length=1, max_length=2000)
    author_name: str = Field(..., min_length=1, max_length=255)

class CommentUpdate(BaseModel):
    body: str = Field(..., min_length=1, max_length=2000)

router = APIRouter()

@router.get('/posts/{post_id}/comments')
async def get_comments(post_id: int):
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'SELECT * FROM comments WHERE post_id=%s ORDER BY created_at',
            (post_id,)
        )
        items = await cur.fetchall()
    conn.close()

    for item in items:
        item['created_at'] = str(item['created_at'])

    return {'items': items, 'count': len(items)}

@router.post('/posts/{post_id}/comments', status_code=201)
async def create_comment(post_id: int, data: CommentCreate, user = Depends(get_current_user)):
    author_id = user['sub']
    author_name = data.author_name

    if not data.body.strip():
        raise HTTPException(status_code=422, detail='Текст пустой')
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute(
            'INSERT INTO comments (body, post_id, author_id, author_name) VALUES (%s, %s, %s, %s)',
            (data.body, post_id, author_id, author_name)
        )
        await conn.commit()
        new_id = cur.lastrowid

        await cur.execute('SELECT * FROM comments WHERE id=%s', (new_id,))
        new_comment = await cur.fetchone()
    conn.close()

    new_comment['created_at'] = str(new_comment['created_at'])
    await manager.broadcast({
        'type': 'new_comment',
        'comment': new_comment,
    })
    return {'id': new_id, 'body': data.body, 'status': 'created'}

@router.put('/comments/{comment_id}')
async def update_comment(comment_id: int, data: CommentUpdate, user = Depends(get_current_user)):
    if not data.body.strip():
        raise HTTPException(status_code=422, detail='Текст пустой')
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute('SELECT * FROM comments WHERE id=%s', (comment_id,))
        comment = await cur.fetchone()
        if not comment:
            raise HTTPException(status_code=404, detail='Not found')
        if int(comment['author_id']) != int(user['sub']):
            raise HTTPException(status_code=403, detail='Not your comment')

        await cur.execute(
            'UPDATE comments SET body=%s WHERE id=%s',
            (data.body, comment_id)
        )
        await conn.commit()
    conn.close()

    await manager.broadcast({
        'type': 'update_comment',
        'comment': {'id': comment_id, 'body': data.body.strip()}
    })
    return {'id': comment_id, 'body': data.body, 'status': 'updated'}

@router.delete('/comments/{comment_id}', status_code=204)
async def delete_comment(comment_id: int, user = Depends(get_current_user)):
    conn = await get_db()
    async with conn.cursor(aiomysql.DictCursor) as cur:
        await cur.execute('SELECT * FROM comments WHERE id=%s', (comment_id,))
        comment = await cur.fetchone()
        if not comment:
            raise HTTPException(status_code=404, detail='Not found')
        if int(comment['author_id']) != int(user['sub']):
            raise HTTPException(status_code=403, detail='Not your comment')

        await cur.execute('DELETE FROM comments WHERE id=%s', (comment_id,))
        await conn.commit()
    conn.close()

    await manager.broadcast({
        'type': 'delete_comment',
        'comment_id': comment_id
    })
    return {'ok': True}