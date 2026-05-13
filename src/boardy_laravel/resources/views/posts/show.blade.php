<x-app-layout>
    <x-slot name="title">{{ $post->title }}</x-slot>

    <div class="flex justify-center items-start py-10 gap-5">
        <a
            class="flex px-5 py-3 bg-white rounded-full cursor-pointer hover:shadow-xl hover:scale-110 transition-all ease-in-out"
            href="{{ route('posts.index') }}"
        >
            <
        </a>

        <div class="flex flex-col gap-15 w-3/4">
            <div class="flex flex-col gap-10 p-10 bg-white rounded-xl shadow-xl">
                <div class="flex flex-col gap-5">
                    <h1 class="text-center text-xl font-bold">{{ $post->title }}</h1>
                    <p>{{ $post->body }}</p>
                </div>

                <div class="flex justify-between items-center gap-10 mt-5">
                    <div class="flex flex-row gap-3 w-3/10">
                        @can('update', $post)
                            <a
                                class="flex-1 px-3 border rounded-xl py-3 text-center border-gray-500 text-gray-500 hover:bg-gray-300 hover:text-black transition-all"
                                href="{{ route('posts.edit', $post) }}"
                            >
                                Редактировать
                            </a>
                        @endcan

                        @can('delete', $post)
                            <button
                                type="submit"
                                form="deleteForm"
                                class="flex-1 border rounded-xl py-3 text-center cursor-pointer border-red-700 bg-red-500 text-white hover:bg-red-700 transition-all"
                            >
                                Удалить
                            </button>
                        @endcan
                    </div>
                    <span class="text-gray-400">Автор: {{ $post->author->name }}</span>
                </div>
            </div>

            @auth()
                <div class="flex flex-col gap-5 p-10 bg-white rounded-xl shadow-xl">
                    <h1 class="text-2xl font-bold">Комментарии</h1>
                    <form action="{{ route('comments.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="number" class="hidden" name="post_id" value="{{ $post->id }}" />

                        <textarea
                            name="body"
                            id="body"
                            placeholder="Оставьте свой комментарий"
                            class="resize-none p-3 mt-1 block w-full rounded-xl border border-gray-200 shadow-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-300"
                            rows="3"
                        ></textarea>

                        <div class="flex justify-end mt-5">
                            <input
                                type="submit"
                                value="Оставить комментарий"
                                class="px-7 py-2 border border-sky-700 bg-sky-500 text-white rounded-xl cursor-pointer hover:bg-sky-800 transition-all"
                            />
                        </div>
                    </form>

                    @forelse($post->comments as $comment)
                        <div class="flex flex-col p-5 gap-3 border border-gray-200 rounded-xl shadow-lg">
                            <div class="flex justify-between">
                                <p>{{ $comment->author->name }}</p>
                                <span class="text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p>{{ $comment->body }}</p>
                        </div>
                    @empty
                        <p class="text-center">Здесь пока что нет комментариев... Будьте первым!</p>
                    @endforelse
                </div>
            @endauth
        </div>
    </div>
    <form method="POST" action="{{ route('posts.destroy', $post) }}" id="deleteForm">
        @method('DELETE')
        @csrf
    </form>
</x-app-layout>
