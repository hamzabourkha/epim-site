window.addEventListener('load',()=>document.getElementById('loader')?.classList.add('hide'));
document.querySelectorAll('.reveal').forEach(el=>new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting)e.target.classList.add('show')}),{threshold:.12}).observe(el));
document.getElementById('darkToggle')?.addEventListener('click',()=>{document.body.classList.toggle('dark');localStorage.setItem('epim-dark',document.body.classList.contains('dark')?'1':'0')});
if(localStorage.getItem('epim-dark')==='1')document.body.classList.add('dark');
document.querySelector('#cookie button')?.addEventListener('click',()=>{localStorage.setItem('epim-cookie','1');document.getElementById('cookie')?.classList.add('hide')});
if(localStorage.getItem('epim-cookie')==='1')document.getElementById('cookie')?.classList.add('hide');
document.getElementById('contactForm')?.addEventListener('submit',async e=>{e.preventDefault();const form=e.currentTarget;const res=await fetch(form.action,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'},body:new FormData(form)});if(res.ok){form.reset();alert('Message envoye avec succes.')}else{alert('Merci de verifier les champs.')}});
