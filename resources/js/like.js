document.addEventListener("click", async (e) => {
    console.log("bombai");
    if (e.target.closest(".like-button")) {
        const likeBtn = e.target.closest(".like-button");
        const postId = likeBtn.dataset.postId;

        try {
            const response = await fetch(`/posts/${postId}/toggle-like`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
                    Accept: "application/json",
                },
            });

            const data = await response.json();
            if (data.success) {
                const svg = likeBtn.querySelector("svg");
                const countSpan = likeBtn.querySelector(".like-count");

                // update icon
                if (data.liked) {
                    svg.classList.remove("text-gray-500");
                    svg.classList.add("text-red-500", "fill-current");
                    svg.setAttribute("fill", "currentColor");
                } else {
                    svg.classList.remove("text-red-500", "fill-current");
                    svg.classList.add("text-gray-500");
                    svg.setAttribute("fill", "none");
                }

                // update jumlah like
                countSpan.textContent = data.likes_count;
            }
        } catch (error) {
            console.error("Error toggle like:", error);
        }
    }
});
