const loader = document.querySelector('.page-loader');
window.addEventListener('load', () => {
  setTimeout(() => {
    loader.style.opacity = '0';
    loader.style.visibility = 'hidden';
  }, 450);
});

const cursorDot = document.querySelector('.cursor-dot');
const cursorRing = document.querySelector('.cursor-ring');
if (cursorDot && cursorRing && matchMedia('(pointer:fine)').matches) {
  let mx = innerWidth / 2, my = innerHeight / 2, rx = mx, ry = my;
  addEventListener('mousemove', e => { mx = e.clientX; my = e.clientY; cursorDot.style.left = mx + 'px'; cursorDot.style.top = my + 'px'; });
  function cursorLoop() {
    rx += (mx-rx)*.14; ry += (my-ry)*.14;
    cursorRing.style.left = rx + 'px'; cursorRing.style.top = ry + 'px';
    requestAnimationFrame(cursorLoop);
  }
  cursorLoop();
  document.querySelectorAll('a,button,.screen,.skill-wall span').forEach(el => {
    el.addEventListener('mouseenter', () => cursorRing.classList.add('hover'));
    el.addEventListener('mouseleave', () => cursorRing.classList.remove('hover'));
  });
}

const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      observer.unobserve(entry.target);
    }
  });
}, {threshold:.12});
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

const progress = document.querySelector('.scroll-progress');
const navLinks = [...document.querySelectorAll('.nav-links a')];
const sections = [...document.querySelectorAll('main section[id]')];

function onScroll() {
  const max = document.documentElement.scrollHeight - innerHeight;
  progress.style.transform = `scaleX(${max ? scrollY/max : 0})`;

  sections.forEach(section => {
    const r = section.getBoundingClientRect();
    if (r.top <= innerHeight * .35 && r.bottom >= innerHeight * .35) {
      navLinks.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + section.id));
    }
  });

  document.querySelectorAll('[data-parallax]').forEach(el => {
    const r = el.getBoundingClientRect();
    if (r.bottom > 0 && r.top < innerHeight) {
      const shift = (innerHeight/2 - (r.top + r.height/2)) * .045;
      el.style.translate = `0 ${shift}px`;
    }
  });
}
addEventListener('scroll', onScroll, {passive:true});
onScroll();

document.querySelectorAll('.magnetic').forEach(el => {
  if (!matchMedia('(pointer:fine)').matches) return;
  el.addEventListener('mousemove', e => {
    const r = el.getBoundingClientRect();
    const x = (e.clientX - (r.left+r.width/2)) * .12;
    const y = (e.clientY - (r.top+r.height/2)) * .12;
    el.style.transform = `translate(${x}px,${y}px)`;
  });
  el.addEventListener('mouseleave', () => el.style.transform = '');
});

const menuToggle = document.querySelector('.menu-toggle');
const mobileMenu = document.querySelector('.mobile-menu');
menuToggle?.addEventListener('click', () => mobileMenu.classList.toggle('open'));
mobileMenu?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => mobileMenu.classList.remove('open')));
