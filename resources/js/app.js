import "./bootstrap";

import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

import Web3 from "web3";
import axios from "axios";

window.Web3 = Web3;

document.addEventListener("DOMContentLoaded", () => {
    const metaMaskLoginButtons = document.querySelectorAll(
        "#metamask-login-desktop, #metamask-login-mobile, #metamask-login-page"
    );

    metaMaskLoginButtons.forEach((metaMaskLoginButton) => {
        metaMaskLoginButton.addEventListener("click", async () => {
            console.log("Button clicked:", metaMaskLoginButton.id);

            const errorBoxId =
                metaMaskLoginButton.id === "metamask-login-desktop" ||
                metaMaskLoginButton.id === "metamask-login-page"
                    ? "metamask-error-desktop"
                    : metaMaskLoginButton.id === "metamask-login-mobile"
                    ? "metamask-error-mobile"
                    : "metamask-error-page";
            const errorBox = document.getElementById(errorBoxId);

            // 🚨 Cek MetaMask
            if (!window.ethereum) {
                if (errorBox) {
                    errorBox.textContent =
                        "🚨 MetaMask tidak terdeteksi. Silakan install MetaMask extension di browser Anda terlebih dahulu.";
                    errorBox.classList.remove("hidden");
                }
                return;
            }

            const web3 = new Web3(window.ethereum);
            const signatureUrl =
                metaMaskLoginButton.getAttribute("data-signature-url");
            const authenticateUrl = metaMaskLoginButton.getAttribute(
                "data-authenticate-url"
            );
            const redirectUrl =
                metaMaskLoginButton.getAttribute("data-redirect-url");

            try {
                // 1️⃣ Coba ambil akun yang sudah connect
                let accounts = await window.ethereum.request({
                    method: "eth_accounts",
                });

                // 2️⃣ Kalau belum ada → minta connect (popup muncul)
                if (!accounts || accounts.length === 0) {
                    accounts = await window.ethereum.request({
                        method: "eth_requestAccounts",
                    });
                }

                const address = accounts[0];
                console.log("Address:", address);

                // 3️⃣ Ambil message dari server
                const response = await axios.get(signatureUrl);
                const message = response.data.message;

                // 4️⃣ User tanda tangan
                const signature = await web3.eth.personal.sign(
                    message,
                    address,
                    ""
                );

                // 5️⃣ Kirim ke server
                const { status } = await axios.post(authenticateUrl, {
                    address,
                    signature,
                });

                if (status === 200) {
                    window.location = redirectUrl;
                }
            } catch (e) {
                console.error("MetaMask login failed:", e);
                if (errorBox) {
                    errorBox.textContent = "❌ Gagal login dengan MetaMask.";
                    errorBox.classList.remove("hidden");
                }
            }
        });
    });
});
