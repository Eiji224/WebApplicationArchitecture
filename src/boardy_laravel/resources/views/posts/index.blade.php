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

            <div>
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
