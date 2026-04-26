const { useState, useEffect, useCallback } = React;
const API = 'https://boardy-api.eiji.ai-info.ru';
const POST_ID = 7;

function App() {
    const [items, setItems] = useState([]);
    const [jwt, setJwt] = useState(null);

    const load = useCallback(async () => {
        try {
            const res = await fetch(`${API}/posts/${POST_ID}/comments`);
            const data = await res.json();
            setItems(data.items || []);
        } catch (e) {
            console.error("Ошибка загрузки:", e);
        }
    }, []);

    useEffect(() => { load(); }, [load]);
    useEffect(() => {
        fetch('/api/me.php', { credentials: 'include' })
            .then(r => {
                if (!r.ok) return null;
                return r.json()
            })
            .then(data => {
                if (data && data.token) setJwt(data.token);
            })
            .catch(() => setJwt(null));
    }, []);

    const headers = {
        'Content-Type': 'application/json',
    };
    if (jwt) {
        headers['Authorization'] = 'Bearer ' + jwt;
    }

    return (
        <div className="container mt-4">
            <ItemList items={items} onRefresh={load} headers={headers} />
            <Form onRefresh={load} headers={headers} />
        </div>
    );
}

function ItemList({ items, onRefresh, headers }) {
    return (
        <div>
            {items.map(item => (
                <div key={item.id} className="card mb-2">
                    <div className="card-body">
                        <EditButtons item={item} onRefresh={onRefresh} headers={headers} />
                    </div>
                </div>
            ))}
        </div>
    );
}

function EditButtons({ item, onRefresh, headers }) {
    const [editId, setEditId] = useState(null);
    const [editText, setEditText] = useState('');

    const save = async (id) => {
        await fetch(`${API}/comments/${id}`, {
            method: 'PUT',
            headers,
            body: JSON.stringify({body: editText})
        });
        setEditId(null);
        onRefresh(); // Вызываем обновление списка
    };

    const del = async (id) => {
        if (!confirm('Удалить?')) return;
        await fetch(`${API}/comments/${id}`, {
            method: 'DELETE',
            headers
        });
        onRefresh();
    };

    return (
        editId !== item.id ? (
            <div>
                <strong>{item.author_name}</strong>
                <p>{item.body}</p>
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

function Form({ onRefresh, headers }) {
    const [text, setText] = useState('');

    const add = async () => {
        if (!text.trim()) return;
        await fetch(`${API}/posts/${POST_ID}/comments`, {
            method: 'POST',
            headers,
            body: JSON.stringify({body: text})
        });
        setText('');
        onRefresh();
    };

    return(
        <div className="input-group mt-3">
            <input className="form-control" placeholder="Комментарий"
                   value={text} onChange={e => setText(e.target.value)} />
            <button className="btn btn-primary" onClick={add}>Отправить</button>
        </div>
    );
}

ReactDOM.createRoot(document.getElementById('app')).render(<App />);