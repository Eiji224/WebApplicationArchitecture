<x-app-layout>
    <div class="flex justify-center items-center py-10">
        <div class="flex flex-col gap-5">
            <div class="flex flex-col justify-center items-center w-full gap-5">
                <h1 class="text-3xl font-medium">Все посты</h1>
                <a
                    href="{{ route('posts.create') }}"
                    class="py-2 w-full bg-sky-500 text-white text-center text-xl rounded-xl hover:bg-sky-800 transition-all"
                >
                    Написать свой пост
                </a>
            </div>

            <div id="posts-feed" class="flex flex-col gap-5">
                @foreach($posts as $post)
                    <a href="{{ route('posts.show', $post) }}">
                        <div class="flex flex-col p-5 rounded-xl shadow-sm bg-white hover:cursor-pointer hover:shadow-xl transition-all">
                            <div class="flex justify-between">
                                <h3 class="text-lg">{{ $post->title }}</h3>
                                <div class="flex flex-row gap-2 text-gray-400">
                                    <span>{{ $post->author->name }}</span>
                                    <span>{{ $post->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div>
                                <p>{{ $post->body }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div>
                {{ $posts->links() }}
            </div>
        </div>
    </div>

    <script>
        @if(app()->environment('production'))
            const wsUrl = 'wss://api.{{ config("app.fastapi_domain") }}/ws'
        @else
            const wsUrl = 'ws://localhost:8000/ws'
        @endif

        function escapeHtml(str) {
            if (!str) return '';
            const d = document.createElement('div');
            d.textContent = str;
            return d.innerHTML;
        }

        function connect() {
            const ws = new WebSocket(wsUrl)
            ws.onopen = () => console.log('WS connected')
            ws.onmessage = (e) => {
                const msg = JSON.parse(e.data)
                if (msg.type === 'new_post') prependPost(msg.post)
            }
            ws.onclose = () => setTimeout(connect, 3000)
        }

        function prependPost(post) {
            const feed = document.getElementById('posts-feed');
            if (!feed) return;

            const el = document.createElement('a');
            el.href = `/posts/${post.id}`;

            el.innerHTML = `
            <div class="flex flex-col p-5 rounded-xl shadow-sm bg-white hover:cursor-pointer hover:shadow-xl transition-all">
                <div class="flex justify-between">
                    <h3 class="text-lg">${escapeHtml(post.title)}</h3>
                    <div class="flex flex-row gap-2 text-gray-400">
                        <span>${escapeHtml(post.author.name || post.author)}</span>
                        <span>только что</span>
                    </div>
                </div>
                <div>
                    <p>${escapeHtml(post.body)}</p>
                </div>
            </div>
        `;

            feed.prepend(el);
        }

        connect()
    </script>
</x-app-layout>
