<?php
if (isset($_POST['edit'])) {
    $id_pembayaran = $_POST['id_pembayaran'];
    $jumlah_bayar_lama = $_POST['jumlah_bayar_lama'];
    $jumlah_bayar = $_POST['jumlah_bayar'];
    $kekurangan = $_POST['kekurangan'];

    $total = $jumlah_bayar_lama + $kekurangan;
    $sisa = $jumlah_bayar - $kekurangan;
    $total1 = $jumlah_bayar_lama + $jumlah_bayar;
    $sisa1 = $kekurangan - $jumlah_bayar;
    $kurang = $kekurangan - $jumlah_bayar;

    if ($jumlah_bayar > $kekurangan) {
        $query = mysqli_query($koneksi, "UPDATE pembayaran SET jumlah_bayar='$total' WHERE id_pembayaran='$id_pembayaran'");
        if ($query) {
            echo '<script>alert("SPP Terbayar || Saldo Anda Di Kembalikan Sebesar : Rp ' . number_format($sisa, 2, ',', '.') . '");location.href="?page=laporan";</script>';
        }
    } elseif ($jumlah_bayar < $kekurangan) {
        $query = mysqli_query($koneksi, "UPDATE pembayaran SET jumlah_bayar='$total1' WHERE id_pembayaran='$id_pembayaran'");
        if ($query) {
            echo '<script>alert("SPP Terbayar || Kekurangan Sebesar : Rp ' . number_format($kurang, 2, ',', '.') . '");location.href="?page=laporan";</script>';
        }
    } else {
        $query = mysqli_query($koneksi, "UPDATE pembayaran SET jumlah_bayar='$total1' WHERE id_pembayaran='$id_pembayaran'");
        if ($query) {
            echo '<script>alert("SPP Terbayar || Saldo Anda Di Kembalikan Sebesar : Rp ' . number_format($sisa1, 2, ',', '.') . '");location.href="?page=laporan";</script>';
        }
    }
}
if (empty($_SESSION['user']['level'])) {
?>
    <script>
        window.history.back();
    </script>
<?php
}
?>
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">History Siswa</h1>
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="laporan" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama petugas</th>
                        <th>Nama siswa</th>
                        <th>Tanggal bayar</th>
                        <th>Bulan bayar</th>
                        <th>Tahun bayar</th>
                        <th>SPP</th>
                        <th>Jumlah bayar</th>
                        <th>status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (isset($_GET['nisn'])) {
                        $nisn = $_GET['nisn'];
                        $i = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM pembayaran INNER JOIN petugas ON pembayaran.id_petugas=petugas.id_petugas INNER JOIN siswa ON pembayaran.nisn=siswa.nisn INNER JOIN spp ON pembayaran.id_spp=spp.id_spp WHERE pembayaran.nisn='$nisn'");
                    }
                    while ($data = mysqli_fetch_array($query)) {
                    ?>
                        <tr>
                            <td><?php echo $i++ ?></td>
                            <td><?php echo $data['nama_petugas'] ?></td>
                            <td><?php echo $data['nama_siswa'] ?></td>
                            <td><?php echo date('d-m-y', strtotime($data['tgl_bayar'])) ?></td>
                            <td><?php echo $data['bulan_bayar'] ?></td>
                            <td><?php echo $data['tahun_dibayar'] ?></td>
                            <td><?php echo $data['tahun'] ?> - Rp <?php echo number_format($data['nominal'], 2, ',', '.') ?></td>
                            <td> Rp. <?php echo number_format($data['jumlah_bayar'], 2, ',', '.') ?></td>
                            <td>
                                <?php
                                if ($data['nominal'] > $data['jumlah_bayar']) {
                                    echo 'Kurang';
                                } else {
                                    echo 'Lunas';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ($data['nominal'] == $data['jumlah_bayar']) {
                                ?>
                                    <button type="button" class="btn btn-success btn-sm">
                                        Lunas
                                    </button>
                                <?php
                                } else {
                                ?>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editpembayaran<?php echo $data['id_pembayaran'] ?>">
                                        Lunasi
                                    </button>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                        <div class="modal fade" id="editpembayaran<?php echo $data['id_pembayaran'] ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="col-sm-12">
                                            <div class="text-center">
                                                <h5 class="modal-title" id="exampleModalLabel">Edit Data Pembayaran</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <form action="" method="post">
                                        <div class="modal-body">
                                            <input type="hidden" name="id_pembayaran" value="<?php echo $data['id_pembayaran'] ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Nama petugas</label>
                                                <input type="text" name="nama_petugas" class="form-control" value="<?php echo $data['nama_petugas'] ?>" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Nama siswa</label>
                                                <input type="text" name="nama_siswa" class="form-control" value="<?php echo $data['nama_siswa'] ?>" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tanggal bayar</label>
                                                <input type="date" name="tgl_bayar" class="form-control" value="<?php echo $data['tgl_bayar'] ?>" disabled>
                                                <input type="hidden" name="tgl_bayar_baru" class="form-control" value="<?php echo date('Y-m-d') ?>" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Bulan bayar</label>
                                                <input type="text" name="bulan_bayar" class="form-control" value="<?php echo $data['bulan_bayar'] ?>" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tahun dibayar</label>
                                                <input type="text" name="tahun_bayar" class="form-control" value="<?php echo $data['tahun_dibayar'] ?>" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tahun dan nominal</label>
                                                <input type="text" name="id_spp" class="form-control" value="<?php echo $data['tahun'] ?> - <?php echo $data['nominal'] ?>" disabled>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Kekurangan</label>
                                                <input type="text" name="kekurangan" class="form-control" value="<?php echo $data['nominal'] - $data['jumlah_bayar'] ?>" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jumlah bayar</label>
                                                <input type="hidden" name="jumlah_bayar_lama" class="form-control" value="<?php echo $data['jumlah_bayar'] ?>">
                                                <input type="text" name="jumlah_bayar" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="col-sm-12">
                                                <div class="text-center">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary" name="edit">Simpan</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
            if (!empty($_SESSION['user']['level']) && !empty($_SESSION['user']['level'] == 'admin')) {
            ?>
                <div class="container" style="text-align: center;">
                    <a href="cetak_history.php?nisn=<?php echo $_GET['nisn'] ?>" class="btn btn-success btn-sm" target="_blank"><i class="fa fa-print"></i></a>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#laporan').DataTable();
    });
</script>