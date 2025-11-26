const slides = document.querySelector(".slides");
const radios = slides.querySelectorAll('input[name="radio-btn"]');
let counter = 0;
const totalSlides = radios.length;
const AUTO_INTERVAL = 6000;

function goToSlide(index) {
    slides.style.marginLeft = `-${index * 100}%`;
    radios[index].checked = true;
    counter = index;
}

let autoSlide = setInterval(() => {
    counter = (counter + 1) % totalSlides;
    goToSlide(counter);
}, AUTO_INTERVAL);

radios.forEach((radio, index) => {
    radio.addEventListener("click", () => {
        clearInterval(autoSlide);
        goToSlide(index);
        setTimeout(() => {
            autoSlide = setInterval(() => {
                counter = (counter + 1) % totalSlides;
                goToSlide(counter);
            }, AUTO_INTERVAL);
        }, 10000);
    });
});

// Jalankan Swiper dulu
const swiper = new Swiper(".card-wrapper", {

    spaceBetween: 30,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
        dynamicBullets: true,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {
        0: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
    },
    on: {
        init: () => {
            // Jalankan animasi setelah Swiper siap
            const animatedElements =
                document.querySelectorAll(".animate-hidden");

            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add("show");
                            observer.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.2 }
            );

            animatedElements.forEach((el) => observer.observe(el));
        },
    },
});

const sidebar = document.getElementById("sidebar");
const menuBtn = document.getElementById("menuBtn");

menuBtn.addEventListener("click", () => {
    sidebar.classList.toggle("active");
    menuBtn.textContent = sidebar.classList.contains("active") ? "✕" : "☰";
});

function showPanel(panelId) {
    document
        .querySelectorAll(".panel")
        .forEach((p) => p.classList.remove("active"));
    document.getElementById(panelId).classList.add("active");
}

function closePanel(event) {
    if (event) event.stopPropagation();
    document
        .querySelectorAll(".panel")
        .forEach((p) => p.classList.remove("active"));
}

document.addEventListener("click", (e) => {
    const anyPanelActive = document.querySelector(".panel.active");
    if (
        !anyPanelActive &&
        !sidebar.contains(e.target) &&
        e.target !== menuBtn
    ) {
        sidebar.classList.remove("active");
        menuBtn.textContent = "☰";
    }
});

document.querySelectorAll(".badge").forEach((badge) => {
    const lineHeight = parseFloat(window.getComputedStyle(badge).lineHeight);
    const maxHeight = lineHeight * 1.2;

    if (badge.scrollHeight > maxHeight) {
        let fontSize = parseFloat(window.getComputedStyle(badge).fontSize);
        while (badge.scrollHeight > maxHeight && fontSize > 12) {
            fontSize -= 1;
            badge.style.fontSize = fontSize + "px";
        }
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const dropdownButtons = document.querySelectorAll(".book-now");
    let activeDropdown = null;

    dropdownButtons.forEach((button) => {
        button.addEventListener("click", (e) => {
            e.stopPropagation();

            if (activeDropdown) {
                activeDropdown.remove();
                activeDropdown = null;
            }

            const sourceDropdown =
                button.parentElement.querySelector(".dropdown-content");
            if (!sourceDropdown) return;

            const clone = sourceDropdown.cloneNode(true);
            clone.classList.add("cloned-dropdown");
            document.body.appendChild(clone);

            const rect = button.getBoundingClientRect();
            clone.style.position = "fixed";
            clone.style.top = rect.bottom + 6 + "px";
            clone.style.left = rect.left + "px";
            clone.style.display = "block";
            clone.style.zIndex = 99999;

            activeDropdown = clone;
        });
    });

    document.addEventListener("click", () => {
        if (activeDropdown) {
            activeDropdown.remove();
            activeDropdown = null;
        }
    });

    window.addEventListener("scroll", () => {
        if (activeDropdown) {
            activeDropdown.remove();
            activeDropdown = null;
        }
    });
});

document.querySelectorAll(".see-more").forEach((btn) => {
    btn.addEventListener("click", () => {
        const roomName = btn.getAttribute("data-room");
        const fasilitas = btn.getAttribute("data-fasilitas").split(",");
        const harga = btn.getAttribute("data-harga");

        document.getElementById("popupTitle").textContent = roomName;

        const ul = document.getElementById("popupFeatures");
        ul.innerHTML = "";
        fasilitas.forEach((f) => {
            const li = document.createElement("li");
            li.textContent = f.trim();
            ul.appendChild(li);
        });

        const popup = document.getElementById("roomPopup");
        const popupBookBtn = popup.querySelector(".book-now");

        let sisa = 1;
        if (
            window.roomAvailability &&
            window.roomAvailability[roomName] !== undefined
        ) {
            sisa = window.roomAvailability[roomName];
        }

        if (sisa > 0) {
            popupBookBtn.disabled = false;
            popupBookBtn.style.opacity = "1";
            popupBookBtn.style.cursor = "pointer";
        } else {
            popupBookBtn.disabled = true;
            popupBookBtn.style.opacity = "0.5";
            popupBookBtn.style.cursor = "not-allowed";
        }

        popup.style.display = "flex";
    });
});

document.getElementById("closePopup").addEventListener("click", () => {
    document.getElementById("roomPopup").style.display = "none";
});
window.addEventListener("click", (e) => {
    const popup = document.getElementById("roomPopup");
    if (e.target === popup) popup.style.display = "none";
});

document.addEventListener("DOMContentLoaded", function () {
    const checkIn = document.getElementById("checkin");
    const checkOut = document.getElementById("checkout");

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return `${year}-${month}-${day}`;
    }

    const params = new URLSearchParams(window.location.search);
    const checkinParam = params.get("checkin");
    const checkoutParam = params.get("checkout");

    const today = new Date();
    checkIn.min = formatDate(today);

    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    checkOut.min = formatDate(tomorrow);

    if (checkinParam && checkoutParam) {
        checkIn.value = checkinParam;
        checkOut.value = checkoutParam;
    } else {
        checkIn.value = formatDate(today);
        checkOut.value = formatDate(tomorrow);
    }

    checkIn.addEventListener("change", function () {
        const selectedIn = new Date(this.value);
        const nextDay = new Date(selectedIn);
        nextDay.setDate(selectedIn.getDate() + 1);

        checkOut.min = formatDate(nextDay);

        if (new Date(checkOut.value) <= selectedIn) {
            checkOut.value = formatDate(nextDay);
        }
    });
});
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".book-now").forEach((btn) => {
        btn.dataset.locked = "true";
        btn.style.opacity = "0.5";
        btn.style.cursor = "not-allowed";

        btn.addEventListener("click", function (e) {
            if (btn.dataset.locked === "true") {
                e.preventDefault();
                alert("Silakan cek ketersediaan terlebih dahulu");
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("cekForm");
    const checkinInput = document.getElementById("checkin");
    const checkoutInput = document.getElementById("checkout");

    form.addEventListener("submit", async function (e) {
        e.preventDefault();

        const checkin = checkinInput.value;
        const checkout = checkoutInput.value;

        if (!checkin || !checkout) {
            alert("Harap pilih tanggal Check-in dan Check-out");
            return;
        }

        document.querySelectorAll(".availability-text").forEach((el) => {
            el.innerHTML = "<span style='color: gray;'>Memeriksa...</span>";
        });

        try {
            const response = await fetch(
                `${window.location.origin}/cek-kamar?checkin=${checkin}&checkout=${checkout}`
            );
            const data = await response.json();

            window.roomAvailability = data;

            for (const [roomName, sisa] of Object.entries(data)) {
                const el = document.querySelector(
                    `.availability-text[data-room="${roomName}"]`
                );
                if (!el) continue;

                const card = el.closest(".card-item");
                const bookBtn = card.querySelector(".book-now");

                if (sisa > 0) {
                    el.innerHTML = `<span style="color: green;">Tersisa ${sisa} kamar</span>`;
                    bookBtn.disabled = false;
                    bookBtn.dataset.locked = "false";
                    bookBtn.style.opacity = "1";
                    bookBtn.style.cursor = "pointer";
                } else {
                    el.innerHTML = `<span style="color: red;">Tidak tersedia</span>`;
                    bookBtn.disabled = true;
                    bookBtn.dataset.locked = "false";
                    bookBtn.style.opacity = "0.5";
                    bookBtn.style.cursor = "not-allowed";
                }
            }
        } catch (err) {
            console.error(err);
            alert("Gagal memuat data ketersediaan kamar!");
        }
    });
});
