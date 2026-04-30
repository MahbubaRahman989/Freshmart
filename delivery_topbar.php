<!-- delivery_topbar.php  (include this on delivery pages after delivery_auth.php) -->
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$dp_id = (int)($_SESSION['delivery_person_id'] ?? 0);
?>
<style>
.dp-topbar{position:sticky;top:0;z-index:999;background:#ffffffcc;backdrop-filter:blur(10px);border-bottom:1px solid #e5e7eb}
.dp-topbar .wrap{max-width:1200px;margin:0 auto;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.brand{font-weight:800;color:#111827}
.notibell{position:relative;display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;border:1px solid #e5e7eb;background:#fff;cursor:pointer}
.badge{position:absolute;top:-6px;right:-6px;min-width:20px;height:20px;padding:0 6px;border-radius:999px;background:#ef4444;color:#fff;font-weight:800;font-size:12px;display:flex;align-items:center;justify-content:center;border:2px solid #fff}
.dropdown{position:absolute;right:0;top:56px;width:420px;max-width:92vw;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 18px 50px rgba(0,0,0,.12);display:none;overflow:hidden}
.dropdown.open{display:block}
.dd-head{padding:12px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
.dd-head strong{font-size:14px;color:#111827}
.dd-body{max-height:420px;overflow:auto}
.notif{padding:12px 14px;border-bottom:1px solid #f1f5f9;display:flex;gap:10px;align-items:flex-start}
.dot{width:10px;height:10px;border-radius:50%;margin-top:6px;background:#cbd5e1}
.notif.unread .dot{background:#16a34a;box-shadow:0 0 0 3px rgba(22,163,74,.15)}
.msg{flex:1}
.msg .t{font-size:13px;color:#111827;font-weight:650;line-height:1.35}
.msg .meta{margin-top:6px;font-size:12px;color:#64748b;display:flex;gap:10px;flex-wrap:wrap}
.btn{border:none;border-radius:10px;padding:8px 10px;font-weight:800;font-size:12px;cursor:pointer;background:#e5e7eb}
.empty{padding:26px;color:#64748b;text-align:center}
</style>

<div class="dp-topbar">
  <div class="wrap">
    <div class="brand">Delivery Dashboard</div>

    <div style="position:relative">
      <button class="notibell" id="dpBell" type="button">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <path d="M12 22a2.5 2.5 0 0 0 2.5-2.5h-5A2.5 2.5 0 0 0 12 22Z" fill="#111827"/>
          <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="#111827" stroke-width="2" stroke-linejoin="round"/>
        </svg>
        <span class="badge" id="dpNotiCount" style="display:none">0</span>
      </button>

      <div class="dropdown" id="dpDrop">
        <div class="dd-head">
          <strong>Notifications</strong>
          <button class="btn" type="button" id="dpRefresh">Refresh</button>
        </div>
        <div class="dd-body" id="dpNotiList"><div class="empty">Loading...</div></div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const bell = document.getElementById('dpBell');
  const drop = document.getElementById('dpDrop');
  const countEl = document.getElementById('dpNotiCount');
  const listEl = document.getElementById('dpNotiList');
  const refreshBtn = document.getElementById('dpRefresh');

  function esc(s){return (s||'').toString().replace(/[&<>"']/g,m=>({ "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;" }[m]));}

  function toggleDrop(){ drop.classList.toggle('open'); if(drop.classList.contains('open')) loadList(); }
  bell.addEventListener('click', toggleDrop);
  refreshBtn.addEventListener('click', loadList);

  document.addEventListener('click', (e)=>{
    if(bell.contains(e.target)) return;
    if(drop.contains(e.target)) return;
    drop.classList.remove('open');
  });

  async function loadCount(){
    try{
      const r = await fetch('delivery_notifications_count.php');
      const n = parseInt((await r.text()).trim(),10) || 0;
      if(n>0){ countEl.style.display='flex'; countEl.textContent = n>99?'99+':n; }
      else countEl.style.display='none';
    }catch(e){}
  }

  async function loadList(){
    listEl.innerHTML = '<div class="empty">Loading...</div>';
    try{
      const r = await fetch('delivery_notifications_list.php');
      const data = await r.json();
      if(!data || !data.items || data.items.length===0){
        listEl.innerHTML = '<div class="empty">No notifications</div>';
        return;
      }
      let html = '';
      data.items.forEach(it=>{
        const unread = (parseInt(it.is_read,10)===0);
        html += `
          <div class="notif ${unread?'unread':''}">
            <div class="dot"></div>
            <div class="msg">
              <div class="t">${esc(it.message)}</div>
              <div class="meta">
                <span>#Order: ${esc(it.order_id)}</span>
                <span>${esc(it.created_at)}</span>
              </div>
              ${unread?`<button class="btn" data-read="${it.id}">Mark Read</button>`:''}
            </div>
          </div>
        `;
      });
      listEl.innerHTML = html;

      listEl.querySelectorAll('[data-read]').forEach(btn=>{
        btn.addEventListener('click', async ()=>{
          const id = btn.getAttribute('data-read');
          await fetch('delivery_mark_notification_read.php', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({id}).toString()
          });
          loadList(); loadCount();
        });
      });

    }catch(e){
      listEl.innerHTML = '<div class="empty">Failed to load</div>';
    }
  }

  loadCount();
  setInterval(loadCount, 4000); // real-time polling
})();
</script>
?>