document.addEventListener("click", async (e) => {
    // LIKE BUTTON
    console.log('like');
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

            // simpan komentar di dataset supaya bisa dipanggil lagi
            commentsList.dataset.comments = JSON.stringify(data.comments);
            commentsList.dataset.currentIndex = "0"; // mulai dari 0

            // render awal 5 komentar
            renderNextComments(commentsList, 5);
        } catch (err) {
            console.error(err);
        }
    } else {
        // reset kalau ditutup
        commentsList.innerHTML = "";
        commentsList.removeAttribute("data-comments");
        commentsList.removeAttribute("data-current-index");
    }

    function renderNextComments(commentsList, count) {
        const comments = JSON.parse(commentsList.dataset.comments || "[]");
        let currentIndex = parseInt(commentsList.dataset.currentIndex || "0");

        const nextIndex = Math.min(currentIndex + count, comments.length);
        const nextBatch = comments.slice(currentIndex, nextIndex);

        // render batch baru
        const html = nextBatch
            .map(
                (c) => `
            <div class="flex space-x-2 items-start p-2 border rounded">
                <img src="${
                    c.user?.avatar ||
                    "https://placehold.co/40x40/cccccc/333333?text=U"
                }" class="w-10 h-10 rounded-full">
                <div>
                    <p class="font-bold text-gray-800">${c.user?.name}</p>
                    <p>${c.content}</p>
                </div>
            </div>
        `
            )
            .join("");
        commentsList.insertAdjacentHTML("beforeend", html);

        // update index
        commentsList.dataset.currentIndex = nextIndex.toString();

        // hapus tombol lama
        const oldBtn = commentsList.querySelector(".load-more-btn");
        if (oldBtn) oldBtn.remove();

        // kalau masih ada komentar tersisa, buat tombol lagi
        if (nextIndex < comments.length) {
            const loadMoreBtn = document.createElement("button");
            loadMoreBtn.textContent = "Lihat komentar lainnya";
            loadMoreBtn.className =
                "load-more-btn mt-2 text-blue-600 hover:underline text-sm font-semibold";

            loadMoreBtn.addEventListener("click", () => {
                renderNextComments(commentsList, 5);
            });

            commentsList.insertAdjacentElement("beforeend", loadMoreBtn);
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
                        data.reply.user?.avatar ||
                        "https://placehold.co/40x40/cccccc/333333?text=U"
                    }" class="w-10 h-10 rounded-full">
                    <div>
                        <p class="font-bold text-gray-800">${
                            data.reply.user?.name
                        }</p>
                        <p>${data.reply.content}</p>
                    </div>
                </div>
            `
            );
            input.value = "";
        } else {
            // Inline error near the form
            let errorEl = form.querySelector(".comment-error");
            if (!errorEl) {
                errorEl = document.createElement("div");
                errorEl.className = "comment-error mt-2";
                errorEl.innerHTML =
                    '<div class="bg-red-50 border-2 border-red-300 p-2 rounded text-red-700 text-sm"></div>';
                form.parentElement.insertBefore(errorEl, form);
            }
            const container = errorEl.querySelector("div");
            let html = data.message || data.error || "Unknown error";
            if (Array.isArray(data.reasons) && data.reasons.length > 0) {
                html =
                    `<div class="font-semibold">${html}</div>` +
                    '<ul class="list-disc pl-5 mt-1">' +
                    data.reasons.map((r) => `<li>${String(r)}</li>`).join("") +
                    "</ul>";
            }
            container.innerHTML = html;
        }
    } catch (err) {
        console.error(err);
        let errorEl = form.querySelector(".comment-error");
        if (!errorEl) {
            errorEl = document.createElement("div");
            errorEl.className = "comment-error mt-2";
            errorEl.innerHTML =
                '<div class="bg-red-50 border-2 border-red-300 p-2 rounded text-red-700 text-sm">An error occurred while posting the reply.</div>';
            form.parentElement.insertBefore(errorEl, form);
        }
    }
});
