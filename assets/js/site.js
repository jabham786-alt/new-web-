'use strict';
document.addEventListener('DOMContentLoaded',()=>{
 const button=document.querySelector('.menu-toggle'),nav=document.querySelector('#primary-nav');
 if(button&&nav)button.addEventListener('click',()=>{const open=button.getAttribute('aria-expanded')==='true';button.setAttribute('aria-expanded',String(!open));nav.classList.toggle('is-open',!open);});
 document.querySelectorAll('[data-copy-otp]').forEach(copyButton=>copyButton.addEventListener('click',async()=>{
  const code=copyButton.getAttribute('data-copy-otp')||'';
  try{if(navigator.clipboard&&window.isSecureContext)await navigator.clipboard.writeText(code);else{const field=document.createElement('textarea');field.value=code;field.setAttribute('readonly','');field.style.position='fixed';field.style.opacity='0';document.body.appendChild(field);field.select();const copied=document.execCommand('copy');field.remove();if(!copied)throw new Error('Copy unavailable');}copyButton.textContent='Copied';}catch(error){copyButton.textContent='Copy manually';}
 }));
 const loader=document.querySelector('#hg-page-loader');let shown=false;
 const showLoader=()=>{if(!loader||shown)return;shown=true;loader.hidden=false;loader.setAttribute('aria-hidden','false');document.documentElement.classList.add('hg-loading');};
 document.querySelectorAll('form:not([data-no-loader])').forEach(form=>form.addEventListener('submit',()=>{if(form.checkValidity())showLoader();}));
 document.querySelectorAll('a[href]').forEach(link=>link.addEventListener('click',event=>{
  if(event.defaultPrevented||event.button!==0||event.metaKey||event.ctrlKey||event.shiftKey||event.altKey)return;
  const href=link.getAttribute('href')||'';if(!href||href.startsWith('#')||/^(mailto:|tel:|javascript:)/i.test(href)||link.target==='_blank'||link.hasAttribute('download'))return;
  try{if(new URL(href,location.href).origin!==location.origin)return;}catch(e){return;}showLoader();
 }));
 window.addEventListener('pageshow',()=>{shown=false;if(loader){loader.hidden=true;loader.setAttribute('aria-hidden','true');document.documentElement.classList.remove('hg-loading');}});
});