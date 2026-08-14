<?php
require_once __DIR__.'/_bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') apiError('Method not allowed');
verifyCsrf();
$tableId = filter_var(postField('table_id'), FILTER_VALIDATE_INT);
if (!$tableId) apiError('Invalid table.');
$db = getDB();
try {
    $db->beginTransaction();
    $q = $db->prepare("SELECT s.*,rp.rate_per_minute,rp.rate_per_frame,rp.slab_config FROM sessions s JOIN rate_plans rp ON rp.id=s.rate_plan_id WHERE s.table_id=? AND s.club_id=? AND s.status IN ('running','paused') FOR UPDATE");
    $q->execute([$tableId, $_SESSION['club_id']]);
    $s = $q->fetch();
    if (!$s) throw new RuntimeException('No active session was found.');
    $pause = (int)$s['total_pause_seconds'];
    if ($s['status'] === 'paused' && $s['pause_start']) $pause += max(0, time() - strtotime($s['pause_start']));
    $minutes = max(0, (time() - strtotime($s['start_time']) - $pause) / 60);
    $amount = match ($s['billing_type']) {
        'per_frame' => (int)$s['frames_played'] * (float)$s['rate_per_frame'],
        'slab' => calculateSlabBilling($minutes, $s['slab_config']),
        'challenge' => (float)$s['challenge_amount'],
        default => $minutes * (float)$s['rate_per_minute'],
    };
    $cafe = $db->prepare("SELECT COALESCE(SUM(total_amount),0) FROM cafe_orders WHERE session_id=? AND status IN ('open','completed')");
    $cafe->execute([$s['id']]);
    $amount += (float)$cafe->fetchColumn();
    $db->prepare("UPDATE sessions SET status='held',pause_start=NULL,total_pause_seconds=?,table_amount=?,cafe_amount=?,total_amount=? WHERE id=?")->execute([$pause, max(0, $amount - (float)$s['cafe_amount']), (float)$s['cafe_amount'], $amount, $s['id']]);
    $db->prepare("INSERT INTO held_bills(club_id,session_id,player_id,amount,held_at,status) VALUES(?,?,?,?,NOW(),'held')")->execute([$_SESSION['club_id'], $s['id'], $s['player_id'], $amount]);
    $db->prepare("UPDATE tables SET status='held' WHERE id=?")->execute([$tableId]);
    $db->commit();
    audit('session_held', 'sessions', (int)$s['id'], null, ['amount' => $amount]);
    jsonResponse(true, 'Bill held for '.money($amount).'.');
} catch (Throwable $e) {
    if ($db->inTransaction()) $db->rollBack();
    apiError($e->getMessage());
}
