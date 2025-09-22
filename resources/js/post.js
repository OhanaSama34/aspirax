document.addEventListener("DOMContentLoaded", () => {
    const textarea = document.getElementById("composer-textarea");
    const postButton = document.getElementById("post-button");
    const postForm = document.getElementById("post-form");
    const postsContainer = document.getElementById("posts-container");

    // Function to create a new post element
    const createPostElement = (post) => {
        console.log(post);
        const postDiv = document.createElement("div");
        postDiv.classList.add(
            "p-4",
            "border-b",
            "border-gray-200",
            "hover:bg-gray-50",
            "cursor-pointer"
        );
        postDiv.innerHTML = `
             <div class="flex space-x-4">
                    <img src="https://placehold.co/48x48/cccccc/333333?text=U"
                        alt="${
                            post.user.name
                        } Avatar" class="w-12 h-12 rounded-full flex-shrink-0">

                    <div class="w-full">
                        <div class="flex items-center">
                            <p class="font-bold text-gray-900">${
                                post.user.name
                            }</p>
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
                        <p class="mt-1 text-gray-800">${post.content}</p>
                        <div class="flex justify-between items-center mt-4 text-gray-500 max-w-sm">
                            <div class="flex items-center space-x-2 hover:text-orange-500">
                                <svg class="w-5 h-5 ${
                                    post.replies.length > 0
                                        ? "text-red-500 fill-current"
                                        : "text-gray-500"
                                }" 
                                    fill="${
                                        post.replies.length > 0
                                            ? "currentColor"
                                            : "none"
                                    }" 
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                                    </path>
                                </svg>
                                <span>${post.replies.length}</span>
                            </div>
                            <div class="flex items-center space-x-2 hover:text-green-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23 4v6h-6m-1 8v6h6M3 10H2c-1.105 0-2 .895-2 2v4c0 1.105.895 2 2 2h2V10zm18-5h1c1.105 0 2 .895 2 2v4c0 1.105-.895 2-2 2h-2V5z"
                                        transform="rotate(90 12 12)"></path>
                                </svg>
                                <span>{{ rand(10, 100) }}</span>
                            </div>
                            <div class="flex items-center space-x-2 like-button cursor-pointer" 
                                data-post-id="${post.id}" 
                                data-liked="${
                                    post.likes.length > 0 ? "true" : "false"
                                }">
                                <svg class="w-5 h-5 ${
                                    post.likes.length > 0
                                        ? "text-red-500 fill-current"
                                        : "text-gray-500"
                                }" 
                                    fill="${
                                        post.likes.length > 0
                                            ? "currentColor"
                                            : "none"
                                    }" 
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                                </svg>
                                <span class="like-count">${
                                    post.likes.length
                                }</span>
                            </div>
                            <div class="hover:text-orange-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 5L12 19M5 12L19 12" stroke-width="2" stroke-linecap="round">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
        `;
        return postDiv;
    };

    // Handle enable/disable button
    textarea.addEventListener("input", () => {
        postButton.disabled = textarea.value.trim() === "";
    });

    // Handle Enter key to submit
    textarea.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && !e.shiftKey && !postButton.disabled) {
            e.preventDefault();
            postForm.dispatchEvent(
                new Event("submit", {
                    cancelable: true,
                })
            );
        }
    });

    // Handle submit via AJAX
    postForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        postButton.disabled = true;

        try {
            const formData = new FormData(postForm);
            const response = await fetch(postForm.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": formData.get("_token"),
                    Accept: "application/json",
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok && data.success) {
                const newPostElement = createPostElement(data.post);
                postsContainer.prepend(newPostElement);

                // Reset form
                textarea.value = "";
                postButton.disabled = true;
            } else {
                alert("Gagal posting: " + (data.error || "Unknown error"));
                postButton.disabled = false;
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Terjadi error saat posting");
            postButton.disabled = false;
        }
    });
});
