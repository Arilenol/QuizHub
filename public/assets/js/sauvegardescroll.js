window.addEventListener('beforeunload', () => {
    localStorage.setItem("scrollPosition", window.scrollY);
});

window.addEventListener('load', () => {
    const pos = localStorage.getItem("scrollPosition");
    if (pos !== null) {
        window.scrollTo(0, parseInt(pos));
    }
});