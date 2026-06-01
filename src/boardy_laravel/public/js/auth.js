import { generateVerifier, generateChallenge, generateState } from "./pkce.js";

const CLIENT_ID = '019e6971-353e-73f1-a021-14471d51631c'
const REDIRECT_URI = window.location.origin + '/oauth/callback'

export async function  startLogin() {
    const verifier = generateVerifier();
    const challenge = await generateChallenge(verifier);
    const state = generateState();

    sessionStorage.setItem('pkce_verifier', verifier);
    sessionStorage.setItem('oauth_state', state);

    const params = new URLSearchParams({
        client_id: CLIENT_ID,
        response_type: 'code',
        redirect_uri: REDIRECT_URI,
        code_challenge: challenge,
        code_challenge_method: 'S256',
        state: state,
        scope: '*',
    });

    window.location = '/oauth/authorize?' + params
}

export async function handleCallback() {
    const params = new URLSearchParams(window.location.search);
    const code = params.get('code');
    const state = params.get('state');

    if (!code) return null;

    const savedState = sessionStorage.getItem('oauth_state');
    if (state !== savedState) throw new Error('Invalid state');

    const verifier = sessionStorage.getItem('pkce_verifier');
    if (!verifier) throw new Error('No verifier');

    const res = await fetch('/oauth/token', {
        method: 'POST',
        credentials: 'include',
        body: new URLSearchParams({
            grant_type: 'authorization_code',
            client_id: CLIENT_ID,
            code: code,
            code_verifier: verifier,
            redirect_uri: REDIRECT_URI,
        }),
    }).catch(err => console.log(err));
    const data = await res.json()


    sessionStorage.removeItem('pkce_verifier');
    sessionStorage.removeItem('oauth_state');

    sessionStorage.setItem('token', data.access_token);

    return data.access_token;
}

export async function refreshToken() {
    const res = await fetch('/oauth/token', {
        method: 'POST',
        credentials: 'include',
        body: new URLSearchParams({
            grant_type: 'refresh_token',
            client_id: CLIENT_ID,
            scope: '*',
        }),
    });

    if (!res.ok) {
        startLogin();
        return null;
    }
    const data = await res.json();
    return data.access_token;
}
