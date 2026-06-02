import { useEffect, useState, useRef } from 'react';

export const useWebsocket = (url, setItems) => {
    const socket = useRef(null);

    useEffect(() => {
        socket.current = new WebSocket(url);

        socket.current.onopen = () => console.log('ws is open')
        socket.current.onclose = () => console.log('ws is closed')
        socket.current.onmessage = (event) => {
            const data = JSON.parse(event.data);

            console.log(data.type)

            setItems((prevItems) => {
                switch(data.type) {
                    case 'new_comment':
                        return [...prevItems, data.comment];
                    case 'update_comment':
                        return prevItems.map(item =>
                            item.id === data.comment.id ? { ...item, ...data.comment } : item
                        );
                    case 'delete_comment':
                        return prevItems.filter(item => item.id !== data.comment_id);
                    default:
                        return prevItems;
                }
            });
        };

        return () => socket.current.close();
    }, [url, setItems]);
}
