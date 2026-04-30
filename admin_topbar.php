
<?php
if (session_status() === PHP_SESSION_NONE) session_start();

/* change if your admin user id different */
$ADMIN_USER_ID = 1;
?>
<style>
.admin-topbar{position:sticky;top:0;z-index:999;background:#ffffffcc;backdrop-filter:blur(10px);border-bottom:1px solid #e5e7eb}
.admin-topbar .wrap{max-width:1200px;margin:0 auto;padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:12px}
.brand{font-weight:800;color:#111827;letter-spacing:.2px}
.notibell{position:relative;display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;border:1px solid #e5e7eb;background:#fff;cursor:pointer}
.notibell:hover{box-shadow:0 8px 18px rgba(0,0,0,.08)}
.badge{position:absolute;top:-6px;right:-6px;min-width:20px;height:20px;padding:0 6px;border-radius:999px;background:#ef4444;color:#fff;font-weight:800;font-size:12px;display:flex;align-items:center;justify-content:center;border:2px solid #fff}
.dropdown{position:absolute;right:0;top:56px;width:420px;max-width:92vw;background:#fff;border:1px solid #e5e7eb;border-radius:16px;box-shadow:0 18px 50px rgba(0,0,0,.12);display:none;overflow:hidden}
.dropdown.open{display:block}
.dd-head{padding:12px 14px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between}
.dd-head strong{font-size:14px;color:#111827}
.dd-head button{border:none;background:#111827;color:#fff;border-radius:10px;padding:8px 10px;font-weight:700;cursor:pointer}
.dd-body{max-height:420px;overflow:auto}
.notif{padding:12px 14px;border-bottom:1px solid #f1f5f9;display:flex;gap:10px;align-items:flex-start}
.dot{width:10px;height:10px;border-radius:50%;margin-top:6px;background:#cbd5e1}
.notif.unread .dot{background:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.15)}
.msg{flex:1}
.msg .t{font-size:13px;color:#111827;font-weight:650;line-height:1.35}
.msg .meta{margin-top:6px;font-size:12px;color:#64748b;display:flex;gap:10px;flex-wrap:wrap}
.actions{margin-top:8px;display:flex;gap:8px;flex-wrap:wrap}
.btn{border:none;border-radius:10px;padding:8px 10px;font-weight:800;font-size:12px;cursor:pointer}
.btn-read{background:#e5e7eb;color:#111827}
.btn-cod{background:linear-gradient(135deg,#06b6d4,#0ea5e9);color:#fff}
.btn-view{background:#111827;color:#fff}
.empty{padding:26px;color:#64748b;text-align:center}
</style>

<div class="admin-topbar">
  <div class="wrap">
    <div class="brand">Admin Panel</div>

    <div style="position:relative">
      <button class="notibell" id="adminBell" type="button" aria-label="Notifications">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
          <path d="M12 22a2.5 2.5 0 0 0 2.5-2.5h-5A2.5 2.5 0 0 0 12 22Z" fill="#111827"/>
          <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9Z" stroke="#111827" stroke-width="2" stroke-linejoin="round"/>
        </svg>
        <span class="badge" id="adminNotiCount" style="display:none">0</span>
      </button>

      <div class="dropdown" id="adminDrop">
        <div class="dd-head">
          <strong>Notifications</strong>
          <button type="button" id="adminRefreshBtn">Refresh</button>
        </div>
        <div class="dd-body" id="adminNotiList">
          <div class="empty">Loading...</div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  const ADMIN_USER_ID = <?= (int)$ADMIN_USER_ID ?>;
  const bell = document.getElementById('adminBell');
  const drop = document.getElementById('adminDrop');
  const countEl = document.getElementById('adminNotiCount');
  const listEl = document.getElementById('adminNotiList');
  const refreshBtn = document.getElementById('adminRefreshBtn');

  function esc(s){return (s||'').toString().replace(/[&<>"']/g,m=>({ "&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#039;" }[m]));}

  function toggleDrop(){
    drop.classList.toggle('open');
    if(drop.classList.contains('open')) loadList();
  }

  document.addEventListener('click', (e)=>{
    if(bell.contains(e.target)) return;
    if(drop.contains(e.target)) return;
    drop.classList.remove('open');
  });

  bell.addEventListener('click', toggleDrop);
  refreshBtn.addEventListener('click', loadList);

  async function loadCount(){
    try{
      const r = await fetch('admin_notifications_count.php');
      const t = (await r.text()).trim();
      const n = parseInt(t,10) || 0;
      if(n>0){ countEl.style.display='flex'; countEl.textContent = n>99 ? '99+' : n; }
      else{ countEl.style.display='none'; }
    }catch(e){}
  }

  async function loadList(){
    listEl.innerHTML = '<div class="empty">Loading...</div>';
    try{
      const r = await fetch('admin_notifications_list.php');
      const data = await r.json();
      if(!data || !data.items || data.items.length===0){
        listEl.innerHTML = '<div class="empty">No notifications</div>';
        return;
      }

      let html = '';
      data.items.forEach(it=>{
        const unread = (parseInt(it.is_read,10)===0);
        const canCOD = (it.payment_method==='COD' && it.payment_status!=='Paid');
        html += `
          <div class="notif ${unread?'unread':''}">
            <div class="dot"></div>
            <div class="msg">
              <div class="t">${esc(it.message)}</div>
              <div class="meta">
                <span>#Order: ${esc(it.order_id)}</span>
                <span>${esc(it.created_at)}</span>
                <span>Pay: ${esc(it.payment_method||'')}</span>
                <span>Status: ${esc(it.payment_status||'')}</span>
              </div>
              <div class="actions">
                ${unread?`<button class="btn btn-read" data-read="${it.id}">Mark Read</button>`:''}
                <button class="btn btn-view" data-view="${it.order_id}">View Order</button>
                ${canCOD?`<button class="btn btn-cod" data-cod="${it.order_id}">Approve COD</button>`:''}
              </div>
            </div>
          </div>
        `;
      });

      listEl.innerHTML = html;

      // bind actions
      listEl.querySelectorAll('[data-read]').forEach(btn=>{
        btn.addEventListener('click', async ()=>{
          const id = btn.getAttribute('data-read');
          await fetch('admin_mark_notification_read.php', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({id}).toString()
          });
          loadList();
          loadCount();
        });
      });

      listEl.querySelectorAll('[data-view]').forEach(btn=>{
        btn.addEventListener('click', ()=>{
          const oid = btn.getAttribute('data-view');
          window.location.href = 'admin_order_details.php?id=' + encodeURIComponent(oid);
        });
      });

      listEl.querySelectorAll('[data-cod]').forEach(btn=>{
        btn.addEventListener('click', async ()=>{
          const oid = btn.getAttribute('data-cod');
          if(!confirm('Confirm COD payment for Order #' + oid + ' ?')) return;
          const rr = await fetch('confirm_cod_payment.php', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({order_id: oid}).toString()
          });
          const tx = (await rr.text()).trim();
          if(tx==='success'){
            alert('COD Approved. Order Completed.');
            loadList(); loadCount();
          }else{
            alert('Failed: ' + tx);
          }
        });
      });

    }catch(e){
      listEl.innerHTML = '<div class="empty">Failed to load</div>';
    }
  }

  // live polling
  loadCount();
  setInterval(loadCount, 4000);
})();
</script>
