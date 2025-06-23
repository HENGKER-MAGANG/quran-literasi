<?php
require '../config/db.php';

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_bacaan_siswa.xls");

$query = "
  SELECT u.username, lr.surah_id, lr.ayat_number, lr.updated_at
  FROM users u
  LEFT JOIN last_read lr ON u.id = lr.user_id
  WHERE u.role = 'siswa'
  GROUP BY u.id
";
$stmt = $db->prepare($query);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<table border="1">
  <tr>
    <th>Nama Siswa</th>
    <th>Surah</th>
    <th>Ayat</th>
    <th>Update Terakhir</th>
  </tr>
  <?php foreach ($data as $row):
    $surah_name = '-';
    if ($row['surah_id']) {
      $api = @file_get_contents("https://api.quran.sutanlab.id/surah/" . $row['surah_id']);
      $surah_data = json_decode($api, true);
      $surah_name = $surah_data['data']['name']['transliteration']['id'] ?? '-';
    }
  ?>
    <tr>
      <td><?= $row['username'] ?></td>
      <td><?= $surah_name ?></td>
      <td><?= $row['ayat_number'] ?: '-' ?></td>
      <td><?= $row['updated_at'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>
