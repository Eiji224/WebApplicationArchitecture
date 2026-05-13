<x-app-layout>
    <x-slot name="title">Создание поста</x-slot>

    <div class="flex justify-center items-start py-10 gap-5">
        <a
            class="flex px-5 py-3 bg-white rounded-full cursor-pointer hover:shadow-xl hover:scale-110 transition-all ease-in-out"
            href="{{ route('posts.index') }}"
        >
            <
        </a>

        <div class="flex flex-col gap-10 p-10 bg-white rounded-xl shadow-xl w-3/4">
            <h1 class="text-center text-xl font-bold">Новый пост</h1>

            <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="flex flex-col gap-5">
                    <div class="flex flex-col">
                        <label for="title" class="text-gray-400 text-sm">Название поста</label>
                        <input
                            type="text"
                            name="title"
                            id="title"
                            placeholder="Введите название поста..."
                            class="mt-1 block p-3 w-full rounded-xl border border-gray-200 shadow-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-300"
                        />
                    </div>

                    <div class="flex flex-col">
                        <label for="body" class="text-gray-400 text-sm">Содержимое поста</label>
                        <textarea
                            name="body"
                            id="body"
                            placeholder="Введите содержимое поста..."
                            class="resize-none p-3 mt-1 block w-full rounded-xl border border-gray-200 shadow-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-300"
                            rows="15"
                        ></textarea>
                    </div>
                </div>

                <div class="flex justify-end mt-5">
                    <button type="submit" class="px-7 py-2 border border-sky-700 bg-sky-500 text-white rounded-xl cursor-pointer hover:bg-sky-800 transition-all">
                        Создать
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
