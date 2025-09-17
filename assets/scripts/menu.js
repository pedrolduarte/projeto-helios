document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('mobile_btn');
    const menu = document.getElementById('mobile_menu');
    const icon = document.querySelector('i');
    if (btn && menu) {
        btn.addEventListener('click', function() {
            menu.classList.toggle('active');
            icon.classList.toggle('fa-x');
        });
    }
});

// Dropdown de idioma
document.querySelectorAll('.dropbtn').forEach(btn => {
    btn.onclick = function(e){
        e.stopPropagation();
        this.parentElement.classList.toggle('open');
    }
});
document.addEventListener('click', function(){
    document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('open'));
});

// Ativo nos menus
document.querySelectorAll('.navitem a').forEach(link => {
    link.onclick = function() {
        document.querySelectorAll('.navitem').forEach(i => i.classList.remove('active'));
        this.parentElement.classList.add('active');
    }
});