'use strict';
document.addEventListener('DOMContentLoaded',()=>{
 const button=document.querySelector('.menu-toggle');
 const nav=document.querySelector('#primary-nav');
 if(button&&nav){
  button.addEventListener('click',()=>{
   const open=button.getAttribute('aria-expanded')==='true';
   button.setAttribute('aria-expanded',String(!open));
   nav.classList.toggle('is-open',!open);
  });
 }
 document.querySelectorAll('[data-copy-otp]').forEach((copyButton)=>{
  copyButton.addEventListener('click',async()=>{
   const code=copyButton.getAttribute('data-copy-otp')||'';
   try{
    if(navigator.clipboard&&window.isSecureContext){
     await navigator.clipboard.writeText(code);
    }else{
     const field=document.createElement('textarea');
     field.value=code;field.setAttribute('readonly','');
     field.style.position='fixed';field.style.opacity='0';
     document.body.appendChild(field);field.select();
     const copied=document.execCommand('copy');field.remove();
     if(!copied)throw new Error('Copy unavailable');
    }
    copyButton.textContent='Copied';
   }catch(error){
    copyButton.textContent='Copy manually';
   }
  });
 });
});
