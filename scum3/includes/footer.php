<script>
  (function(){
    const burger = document.getElementById('navBurger');
    const drawer = document.getElementById('navDrawer');
    const closeBtn = document.getElementById('navClose');

    if(!burger || !drawer) return;

    function openDrawer(){
      drawer.classList.add('open');
      drawer.setAttribute('aria-hidden','false');
      burger.setAttribute('aria-expanded','true');
      document.body.style.overflow = 'hidden';
    }

    function closeDrawer(){
      drawer.classList.remove('open');
      drawer.setAttribute('aria-hidden','true');
      burger.setAttribute('aria-expanded','false');
      document.body.style.overflow = '';
    }

    burger.addEventListener('click', openDrawer);
    closeBtn?.addEventListener('click', closeDrawer);

    drawer.addEventListener('click', (e) => {
      if(e.target === drawer) closeDrawer(); // click auf Overlay schließt
    });

    window.addEventListener('keydown', (e) => {
      if(e.key === 'Escape') closeDrawer();
    });

    // Klick auf Link im Drawer => schließen (fühlt sich mobil richtig an)
    drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', closeDrawer));
  })();
</script>

</body>
</html>
