/* main.js */

// navbar
var hd=document.getElementById('hd'),bg=document.getElementById('burger');
function sc(){hd.classList.toggle('solid',scrollY>40)}sc();addEventListener('scroll',sc,{passive:true});
bg.onclick=function(){var o=hd.classList.toggle('open');bg.setAttribute('aria-expanded',o);bg.textContent=o?'✕':'☰'};
document.querySelectorAll('#m a').forEach(function(a){a.onclick=function(){hd.classList.remove('open');bg.setAttribute('aria-expanded','false');bg.textContent='☰'}});
var contactDialog=document.getElementById('contact-dialog');
if(contactDialog){
  document.querySelectorAll('[data-contact-trigger]').forEach(function(trigger){trigger.addEventListener('click',function(e){e.preventDefault();if(hd){hd.classList.remove('open');bg.setAttribute('aria-expanded','false');bg.textContent='☰'}contactDialog.showModal();contactDialog.querySelector('input[name="name"]').focus()})});
  contactDialog.querySelector('[data-contact-close]').addEventListener('click',function(){contactDialog.close()});
  contactDialog.addEventListener('click',function(e){if(e.target===contactDialog)contactDialog.close()});
}
// slider
var rg=document.getElementById('rg');
if(rg){
  var slider=document.getElementById('sl'),sliderPhoto=document.getElementById('s2'),sliderBar=document.getElementById('bar2');
  function updateSlider(){
    var value=Math.max(0,Math.min(100,Number(rg.value)||0));
    rg.value=value;
    sliderPhoto.style.clipPath='inset(0 0 0 '+value+'%)';
    sliderBar.style.left=value+'%';
    sliderBar.setAttribute('aria-valuenow',value);
  }
  slider.querySelectorAll('img').forEach(function(img){img.addEventListener('error',function(){img.closest('.slider-photo').classList.add('is-error')})});
  rg.addEventListener('input',updateSlider);
  rg.addEventListener('change',updateSlider);
  updateSlider();
}
// reveal
var io=new IntersectionObserver(function(e){e.forEach(function(x){if(x.isIntersecting){x.target.classList.add('in');io.unobserve(x.target)}})},{threshold:.15});
document.querySelectorAll('.rv').forEach(function(el){io.observe(el)});
var RM=matchMedia('(prefers-reduced-motion:reduce)').matches;
// timeline ngang
var timeline=document.getElementById('timeline'),timelineShell=timeline&&timeline.closest('.timeline-shell');
if(timeline&&timelineShell){
  var timelineItems=[].slice.call(timeline.querySelectorAll('li'));
  var timelineDots=timelineShell.querySelector('.timeline-dots');
  var timelineProgress=timelineShell.querySelector('.timeline-progress');
  var timelinePrev=timelineShell.querySelector('[data-timeline-prev]');
  var timelineNext=timelineShell.querySelector('[data-timeline-next]');
  var timelineControls=timelineShell.querySelector('.timeline-controls');
  var timelineNames=timelineItems.map(function(item){return item.querySelector('b').textContent.trim()});
  var timelineCurrent=0,timelineFrame=0,drag={active:false,startX:0,startScroll:0};
  timelineNames.forEach(function(name,i){
    var dot=document.createElement('button');
    dot.type='button'; dot.setAttribute('aria-label','Đến mốc: '+name);
    dot.onclick=function(){timelineItems[i].scrollIntoView({behavior:RM?'auto':'smooth',block:'nearest',inline:'start'})};
    timelineDots.appendChild(dot);
  });
  var timelineDotButtons=[].slice.call(timelineDots.children);
  function timelineIndex(){
    var best=0,bestDistance=Infinity;
    timelineItems.forEach(function(item,i){var distance=Math.abs(item.offsetLeft-timeline.scrollLeft);if(distance<bestDistance){bestDistance=distance;best=i}});
    return best;
  }
  function timelineUpdate(){
    timelineFrame=0;
    var max=timeline.scrollWidth-timeline.clientWidth, index=timelineIndex(), ratio=max>0?timeline.scrollLeft/max:0;
    timelineCurrent=index;
    timelineProgress.style.width=(ratio*100)+'%';
    timelineItems.forEach(function(item,i){item.classList.toggle('is-current',i===index);item.classList.toggle('is-past',i<index)});
    timelineDotButtons.forEach(function(dot,i){dot.classList.toggle('is-current',i===index);dot.classList.toggle('is-past',i<index);dot.setAttribute('aria-current',i===index?'step':'false')});
    timelinePrev.disabled=index===0; timelineNext.disabled=index===timelineItems.length-1;
    timeline.classList.toggle('is-start',timeline.scrollLeft<=1);timeline.classList.toggle('is-end',max<=1||timeline.scrollLeft>=max-1);timeline.classList.toggle('is-middle',timeline.scrollLeft>1&&timeline.scrollLeft<max-1);
  }
  function timelineSchedule(){if(!timelineFrame)timelineFrame=requestAnimationFrame(timelineUpdate)}
  function timelineGo(index){index=Math.max(0,Math.min(timelineItems.length-1,index));timelineItems[index].scrollIntoView({behavior:RM?'auto':'smooth',block:'nearest',inline:'start'})}
  timelinePrev.onclick=function(){timelineGo(timelineCurrent-1)}; timelineNext.onclick=function(){timelineGo(timelineCurrent+1)};
  timeline.addEventListener('scroll',timelineSchedule,{passive:true});
  timeline.addEventListener('keydown',function(e){if(e.key==='ArrowLeft'||e.key==='ArrowRight'){e.preventDefault();timelineGo(timelineCurrent+(e.key==='ArrowRight'?1:-1))}});
  timeline.addEventListener('pointerdown',function(e){if(e.pointerType!=='mouse'||timeline.scrollWidth<=timeline.clientWidth)return;drag={active:true,startX:e.clientX,startScroll:timeline.scrollLeft};timelineShell.classList.add('is-dragging');timeline.setPointerCapture(e.pointerId)});
  timeline.addEventListener('pointermove',function(e){if(!drag.active)return;timeline.scrollLeft=drag.startScroll-(e.clientX-drag.startX)});
  function stopTimelineDrag(e){if(!drag.active)return;drag.active=false;timelineShell.classList.remove('is-dragging');if(e&&timeline.hasPointerCapture(e.pointerId))timeline.releasePointerCapture(e.pointerId);timelineGo(timelineCurrent)}
  timeline.addEventListener('pointerup',stopTimelineDrag);timeline.addEventListener('pointercancel',stopTimelineDrag);
  function timelineVisibility(){timelineControls.hidden=timeline.scrollWidth<=timeline.clientWidth+1;timelineUpdate()}
  addEventListener('resize',timelineVisibility);timelineVisibility();
}
// thanh tiến độ cuộn
var pg=document.getElementById('pg');
function prog(){var h=document.documentElement.scrollHeight-innerHeight;pg.style.transform='scaleX('+(h>0?scrollY/h:0)+')'}prog();addEventListener('scroll',prog,{passive:true});
// stagger cho timeline và hành trình
document.querySelectorAll('.tl li,.ph li').forEach(function(li,i){li.classList.add('rv');li.style.transitionDelay=(i%5)*90+'ms';io.observe(li)});
// slider tự chạy một lần để gợi ý cách kéo
if(!RM&&document.getElementById('sl')&&rg){var sl=document.getElementById('sl'),touched=false;rg.addEventListener('pointerdown',function(){touched=true});
new IntersectionObserver(function(e,o){if(!e[0].isIntersecting)return;o.disconnect();var t0=performance.now(),D=2600;
(function step(t){if(touched)return;var k=Math.min((t-t0)/D,1),v=50+Math.sin(k*Math.PI*2)*38*(1-k*.3);rg.value=v;rg.dispatchEvent(new Event('input'));if(k<1)requestAnimationFrame(step);else{rg.value=50;rg.dispatchEvent(new Event('input'))}})(t0)},{threshold:.6}).observe(sl)}

;
/* library.js */
function initLibraries(scope){var cleanup=[];
(scope||document).querySelectorAll('[data-library]').forEach(function(library){
var phase='all',category='Tất cả',figures=Array.from(library.querySelectorAll('.gfig')),phaseButtons=Array.from(library.querySelectorAll('.phase-nav button')),categoryButtons=Array.from(library.querySelectorAll('.gal button')),dialog=library.querySelector('dialog'),lastFocus=null,active=null;
function apply(){var count=0;figures.forEach(function(f){f.hidden=!(phase==='all'||f.dataset.phase===phase)||!(category==='Tất cả'||f.dataset.category===category);if(!f.hidden)count++});phaseButtons.forEach(function(b){b.setAttribute('aria-pressed',b.dataset.phase===phase)});categoryButtons.forEach(function(b){b.setAttribute('aria-pressed',b.dataset.category===category)});library.querySelectorAll('[data-phase-copy]').forEach(function(c){c.hidden=c.dataset.phaseCopy!==phase});var h=library.querySelector('[data-library-history]');if(h)h.hidden=phase!=='all'&&phase!=='ph1';library.querySelector('.library-empty').hidden=count>0;}
function hashPhase(){var v=location.hash.slice(1);phase=phaseButtons.some(function(b){return b.dataset.phase===v})?v:'all';category='Tất cả';apply()}
phaseButtons.forEach(function(b){b.onclick=function(){phase=b.dataset.phase;category='Tất cả';history.replaceState(null,'',location.pathname+location.search+(phase==='all'?(document.getElementById('du-an')?'#du-an':'#library'):'#'+phase));apply()}});
categoryButtons.forEach(function(b){b.onclick=function(){category=b.dataset.category;apply()}});
function open(f){active=f;var source=f.querySelector('img'),img=document.createElement('img');img.src=f.dataset.large||source.src;img.alt=source.alt;dialog.querySelector('.lb').replaceChildren(img);dialog.querySelector('.lightbox-caption').textContent=f.querySelector('span').textContent;if(!dialog.open){lastFocus=f;dialog.showModal();dialog.querySelector('button').focus()}}
figures.forEach(function(f){f.onclick=function(){open(f)}});
dialog.addEventListener('keydown',function(e){if(e.key!=='ArrowLeft'&&e.key!=='ArrowRight')return;e.preventDefault();var visible=figures.filter(function(f){return !f.hidden}),i=visible.indexOf(active);if(visible.length)open(visible[(i+(e.key==='ArrowRight'?1:-1)+visible.length)%visible.length])});
dialog.addEventListener('click',function(e){if(e.target===dialog){var r=dialog.getBoundingClientRect();if(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom)dialog.close()}});
dialog.addEventListener('close',function(){if(lastFocus)lastFocus.focus()});addEventListener('hashchange',hashPhase);cleanup.push(function(){removeEventListener('hashchange',hashPhase)});hashPhase();
});

return function(){cleanup.forEach(function(fn){fn()})};}
window.initLibraries=initLibraries;initLibraries(document);

;
/* projects.js */
(function(){
function initTimeline(root){
var timeline=root.querySelector('#timeline'),timelineShell=timeline&&timeline.closest('.timeline-shell');
if(timeline&&timelineShell){var RM=matchMedia('(prefers-reduced-motion:reduce)').matches;
  var timelineItems=[].slice.call(timeline.querySelectorAll('li'));
  var timelineDots=timelineShell.querySelector('.timeline-dots');
  var timelineProgress=timelineShell.querySelector('.timeline-progress');
  var timelinePrev=timelineShell.querySelector('[data-timeline-prev]');
  var timelineNext=timelineShell.querySelector('[data-timeline-next]');
  var timelineControls=timelineShell.querySelector('.timeline-controls');
  var timelineNames=timelineItems.map(function(item){return item.querySelector('b').textContent.trim()});
  var timelineCurrent=0,timelineFrame=0,drag={active:false,startX:0,startScroll:0};
  timelineNames.forEach(function(name,i){
    var dot=document.createElement('button');
    dot.type='button'; dot.setAttribute('aria-label','Đến mốc: '+name);
    dot.onclick=function(){timelineItems[i].scrollIntoView({behavior:RM?'auto':'smooth',block:'nearest',inline:'start'})};
    timelineDots.appendChild(dot);
  });
  var timelineDotButtons=[].slice.call(timelineDots.children);
  function timelineIndex(){
    var best=0,bestDistance=Infinity;
    timelineItems.forEach(function(item,i){var distance=Math.abs(item.offsetLeft-timeline.scrollLeft);if(distance<bestDistance){bestDistance=distance;best=i}});
    return best;
  }
  function timelineUpdate(){
    timelineFrame=0;
    var max=timeline.scrollWidth-timeline.clientWidth, index=timelineIndex(), ratio=max>0?timeline.scrollLeft/max:0;
    timelineCurrent=index;
    timelineProgress.style.width=(ratio*100)+'%';
    timelineItems.forEach(function(item,i){item.classList.toggle('is-current',i===index);item.classList.toggle('is-past',i<index)});
    timelineDotButtons.forEach(function(dot,i){dot.classList.toggle('is-current',i===index);dot.classList.toggle('is-past',i<index);dot.setAttribute('aria-current',i===index?'step':'false')});
    timelinePrev.disabled=index===0; timelineNext.disabled=index===timelineItems.length-1;
    timeline.classList.toggle('is-start',timeline.scrollLeft<=1);timeline.classList.toggle('is-end',max<=1||timeline.scrollLeft>=max-1);timeline.classList.toggle('is-middle',timeline.scrollLeft>1&&timeline.scrollLeft<max-1);
  }
  function timelineSchedule(){if(!timelineFrame)timelineFrame=requestAnimationFrame(timelineUpdate)}
  function timelineGo(index){index=Math.max(0,Math.min(timelineItems.length-1,index));timelineItems[index].scrollIntoView({behavior:RM?'auto':'smooth',block:'nearest',inline:'start'})}
  timelinePrev.onclick=function(){timelineGo(timelineCurrent-1)}; timelineNext.onclick=function(){timelineGo(timelineCurrent+1)};
  timeline.addEventListener('scroll',timelineSchedule,{passive:true});
  timeline.addEventListener('keydown',function(e){if(e.key==='ArrowLeft'||e.key==='ArrowRight'){e.preventDefault();timelineGo(timelineCurrent+(e.key==='ArrowRight'?1:-1))}});
  timeline.addEventListener('pointerdown',function(e){if(e.pointerType!=='mouse'||timeline.scrollWidth<=timeline.clientWidth)return;drag={active:true,startX:e.clientX,startScroll:timeline.scrollLeft};timelineShell.classList.add('is-dragging');timeline.setPointerCapture(e.pointerId)});
  timeline.addEventListener('pointermove',function(e){if(!drag.active)return;timeline.scrollLeft=drag.startScroll-(e.clientX-drag.startX)});
  function stopTimelineDrag(e){if(!drag.active)return;drag.active=false;timelineShell.classList.remove('is-dragging');if(e&&timeline.hasPointerCapture(e.pointerId))timeline.releasePointerCapture(e.pointerId);timelineGo(timelineCurrent)}
  timeline.addEventListener('pointerup',stopTimelineDrag);timeline.addEventListener('pointercancel',stopTimelineDrag);
  function timelineVisibility(){timelineControls.hidden=timeline.scrollWidth<=timeline.clientWidth+1;timelineUpdate()}
  addEventListener('resize',timelineVisibility);timelineVisibility();return function(){removeEventListener('resize',timelineVisibility)};
}

return function(){};
}

const dialog=document.querySelector('.project-dialog');if(!dialog)return;
const content=dialog.querySelector('.project-dialog-content'),close=dialog.querySelector('.project-dialog-close');let abort=null,dispose=null,focus=null;
document.querySelectorAll('[data-project-open]').forEach(link=>link.addEventListener('click',async event=>{
 if(event.ctrlKey||event.metaKey||event.shiftKey||event.altKey)return;
 event.preventDefault();focus=link;if(abort)abort.abort();abort=new AbortController();if(dispose){dispose();dispose=null;}
 content.innerHTML='<p class="note" role="status">Đang mở dự án…</p>';dialog.showModal();close.focus();
 try{const url=new URL(link.href);url.searchParams.set('fragment','1');const response=await fetch(url,{signal:abort.signal});if(!response.ok)throw Error('Không thể mở dự án.');content.innerHTML=await response.text();const libraryDispose=window.initLibraries?window.initLibraries(content):function(){};const timelineDispose=initTimeline(content);dispose=function(){libraryDispose();timelineDispose()};dialog.scrollTop=0;}
 catch(error){if(error.name==='AbortError')return;const p=document.createElement('p');p.className='note';p.textContent='Chưa tải được nội dung. ';const fallback=document.createElement('a');fallback.href=link.href;fallback.textContent='Mở trang dự án';p.append(fallback);content.replaceChildren(p);}
}));
close.addEventListener('click',()=>dialog.close());dialog.addEventListener('close',()=>{if(abort)abort.abort();if(dispose){dispose();dispose=null;}if(focus)focus.focus();});
dialog.addEventListener('click',e=>{if(e.target===dialog){const r=dialog.getBoundingClientRect();if(e.clientX<r.left||e.clientX>r.right||e.clientY<r.top||e.clientY>r.bottom)dialog.close();}});
}());

