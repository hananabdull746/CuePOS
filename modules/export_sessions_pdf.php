<?php
require_once __DIR__.'/../includes/bootstrap.php';
requireLogin();
requireRole(['owner','manager']);
require_once __DIR__.'/../libs/fpdf/fpdf.php';

$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$db = getDB();
$q = $db->prepare("SELECT s.*,t.table_number,COALESCE(p.name,s.player_name_walkin,'Walk-in Guest') player_name FROM sessions s JOIN tables t ON t.id=s.table_id LEFT JOIN players p ON p.id=s.player_id WHERE s.club_id=? AND s.status='completed' AND DATE(s.end_time) BETWEEN ? AND ? ORDER BY s.end_time DESC");
$q->execute([$_SESSION['club_id'], $from, $to]);
$rows = $q->fetchAll();

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->SetTitle('CuePOS Session Report');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 9, ($_SESSION['club_name'] ?? 'CuePOS').' - Session Report', 0, 1);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'Period: '.$from.' to '.$to, 0, 1);
$pdf->Ln(4);
$widths = [29, 16, 42, 25, 23, 24, 24, 21, 27];
$headers = ['Date', 'Table', 'Player', 'Mode', 'Table Amt', 'Cafe Amt', 'Discount', 'Total', 'Payment'];
$pdf->SetFillColor(30, 41, 59);
$pdf->SetTextColor(255);
$pdf->SetFont('Arial', 'B', 8);
foreach ($headers as $i => $header) $pdf->Cell($widths[$i], 7, $header, 1, 0, 'L', true);
$pdf->Ln();
$pdf->SetTextColor(0);
$pdf->SetFont('Arial', '', 8);
$total = 0;
foreach ($rows as $row) {
    $values = [date('d M Y', strtotime($row['end_time'])), $row['table_number'], substr($row['player_name'], 0, 24), ucfirst(str_replace('_',' ',$row['billing_type'])), number_format($row['table_amount'],0), number_format($row['cafe_amount'],0), number_format($row['discount_amount'],0), number_format($row['total_amount'],0), ucfirst($row['payment_method'])];
    foreach ($values as $i => $value) $pdf->Cell($widths[$i], 6, $value, 1);
    $pdf->Ln();
    $total += (float)$row['total_amount'];
}
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(array_sum(array_slice($widths,0,7)), 8, 'Total Revenue', 1, 0, 'R');
$pdf->Cell($widths[7], 8, 'Rs. '.number_format($total,0), 1, 0, 'L');
$pdf->Cell($widths[8], 8, '', 1);
$pdf->Output('D', 'cuepos-sessions-'.$from.'-'.$to.'.pdf');
