function filterSelection(category, btnElement) {
    btnElement.classList.toggle('active');

    const activeTags = document.querySelectorAll('.btn.active');
    const activeTagNames = Array.from(activeTags).map(btn => btn.textContent.trim().toLowerCase());

    const threads = document.querySelectorAll('.thread');
    threads.forEach(thread => {
        const tags = thread.getAttribute('data-tags').toLowerCase().split(' ');

        if (activeTagNames.length === 0 || tags.some(tag => activeTagNames.includes(tag))) {
            thread.style.display = 'block';
        } else {
            thread.style.display = 'none';
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    var toggleButton = document.getElementById("toggleSidebar");
    var sidebar = document.getElementsByClassName("sidebar")[0];
    var mainBody = document.getElementsByClassName("main-body")[0];

    toggleButton.addEventListener("click", function() {
        if (sidebar.style.width === "200px" || sidebar.style.width === "") {
            sidebar.style.width = "0";
            mainBody.style.left = "0";
        } else {
            sidebar.style.width = "200px";
            mainBody.style.left = "250px";
        }
    });
});



