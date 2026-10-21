{{-- Aplica el tema guardado antes de pintar para evitar el parpadeo --}}
<script>
    try {
        const theme = localStorage.getItem('theme');
        if (theme === 'dark' || theme === 'light') {
            document.documentElement.dataset.theme = theme;
        }
    } catch (e) {}
</script>
