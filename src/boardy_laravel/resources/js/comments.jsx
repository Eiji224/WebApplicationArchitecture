import React, { useState, useEffect, useCallback } from "react";
import ReactDOM from 'react-dom/client';
import { startLogin, refreshToken } from "../../public/js/auth.js";
import { useWebsocket } from "./hooks/useWebsocket.jsx";

const API = 'https://boardy.api.local';

const rootElement = document.getElementById('react-app');

if (rootElement) {
    const rawData = rootElement.getAttribute('data-frontend-data');

    try {
        const frontendData = JSON.parse(rawData);
        const { postId, username } = frontendData;

        const root = ReactDOM.createRoot(rootElement);
        root.render(<App postId={postId} username={username} />);
    } catch (error) {
        console.error("Не удалось распарсить данные из Blade:", error);
        console.log("То, что пришло в JS:", rawData);
    }
}

function App({ postId, username }) {
    const [items, setItems] = useState([]);
    const [jwt, setJwt] = useState(null);

    useEffect(() => {
        sessionStorage.setItem('redirectUrl', window.location.href)

        const savedToken = sessionStorage.getItem('token')
        if (!savedToken) {
            refreshToken().then(newToken => {
                if (newToken) setJwt(newToken);
                else {
                    startLogin();
                }
            })
        }

        setJwt(savedToken);
    }, []);

    const authedFetch = useCallback(async (url, options = {}) => {
        const currentToken = jwt || sessionStorage.getItem('token');

        let response = await fetch(url, {
            ...options,
            headers: {
                ...options.headers,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + currentToken,
            }
        });

        if (response.status === 401) {
            const newToken = await refreshToken();
            if (!newToken) {
                localStorage.setItem('redirectUrl', window.location.href);
                startLogin();
                return null;
            }

            setJwt(newToken);

            return fetch(url, {
                ...options,
                headers: {
                    ...options.headers,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + newToken,
                }
            });
        }

        return response;
    }, [jwt]);

    const load = useCallback(async () => {
        if (!jwt && !sessionStorage.getItem('token')) return;

        const res = await authedFetch(`${API}/posts/${postId}/comments`);
        if (!res) return;

        const data = await res.json();
        setItems(data.items || []);
    }, [postId, jwt, authedFetch]);

    useEffect(() => { load().catch(err => console.error(err)); }, [load]);

    useWebsocket(`${API}/ws`, setItems)

    return (
        <div className="flex flex-col gap-10 mt-4">
            <Form postId={postId} username={username} onRefresh={load} authedFetch={authedFetch} />
            <ItemList items={items} onRefresh={load} authedFetch={authedFetch} />
        </div>
    );
}




function ItemList({ items, onRefresh, authedFetch }) {
    return (
        <div className="flex flex-col gap-5">
            {items.map(item => (
                <div key={item.id} className="flex flex-col p-5 gap-3 border border-gray-200 rounded-xl shadow-lg">
                    <div className="flex justify-between">
                        <strong>{item.author_name}</strong>
                        <span className="text-gray-400">{item.created_at}</span>
                    </div>

                    <p>{item.body}</p>

                    <EditButtons item={item} onRefresh={onRefresh} authedFetch={authedFetch} />
                </div>
            ))}
        </div>
    );
}

function EditButtons({ item, onRefresh, authedFetch }) {
    const [editId, setEditId] = useState(null);
    const [editText, setEditText] = useState('');

    const save = async (id) => {
        await authedFetch(`${API}/comments/${id}`, {
            method: 'PUT',
            body: JSON.stringify({ body: editText })
        });
        setEditId(null);
        onRefresh();
    };

    const del = async (id) => {
        if (!confirm('Удалить?')) return;
        await authedFetch(`${API}/comments/${id}`, {
            method: 'DELETE'
        });
        onRefresh();
    };

    return (
        editId !== item.id ? (
            <div>
                <button className="btn btn-sm btn-outline-secondary me-2"
                        onClick={() => {
                            setEditId(item.id);
                            setEditText(item.body);
                        }}>✏️</button>
                <button className="btn btn-sm btn-outline-danger"
                        onClick={() => del(item.id)}>🗑️</button>
            </div>
        ) : (
            <div className="input-group">
                <input className="form-control" value={editText}
                       onChange={e => setEditText(e.target.value)}/>
                <button className="btn btn-success" onClick={() => save(item.id)}>Сохранить</button>
                <button className="btn btn-secondary" onClick={() => setEditId(null)}>Отмена</button>
            </div>
        )
    );
}

function Form({ postId, username, onRefresh, authedFetch }) {
    const [text, setText] = useState('');

    const add = async () => {
        if (!text.trim()) return;
        await authedFetch(`${API}/posts/${postId}/comments`, {
            method: 'POST',
            body: JSON.stringify({
                body: text,
                author_name: username,
            })
        });
        setText('');
        onRefresh();
    };

    return(
        <div className="input-group mt-3">
            <textarea
                placeholder="Оставьте свой комментарий"
                className="resize-none p-3 mt-1 block w-full rounded-xl border border-gray-200 shadow-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-300"
                rows="3"
                onChange={e => setText(e.target.value)}
                value={text}
            ></textarea>
            <button
                className="px-7 py-2 border border-sky-700 bg-sky-500 text-white rounded-xl cursor-pointer hover:bg-sky-800 transition-all"
                onClick={add}>Отправить
            </button>
        </div>
    );
}
