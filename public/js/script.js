// JavaScript untuk toggle visibility password
const toggle = document.getElementById("togglePassword");
const password = document.getElementById("password");

toggle.addEventListener("click", () => {
    const type = password.getAttribute("type") === "password" ? "text" : "password";
    password.setAttribute("type", type);

    // ubah icon saat password terlihat
    toggle.textContent = type === "password" ? "👁️" : "🙈";
});
