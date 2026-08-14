<?php
$pageTitle = 'Receipt';
require_once __DIR__.'/includes/header.php';
require_once __DIR__.'/includes/receipt-template.php';
$id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT);
$db = getDB();
$q = $db->prepare("SELECT s.*,t.table_number,t.table_type,COALESCE(p.name,s.player_name_walkin,'Walk-in Guest') player_name,u.name operator_name FROM sessions s JOIN tables t ON t.id=s.table_id JOIN users u ON u.id=s.operator_id LEFT JOIN players p ON p.id=s.player_id WHERE s.id=? AND s.club_id=? AND s.status='completed'");
$q->execute([$id, $_SESSION['club_id']]);
$session = $q->fetch();
if (!$session) { http_response_code(404); exit('Receipt not found.'); }
$items = $db->prepare("SELECT coi.* FROM cafe_order_items coi JOIN cafe_orders co ON co.id=coi.order_id WHERE co.session_id=? ORDER BY coi.id");
$items->execute([$id]);
$items = $items->fetchAll();
$text = '*'.($_SESSION['club_name'] ?? 'CuePOS')."*\n".'Receipt #'.$session['id']."\n".'Table '.$session['table_number'].' | '.$session['player_name']."\n".'Total: '.money($session['total_amount'])."\n".'Payment: '.ucfirst($session['payment_method'])."\nThank you for visiting!";
?>
<div class="max-w-md mx-auto"><div class="flex justify-between items-center mb-4 no-print"><a href="<?=url('modules/sessions.php')?>" class="text-slate-400"><i class="fa-solid fa-arrow-left mr-1"></i>History</a><div class="flex gap-2"><a class="btn-secondary" target="_blank" href="https://wa.me/?text=<?=rawurlencode($text)?>"><i class="fa-brands fa-whatsapp mr-1"></i>WhatsApp</a><button onclick="window.print()" class="btn-primary"><i class="fa-solid fa-print mr-1"></i>Print</button></div></div><?php renderReceipt($session, $items); ?></div>
<?php require __DIR__.'/includes/footer.php'; ?>
