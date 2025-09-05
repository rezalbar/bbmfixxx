<?php
// Memanggil autoloader dari PhpSpreadsheet
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Membuat objek Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Menentukan tipe template dari parameter URL (misal: ?type=anggaran)
$type = $_GET['type'] ?? 'anggaran';
$fileName = "template_anggaran.xlsx";
$headers = ['Mata Anggaran', 'Rencana Anggaran BBM']; // Default untuk anggaran

// Menentukan header berdasarkan tipe
if ($type === 'kontrol') {
    $fileName = "template_kontrol.xlsx";
    $headers = ['No. Polisi', 'Kategori Kendaraan', 'Quota (L)', 'Aktif', 'Cadangan'];
} elseif ($type === 'spb') {
    $fileName = "template_spb.xlsx";
    $headers = ['No. SPB', 'Tanggal', 'No. Polisi', 'Departemen', 'Jenis BBM', 'Pengambilan (L)'];
} elseif ($type === 'pengambilan') {
    $fileName = "template_pengambilan.xlsx";
    $headers = ['Tanggal', 'No. Polisi', 'No. SPB', 'Nama Pemakai', 'Pengambilan (L)', 'Keterangan'];
}
// Anda bisa menambahkan tipe lain di sini dengan 'else if'

// Menulis header ke baris pertama
$sheet->fromArray($headers, NULL, 'A1');

// Mengatur header HTTP untuk mengunduh file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');

// Membuat writer dan mengirimkan file ke output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
