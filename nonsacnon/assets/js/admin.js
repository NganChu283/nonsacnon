/* admin-menu.js */
(() => {
  const layout = document.querySelector('.adm');
  const sidebar = document.getElementById('admin-sidebar');
  const buttons = [...document.querySelectorAll('[data-admin-menu-toggle]')];
  if (!layout || !sidebar || !buttons.length) return;
  const desktop = matchMedia('(min-width: 900px)');
  let preference = null;
  try { preference = localStorage.getItem('admin-menu-visible'); } catch (_) {}
  const apply = visible => {
    sidebar.hidden = !visible;
    layout.classList.toggle('is-menu-hidden', !visible);
    buttons.forEach(button => {
      button.setAttribute('aria-expanded', String(visible));
      const label = visible ? 'Ẩn menu' : 'Hiện menu';
      button.setAttribute('aria-label', label);
      button.title = label;
    });
  };
  apply(desktop.matches && preference !== 'false');
  buttons.forEach(button => button.addEventListener('click', () => {
    const visible = sidebar.hidden;
    if (!visible && sidebar.contains(document.activeElement)) buttons.find(b => !sidebar.contains(b))?.focus();
    apply(visible);
    if (desktop.matches) {
      preference = String(visible);
      try { localStorage.setItem('admin-menu-visible', preference); } catch (_) {}
    }
  }));
  desktop.addEventListener('change', () => apply(desktop.matches && preference !== 'false'));
})();

;
/* admin-design.js */
(() => {
  document.querySelectorAll('#admin-content table').forEach(table => {
    if (table.closest('.gallery-table,.admin-table-scroll')) return;
    const wrap = document.createElement('div');
    wrap.className = 'admin-table-scroll';
    wrap.setAttribute('role', 'region');
    wrap.setAttribute('aria-label', 'Bảng dữ liệu, cuộn ngang để xem đầy đủ');
    wrap.tabIndex = 0;
    table.before(wrap);
    wrap.append(table);
  });
  const input = document.getElementById('activity-search');
  const feed = document.getElementById('activity-feed');
  if (!input || !feed) return;
  const normalize = text => text.toLocaleLowerCase('vi').normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');
  const rows = [...feed.querySelectorAll('[data-activity-row]')];
  const filter = () => {
    const term = normalize(input.value.trim());
    let count = 0;
    rows.forEach(row => {
      row.hidden = !normalize(row.textContent).includes(term);
      if (!row.hidden) count++;
    });
    document.getElementById('activity-empty').hidden = count > 0;
    document.getElementById('activity-result').textContent = `${count} hoạt động phù hợp`;
  };
  input.addEventListener('input', filter);
  filter();
})();

;
/* editor.js */
(function(){
  var editor=document.getElementById('storyBody'),source=document.getElementById('bodySource'),form=editor&&editor.closest('form');
  if(!editor||!source||!form)return;
  var range=null;
  function sync(){source.value=editor.innerHTML.trim();}
  function remember(){var s=getSelection();if(s.rangeCount&&editor.contains(s.anchorNode)&&editor.contains(s.focusNode))range=s.getRangeAt(0).cloneRange();}
  function restore(){editor.focus();if(range){var s=getSelection();s.removeAllRanges();s.addRange(range);}}
  editor.addEventListener('keyup',remember);editor.addEventListener('mouseup',remember);
  document.addEventListener('selectionchange',remember);
  document.querySelectorAll('.editor-toolbar [data-command]').forEach(function(control){
    if(control.tagName==='BUTTON')control.addEventListener('mousedown',function(e){e.preventDefault();});
    control.addEventListener(control.tagName==='SELECT'?'change':'click',function(){
      remember();
      var command=control.dataset.command;
      if(command==='insertImage'){
        var input=document.createElement('input'); input.type='file'; input.accept='image/jpeg,image/png,image/webp';
        input.onchange=function(){if(!input.files[0])return;var data=new FormData();data.append('image',input.files[0]);data.append('t',window.editorCsrf);
          fetch('upload_image.php',{method:'POST',body:data}).then(function(r){return r.json().then(function(x){if(!r.ok)throw new Error(x.error||'Không thể tải ảnh.');return x;});}).then(function(x){restore();document.execCommand('insertImage',false,x.url);sync();remember();}).catch(function(err){alert(err.message);});};
        input.click(); return;
      }
      restore();
      if(command==='createLink'){var url=prompt('Nhập URL liên kết:','https://');if(url&&/^(https?:\/\/|mailto:)/i.test(url))document.execCommand(command,false,url);}
      else document.execCommand(command,false,control.tagName==='SELECT'?control.value:null);
      sync();remember();
    });
  });
  editor.addEventListener('input',function(){sync();remember();}); form.addEventListener('submit',sync);
}());

;
/* phase-editor.js */
(function(){
  var editor=document.getElementById('phaseBody'),source=document.getElementById('phaseSource');
  if(!editor||!source)return;
  var range=null;
  function sync(){source.value=editor.innerHTML.trim();}
  function remember(){var s=getSelection();if(s.rangeCount&&editor.contains(s.anchorNode))range=s.getRangeAt(0).cloneRange();}
  function restore(){editor.focus();if(range){var s=getSelection();s.removeAllRanges();s.addRange(range);}}
  editor.addEventListener('keyup',remember);editor.addEventListener('mouseup',remember);editor.addEventListener('input',function(){sync();remember();});
  document.querySelectorAll('.editor-toolbar [data-command]').forEach(function(control){
    if(control.tagName==='BUTTON')control.addEventListener('mousedown',function(e){e.preventDefault();});
    control.addEventListener(control.tagName==='SELECT'?'change':'click',function(){
      var command=control.dataset.command;remember();
      if(command==='insertImage'){
        var picker=document.createElement('input');picker.type='file';picker.accept='image/jpeg,image/png,image/webp';
        picker.onchange=async function(){if(!picker.files[0])return;control.disabled=true;
          try{var data=new FormData();data.append('image',picker.files[0]);data.append('t',window.editorCsrf);data.append('context','project');
            var res=await fetch('upload_image.php',{method:'POST',body:data}),body=await res.json();if(!res.ok)throw Error(body.error||'Không thể tải ảnh.');
            restore();document.execCommand('insertImage',false,body.url);sync();remember();
          }catch(err){alert(err.message);}finally{control.disabled=false;}};
        picker.click();return;
      }
      restore();
      if(command==='createLink'){var url=prompt('Nhập URL liên kết:','https://');if(url&&/^(https?:\/\/|mailto:)/i.test(url))document.execCommand(command,false,url);}
      else document.execCommand(command,false,control.tagName==='SELECT'?control.value:null);
      sync();remember();
    });
  });
  editor.closest('form').addEventListener('submit',sync);
}());

;
/* gallery-projects.js */
(function(){const form=document.querySelector('form[enctype]');if(!form)return;const project=form.elements.project_id,phase=form.elements.phase_id;if(!project||!phase)return;function update(){let current=false;Array.from(phase.options).forEach(o=>{o.hidden=o.disabled=o.dataset.project!==project.value;if(!o.disabled&&o.selected)current=true;});if(!current){phase.value=Array.from(phase.options).find(o=>!o.disabled)?.value||'';}form.querySelector('button[type=submit]').disabled=!phase.value;}project.addEventListener('change',update);update();}());

;
/* admin-workshops.js */
(() => {
  const button = document.querySelector('[data-workshop-toggle]');
  const form = document.getElementById('new-workshop-form');
  if (!button || !form) return;
  const show = visible => {
    form.hidden = !visible;
    button.setAttribute('aria-expanded', String(visible));
    if (visible) {
      form.scrollIntoView({ block: 'start', behavior: 'instant' });
      form.querySelector('[name="title"]').focus({ preventScroll: true });
    } else button.focus();
  };
  button.addEventListener('click', () => show(form.hidden));
  form.querySelector('[data-workshop-cancel]').addEventListener('click', () => show(false));
})();

;
/* project-delete.js */
document.querySelectorAll('[data-project-delete]').forEach(form => {
  form.addEventListener('submit', event => {
    const message = `Xóa dự án “${form.dataset.projectTitle}” cùng tất cả giai đoạn và ảnh trong thư viện của dự án? Thao tác này không thể hoàn tác.`;
    if (!confirm(message)) event.preventDefault();
  });
});

document.querySelectorAll('[data-project-item]').forEach(item => {
  let start = null;
  let swipeTime = 0;
  item.addEventListener('touchstart', event => {
    const touch = event.touches[0];
    start = event.touches.length === 1 ? { x: touch.clientX, y: touch.clientY } : null;
  }, { passive: true });
  item.addEventListener('touchend', event => {
    if (!start) return;
    const touch = event.changedTouches[0];
    const dx = touch.clientX - start.x;
    const dy = touch.clientY - start.y;
    start = null;
    if (Math.abs(dx) < 45 || Math.abs(dy) > 35) return;
    swipeTime = Date.now();
    document.querySelectorAll('[data-project-item]').forEach(other => other.classList.remove('is-delete-visible'));
    item.classList.toggle('is-delete-visible', dx > 0);
  }, { passive: true });
  item.addEventListener('touchcancel', () => { start = null; });
  item.addEventListener('click', event => {
    if (Date.now() - swipeTime < 400 && event.target.closest('a')) event.preventDefault();
  });
});

