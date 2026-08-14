<?php
$pageTitle='Live Tables';
require_once __DIR__.'/includes/header.php';
require_once __DIR__.'/includes/sidebar.php';
require_once __DIR__.'/includes/table-card.php';
$db=getDB();
$q=$db->prepare("SELECT t.*,rp.plan_name,rp.rate_per_minute,s.id session_id,s.player_name_walkin,s.start_time,s.total_pause_seconds,s.billing_type,s.cafe_amount,p.name player_name FROM tables t LEFT JOIN rate_plans rp ON rp.id=t.rate_plan_id LEFT JOIN sessions s ON s.table_id=t.id AND s.status IN ('running','paused','held') LEFT JOIN players p ON p.id=s.player_id WHERE t.club_id=? AND t.is_active=1 ORDER BY t.table_number");
$q->execute([$_SESSION['club_id']]);$tables=$q->fetchAll();
?>
<div class="page-header"><div><p class="page-kicker">Club floor</p><h1 class="page-title">Live tables</h1><p class="page-description">Start, pause, hold and settle sessions from one real-time floor view.</p></div><div id="floor-stats" class="floor-summary"></div></div>
<section class="card p-3 sm:p-4"><div class="flex flex-wrap items-center justify-between gap-3 px-1 pb-4"><div class="flex items-center gap-3"><span class="w-10 h-10 grid place-items-center rounded-xl bg-green-400/10 text-green-300"><i class="fa-solid fa-table-cells-large"></i></span><div><h2 class="section-title">Table control centre</h2><p class="section-subtitle">Choose an available table to open a new session.</p></div></div><div class="soft-pill"><i class="fa-solid fa-rotate text-green-300"></i> Updates every 3 seconds</div></div><div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-5 gap-4" id="table-grid"><?php foreach($tables as $table)renderTableCard($table);?></div></section>
<div id="modal-root"></div><script src="<?=url('assets/js/toast.js')?>"></script><script src="<?=url('assets/js/timer.js')?>"></script><?php require __DIR__.'/includes/footer.php'; ?>
