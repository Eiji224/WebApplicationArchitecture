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
            <div class="flex flex-col gap-10 p-10 mb-5 bg-white rounded-xl shadow-xl">
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
                    <div id="react-app"
                         data-frontend-data='{!! json_encode(["postId" => $post->id, "username" => auth()->user()->name], JSON_THROW_ON_ERROR) !!}'
                    ></div>
                </div>
            @endauth
        </div>
    </div>
    <form method="POST" action="{{ route('posts.destroy', $post) }}" id="deleteForm">
        @method('DELETE')
        @csrf
    </form>

    @viteReactRefresh
    @vite('resources/js/comments.jsx')
</x-app-layout>
