import jwt
import os
from fastapi import Header, HTTPException

PUBLIC_KEY_PATH = os.getenv("OAUTH_PUBLIC_KEY", "/laravel-storage/oauth-public.key")

async def get_current_user(authorization: str = Header(None)):
    if not authorization or not authorization.startswith('Bearer '):
        raise HTTPException(status_code=401, detail='Token required')
    token = authorization.split(' ')[1]

    try:
        with open(PUBLIC_KEY_PATH, "r") as key_file:
            public_key_content = key_file.read()

        payload = jwt.decode(
            token,
            public_key_content,
            algorithms=['RS256'],
            options={'verify_aud': False}
        )
        return payload
    except FileNotFoundError:
        raise HTTPException(status_code=500, detail="Public key file not found")
    except jwt.ExpiredSignatureError:
        raise HTTPException(status_code=401, detail='Token expired')
    except jwt.InvalidTokenError as e:
        # Для отладки можно временно вернуть str(e), чтобы увидеть причину
        raise HTTPException(status_code=401, detail='Invalid token')