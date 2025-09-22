document.addEventListener("click", async (e) => {
    // LIKE BUTTON
    const likeBtn = e.target.closest(".like-button");
    if (likeBtn) {
        const postId = likeBtn.dataset.postId;
        try {
            const res = await fetch(`/posts/${postId}/toggle-like`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
            });
            const data = await res.json();
            if (data.success) {
                const svg = likeBtn.querySelector("svg");
                const countSpan = likeBtn.querySelector(".like-count");
                if (data.liked) {
                    svg.classList.remove("text-gray-500");
                    svg.classList.add("text-red-500", "fill-current");
                    svg.setAttribute("fill", "currentColor");
                } else {
                    svg.classList.remove("text-red-500", "fill-current");
                    svg.classList.add("text-gray-500");
                    svg.setAttribute("fill", "none");
                }
                countSpan.textContent = data.likes_count;
            }
        } catch (err) {
            console.error(err);
        }
        return; // stop propagation
    }

    // COMMENT TOGGLE
    const postItem = e.target.closest(".post-item");
    if (!postItem) return;

    // Jangan toggle jika klik di dalam comments-area
    if (e.target.closest(".comments-area")) return;

    const commentArea = postItem.querySelector(".comments-area");
    const commentsList = commentArea.querySelector(".comments-list");
    const postId = postItem.dataset.postId;

    // toggle visibility
    commentArea.classList.toggle("hidden");
    if (!commentArea.classList.contains("hidden")) {
        try {
            const res = await fetch(`/posts/${postId}/replies`);
            const data = await res.json();

            commentsList.innerHTML = data.comments
                .map(
                    (c) => `
                    <div class="flex space-x-2 items-start p-2 border rounded">
                        <img src="${
                            c.user.avatar ||
                            "https://placehold.co/40x40/cccccc/333333?text=U"
                        }" class="w-10 h-10 rounded-full">
                        <div>
                            <p class="font-bold text-gray-800">${
                                c.user.name
                            }</p>
                            <p>${c.content}</p>
                        </div>
                    </div>
                `
                )
                .join("");
        } catch (err) {
            console.error(err);
        }
    }
});

// SUBMIT COMMENT
document.addEventListener("submit", async (e) => {
    const form = e.target.closest(".comment-form");
    if (!form) return;
    e.preventDefault();

    const postId = form.dataset.postId;
    const input = form.querySelector('input[name="content"]');
    const content = input.value.trim();
    if (!content) return;

    try {
        const res = await fetch(`/posts/${postId}/reply`, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
            body: new URLSearchParams({ content }),
        });
        const data = await res.json();

        if (data.success) {
            const commentsList = form
                .closest(".comments-area")
                .querySelector(".comments-list");
            commentsList.insertAdjacentHTML(
                "beforeend",
                `
                <div class="flex space-x-2 items-start p-2 border rounded">
                    <img src="${
                        data.reply.user.avatar ||
                        "https://placehold.co/40x40/cccccc/333333?text=U"
                    }" class="w-10 h-10 rounded-full">
                    <div>
                        <p class="font-bold text-gray-800">${
                            data.reply.user.name
                        }</p>
                        <p>${data.reply.content}</p>
                    </div>
                </div>
            `
            );
            input.value = "";
        }
    } catch (err) {
        console.error(err);
    }
});
