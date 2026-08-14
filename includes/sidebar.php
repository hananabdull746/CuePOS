<?php
$current=basename($_SERVER['PHP_SELF']);
$sections=[
    'Workspace'=>[['dashboard.php','fa-gauge-high','Overview'],['operator.php','fa-table-cells-large','Live Tables'],['modules/shifts.php','fa-user-clock','Shifts']],
    'Club management'=>[['modules/players.php','fa-users','Players & CRM'],['modules/cafe.php','fa-mug-hot','Café & Menu'],['modules/inventory.php','fa-boxes-stacked','Inventory'],['modules/expenses.php','fa-receipt','Expenses']],
    'Insights & settings'=>[['modules/reports.php','fa-chart-line','Finance Reports'],['modules/sessions.php','fa-clock-rotate-left','Session History'],['modules/tournament.php','fa-trophy','Tournaments'],['modules/leaderboard.php','fa-ranking-star','Leaderboard'],['tv.php','fa-tv','TV Display'],['modules/settings.php','fa-gear','Settings']]
];
$renderNav=function()use($sections,$current){foreach($sections as $group=>$items){echo '<div class="sidebar-section-label">'.sanitize($group).'</div>';foreach($items as [$link,$icon,$label]){$active=$current===basename($link);echo '<a href="'.url($link).'" class="sidebar-link '.($active?'is-active':'').'"><i class="fa-solid '.$icon.'"></i><span>'.sanitize($label).'</span></a>';}}};
?>
<aside class="app-sidebar fixed top-[72px] bottom-0 left-0 hidden lg:flex flex-col z-40"><nav class="p-3 flex-1 overflow-y-auto"><?php $renderNav(); ?></nav><div class="sidebar-footer"><span class="text-green-300 font-bold">CuePOS Workspace</span><br>Purpose-built for modern snooker clubs.</div></aside>
<div id="mobileNavBackdrop" class="hidden fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-[60] lg:hidden"></div>
<aside id="mobileSidebar" class="app-sidebar fixed top-0 bottom-0 left-0 z-[70] w-[280px] p-3 flex flex-col transition-transform duration-200 -translate-x-full lg:hidden"><div class="h-14 px-2 flex items-center justify-between border-b border-slate-700/60"><span class="app-brand"><span class="app-brand-mark"><i class="fa-solid fa-circle-dot"></i></span>CuePOS</span><button type="button" onclick="document.getElementById('mobileSidebar').classList.add('-translate-x-full');document.getElementById('mobileNavBackdrop').classList.add('hidden');" class="nav-toggle"><i class="fa-solid fa-xmark"></i></button></div><nav class="pt-2 flex-1 overflow-y-auto"><?php $renderNav(); ?></nav><div class="sidebar-footer">Signed in as <b class="text-slate-300"><?=sanitize($_SESSION['user_name']??'')?></b></div></aside>
<main class="workspace-main"><div class="workspace-inner">
