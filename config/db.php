<?php
/** CuePOS shared configuration. Copy this file and update values for production. */
define('DB_HOST', getenv('CUEPOS_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('CUEPOS_DB_NAME') ?: 'cuepos');
define('DB_USER', getenv('CUEPOS_DB_USER') ?: 'root');
define('DB_PASS', getenv('CUEPOS_DB_PASS') ?: '');
define('DB_CHARSET', 'utf8mb4');
define('APP_URL', rtrim(getenv('CUEPOS_APP_URL') ?: '', '/'));
define('APP_NAME', 'CuePOS');
define('BASE_PATH', dirname(__DIR__));

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log('CuePOS database error: '.$e->getMessage());
            http_response_code(500);
            exit('Database connection failed. Check config/db.php.');
        }
    }
    return $pdo;
}
function startAppSession(): void { if (session_status() !== PHP_SESSION_ACTIVE) { session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')]); session_start(); } }
function sanitize(?string $value): string { return htmlspecialchars(trim(strip_tags((string)$value)), ENT_QUOTES, 'UTF-8'); }
function isLoggedIn(): bool { return !empty($_SESSION['user_id']) && !empty($_SESSION['club_id']); }
function requireLogin(): void { if (!isLoggedIn()) { header('Location: '.url('index.php')); exit; } }
function requireRole($roles): void { if (!in_array($_SESSION['user_role'] ?? '', (array)$roles, true)) { http_response_code(403); exit('You do not have permission to access this area.'); } }
function url(string $path = ''): string { return (APP_URL ?: '') . '/' . ltrim($path, '/'); }
function jsonResponse(bool $success, string $message = '', array $data = []): void { header('Content-Type: application/json; charset=utf-8'); echo json_encode(['success'=>$success, 'message'=>$message, 'data'=>$data]); exit; }
function csrfToken(): string { if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); return $_SESSION['csrf_token']; }
function verifyCsrf(): void { if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''))) jsonResponse(false, 'Your session expired. Refresh the page and try again.'); }
function money($amount): string { return 'Rs. '.number_format((float)$amount, 0); }
function currentClub(): array { return ['id'=>(int)($_SESSION['club_id'] ?? 0), 'name'=>$_SESSION['club_name'] ?? APP_NAME]; }
function audit(string $action, string $entityType, ?int $entityId = null, $old = null, $new = null): void { try { $stmt=getDB()->prepare('INSERT INTO audit_log (club_id,user_id,action,entity_type,entity_id,old_value,new_value,ip_address) VALUES (?,?,?,?,?,?,?,?)'); $stmt->execute([$_SESSION['club_id']??null,$_SESSION['user_id']??null,$action,$entityType,$entityId,$old?json_encode($old):null,$new?json_encode($new):null,$_SERVER['REMOTE_ADDR']??null]); } catch(Throwable $e) { error_log($e->getMessage()); } }
function tierDiscount(string $tier): float { return ['silver'=>5,'gold'=>10,'platinum'=>15][$tier] ?? 0; }
function calculateSlabBilling(float $elapsedMinutes, ?string $slabConfig): float { $slabs=json_decode($slabConfig ?: '[]', true) ?: []; usort($slabs, fn($a,$b)=>($a['up_to_minutes']??PHP_INT_MAX)<=>($b['up_to_minutes']??PHP_INT_MAX)); foreach($slabs as $slab){ if(isset($slab['up_to_minutes']) && $elapsedMinutes <= (float)$slab['up_to_minutes']) return (float)($slab['price']??0); } $base=end($slabs) ?: []; $baseMinutes=(float)($base['up_to_minutes']??0); $basePrice=(float)($base['price']??0); $extraMinutes=(float)($base['per_extra_minutes'] ?? 1); $extraPrice=(float)($base['extra_price'] ?? 0); return $basePrice + (ceil(max(0,$elapsedMinutes-$baseMinutes)/max(1,$extraMinutes))*$extraPrice); }
function activeShiftId(): ?int { $q=getDB()->prepare("SELECT id FROM shifts WHERE club_id=? AND status='open' ORDER BY id DESC LIMIT 1"); $q->execute([$_SESSION['club_id']]); return ($v=$q->fetchColumn()) ? (int)$v : null; }
