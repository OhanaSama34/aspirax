<x-app-layout>

    <div class="max-w-7xl mx-auto grid grid-cols-12">

        <!-- Sidebar Kiri -->
        <aside class="hidden md:block md:col-span-3 p-4">
            <div class="sticky top-0 space-y-4">

                <nav class="space-y-2">
                    <a href="#"
                        class="flex items-center space-x-4 px-4 py-2 rounded-full hover:bg-gray-100 font-bold text-lg text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                        </svg>

                        <span>Home</span>
                    </a>
                    <a href="/aspiro"
                        class="flex items-center space-x-4 px-4 py-2 rounded-full hover:bg-gray-100 font-medium text-lg text-gray-800">
                        <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                            <g>
                                <path
                                    d="M10.25 3.75c-3.59 0-6.5 2.91-6.5 6.5s2.91 6.5 6.5 6.5c1.795 0 3.419-.726 4.596-1.904 1.178-1.177 1.904-2.801 1.904-4.596 0-3.59-2.91-6.5-6.5-6.5zm-8.5 6.5c0-4.694 3.806-8.5 8.5-8.5s8.5 3.806 8.5 8.5c0 1.986-.682 3.83-1.824 5.262l4.781 4.781-1.414 1.414-4.781-4.781c-1.432 1.142-3.276 1.824-5.262 1.824-4.694 0-8.5-3.806-8.5-8.5z">
                                </path>
                            </g>
                        </svg>
                        <span>Chat With Aspiro</span>
                    </a>
                    <a href="#"
                        class="flex items-center space-x-4 px-4 py-2 rounded-full hover:bg-gray-100 font-medium text-lg text-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 3.75V16.5L12 14.25 7.5 16.5V3.75m9 0H18A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75h1.5m9 0h-9" />
                        </svg>

                        <span>Leaderboard</span>
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Feed Utama -->
        <main class="col-span-12 md:col-span-6 main-content-border">
            <div class="p-4 border-b border-gray-200">
                <form id="post-form" action="{{ route('posts.store') }}" method="POST">
                    @csrf
                    <div class="flex items-start space-x-4">
                        <img src="https://placehold.co/48x48/e2e8f0/333333?text=." alt="User Avatar"
                            class="w-12 h-12 rounded-full">
                        <div class="w-full">
                            <textarea id="composer-textarea" name="content"
                                class="w-full text-xl border-none p-2 resize-none focus:ring-0 placeholder-gray-500" rows="2"
                                placeholder="What's happening?!"></textarea>
                            <div id="post-error" class="hidden mt-2">
                                <div class="bg-red-50 border-2 border-red-300 p-3 rounded-xl text-red-700 text-sm">
                                </div>
                            </div>
                            <div class="flex justify-end items-center mt-3 pt-3 border-t border-gray-100">
                                <button id="post-button" type="submit"
                                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-5 py-2 rounded-full shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                    Send Aspiration
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div id="posts-container" class="posts-container">
                @foreach ($posts as $post)
                    <div class="post-item p-4 border-b border-gray-200 hover:bg-gray-50 cursor-pointer"
                        data-post-id="{{ $post->id }}">
                        <div class="flex space-x-4">
                            <img src="https://placehold.co/48x48/cccccc/333333?text=U"
                                alt="{{ $post->user->name }} Avatar" class="w-12 h-12 rounded-full flex-shrink-0">

                            <div class="w-full">
                                <div class="flex items-center">
                                    <p class="font-bold text-gray-900">{{ $post->user->name }}</p>
                                    <div class="ml-auto text-gray-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <g>
                                                <path
                                                    d="M3 12c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zm9 2c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm7 0c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z">
                                                </path>
                                            </g>
                                        </svg>
                                    </div>
                                </div>

                                <p class="mt-1 text-gray-800">{{ $post->content }}</p>

                                <div class="flex justify-between items-center mt-4 text-gray-500 max-w-sm">
                                    <!-- Comment count -->
                                    <div class="flex items-center space-x-2 hover:text-blue-500 comment-button">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                            </path>
                                        </svg>
                                        <span>{{ $post->replies->count() }}</span>
                                    </div>

                                    <!-- Retweet / share placeholder -->
                                    <div class="flex items-center space-x-2 hover:text-green-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M23 4v6h-6m-1 8v6h6M3 10H2c-1.105 0-2 .895-2 2v4c0 1.105.895 2 2 2h2V10zm18-5h1c1.105 0 2 .895 2 2v4c0 1.105-.895 2-2 2h-2V5z"
                                                transform="rotate(90 12 12)"></path>
                                        </svg>
                                        <span>{{ rand(10, 100) }}</span>
                                    </div>

                                    <!-- Like button -->
                                    <div class="flex items-center space-x-2 like-button cursor-pointer"
                                        data-post-id="{{ $post->id }}"
                                        data-liked="{{ $post->isLikedBy(Auth::user()) ? 'true' : 'false' }}">
                                        <svg class="w-5 h-5 {{ $post->isLikedBy(Auth::user()) ? 'text-red-500 fill-current' : 'text-gray-500' }}"
                                            fill="{{ $post->isLikedBy(Auth::user()) ? 'currentColor' : 'none' }}"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z">
                                            </path>
                                        </svg>
                                        <span class="like-count">{{ $post->likes()->count() }}</span>
                                    </div>

                                    <!-- Add / placeholder button -->
                                    <div class="hover:text-blue-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 5L12 19M5 12L19 12" stroke-width="2" stroke-linecap="round">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <!-- COMMENT AREA (default hidden) -->
                                <div class="comments-area hidden mt-4 border-t border-gray-200 pt-2">
                                    <div class="comments-list space-y-2"></div>
                                    <form class="comment-form mt-2 flex space-x-2" data-post-id="{{ $post->id }}">
                                        <input type="text" name="content" class="flex-1 border rounded px-2 py-1"
                                            placeholder="Tambahkan komentar...">
                                        <button type="submit"
                                            class="bg-blue-500 text-white px-3 rounded">Kirim</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>


        </main>

        <!-- Sidebar Kanan -->
        <aside class="hidden lg:block lg:col-span-3 p-4">
            <div class="sticky top-0 space-y-4">
                <div class="bg-gray-100 rounded-xl p-4">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Trends for you</h2>
                    <ul class="space-y-3">
                        <li class="hover:bg-gray-200 p-2 rounded-lg cursor-pointer">
                            <p class="text-sm text-gray-500">Trending in Indonesia</p>
                            <p class="font-bold">#Laravel11</p>
                            <p class="text-sm text-gray-500">15.2K Tweets</p>
                        </li>
                        <li class="hover:bg-gray-200 p-2 rounded-lg cursor-pointer">
                            <p class="text-sm text-gray-500">Technology · Trending</p>
                            <p class="font-bold">#TailwindCSS</p>
                            <p class="text-sm text-gray-500">4,321 Tweets</p>
                        </li>
                        <li class="hover:bg-gray-200 p-2 rounded-lg cursor-pointer">
                            <p class="text-sm text-gray-500">Business · Trending</p>
                            <p class="font-bold">#Web3</p>
                            <p class="text-sm text-gray-500">21.7K Tweets</p>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
    </div>

    @vite(['resources/js/post.js', 'resources/js/like.js'])

</x-app-layout>
