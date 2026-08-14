<?php
function renderTableCard(array $table): void {
    $status=$table['status'];
    $styles=[
        'available'=>['border'=>'border-green-400/35','badge'=>'bg-green-400/10 text-green-300 border-green-400/20','glow'=>'from-green-400/15'],
        'running'=>['border'=>'border-red-400/45','badge'=>'bg-red-400/10 text-red-300 border-red-400/20','glow'=>'from-red-400/15'],
        'paused'=>['border'=>'border-amber-400/45','badge'=>'bg-amber-400/10 text-amber-200 border-amber-400/20','glow'=>'from-amber-400/15'],
        'held'=>['border'=>'border-orange-400/45','badge'=>'bg-orange-400/10 text-orange-200 border-orange-400/20','glow'=>'from-orange-400/15'],
        'closed'=>['border'=>'border-slate-600','badge'=>'bg-slate-500/15 text-slate-400 border-slate-500/20','glow'=>'from-slate-400/10']
    ][$status] ?? ['border'=>'border-slate-700','badge'=>'bg-slate-500/15 text-slate-400 border-slate-500/20','glow'=>'from-slate-400/10'];
    $statusLabel=ucfirst($status);
?>
<article class="table-card relative min-h-[17rem] flex flex-col rounded-2xl border <?=$styles['border']?> bg-gradient-to-br <?=$styles['glow']?> to-[#101d2d] p-4" id="table-<?=$table['id']?>" data-table-id="<?=$table['id']?>" data-rate-plan="<?=$table['rate_plan_id']??''?>">
    <div class="absolute top-0 left-5 right-5 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
    <div class="flex justify-between items-start gap-3"><div class="flex items-center gap-3"><span class="w-12 h-12 grid place-items-center rounded-2xl bg-slate-950/40 border border-white/5 font-extrabold text-2xl tracking-tight"><?=sanitize($table['table_number'])?></span><div><p class="text-[.65rem] uppercase tracking-[.12em] font-extrabold text-slate-500"><?=sanitize($table['table_type'])?></p><p class="text-sm font-bold text-slate-200 mt-1"><?=sanitize($table['table_name'] ?: 'Table '.$table['table_number'])?></p></div></div><span class="status inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-full border text-[.62rem] uppercase tracking-[.08em] font-extrabold <?=$styles['badge']?>"><?php if($status==='running'): ?><i class="fa-solid fa-circle text-[.4rem] animate-pulse"></i><?php endif; ?><?=sanitize($statusLabel)?></span></div>
    <div class="table-detail flex-1 mt-5">
        <?php if(in_array($status,['running','paused'])): ?>
            <div class="rounded-xl bg-black/15 border border-white/5 p-3"><div class="flex justify-between gap-2"><p class="text-xs text-slate-400 truncate"><i class="fa-solid fa-user text-slate-500 mr-1"></i><span class="player font-semibold text-slate-200"><?=sanitize($table['player_name']?:'Walk-in Guest')?></span></p><span class="text-[.62rem] text-slate-500"><?=sanitize($table['plan_name']?:'Standard')?></span></div><div class="timer font-mono text-[1.65rem] leading-none mt-3 <?=$status==='running'?'text-green-300':'text-amber-200'?>" data-start="<?=strtotime($table['start_time']??'now')?>" data-paused="<?=$table['total_pause_seconds']??0?>">00:00:00</div><div class="flex items-end justify-between mt-2"><span class="text-[.64rem] uppercase tracking-widest font-bold text-slate-500">Running total</span><span class="amount text-lg font-extrabold text-white"><?=money(($table['display_amount']??0)+($table['cafe_amount']??0))?></span></div></div>
        <?php elseif($status==='held'): ?>
            <div class="rounded-xl bg-orange-400/5 border border-orange-400/15 p-4 text-center"><i class="fa-solid fa-hand text-orange-300 text-xl"></i><p class="text-sm text-slate-300 mt-2">Held bill awaiting recall</p><p class="amount text-xl font-extrabold text-white mt-2"><?=money($table['display_amount']??0)?></p></div>
        <?php elseif($status==='available'): ?>
            <div class="h-full flex flex-col justify-center items-center text-center pb-1"><span class="w-11 h-11 grid place-items-center rounded-full bg-green-400/10 text-green-300"><i class="fa-solid fa-play"></i></span><p class="text-sm font-bold text-slate-200 mt-3">Ready for play</p><p class="text-xs text-slate-500 mt-1"><?=sanitize($table['plan_name']?:'No rate plan assigned')?></p></div>
        <?php else: ?>
            <div class="h-full flex flex-col justify-center items-center text-center pb-1"><span class="w-11 h-11 grid place-items-center rounded-full bg-slate-500/10 text-slate-500"><i class="fa-solid fa-ban"></i></span><p class="text-sm font-bold text-slate-400 mt-3">Unavailable</p><p class="text-xs text-slate-600 mt-1">Closed for maintenance</p></div>
        <?php endif; ?>
    </div>
    <div class="actions flex gap-2 mt-4 pt-3 border-t border-white/5">
        <?php if($status==='running'): ?><button class="cafe-btn btn-secondary flex-1"><i class="fa-solid fa-mug-hot"></i><span>Café</span></button><button class="pause-btn btn-ghost px-3" title="Pause session"><i class="fa-solid fa-pause"></i></button><button class="hold-btn btn-ghost px-3" title="Hold bill"><i class="fa-solid fa-hand"></i></button><button class="end-btn btn-danger px-3" title="End and bill"><i class="fa-solid fa-stop"></i></button><?php elseif($status==='paused'): ?><button class="resume-btn btn-primary flex-1"><i class="fa-solid fa-play"></i> Resume</button><button class="end-btn btn-danger px-3" title="End and bill"><i class="fa-solid fa-stop"></i></button><?php elseif($status==='held'): ?><button class="recall-btn btn-primary w-full"><i class="fa-solid fa-reply"></i> Recall bill</button><?php elseif($status==='available'): ?><button class="start-session btn-primary w-full"><i class="fa-solid fa-play"></i> Start session</button><?php endif; ?>
    </div>
</article>
<?php }
