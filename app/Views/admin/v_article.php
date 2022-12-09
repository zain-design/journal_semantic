<?php
$halaman = "article";
?>

<div class="card-header">
    <i class="fas fa-table me-1"></i>
    <?php echo $templateJudul ?>
</div>
<div class="card-body">
    <div class="row mb-3">
        <div class="col-lg-3 pt-1 pb-1">
            <form action="" method="get">
                <input type="text" placeholder="katakunci.." name="katakunci" class="form-control" value="<?php echo $katakunci ?>">
            </form>
        </div>
        <div class="col-lg-9 pt-1 pb-1 text-end">
            <a href="<?php echo site_url('admin/' . $halaman . '/tambah') ?>" class="btn btn-primary">tambah</a>
        </div>
    </div>
    <?php
    $session = \Config\Services::session();
    if ($session->getFlashdata('warning')) {
    ?>
        <div class="alert alert-warning">
            <ul>
                <?php
                foreach ($session->getFlashdata('warning') as $val) {
                ?>
                    <li><?php echo $val ?></li>
                <?php
                }
                ?>
            </ul>
        </div>
    <?php
    }
    if ($session->getFlashdata('success')) {
    ?>
        <div class="alert alert-success"><?php echo $session->getFlashdata("success") ?></div>
    <?php
    }
    ?>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th class="col-1">no</th>
                <th class="col-6">judul</th>
                <th class="col-3">tanggal</th>
                <th class="col-2 text-center">aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($record as $value) {
                $post_id = $value['post_id'];
                $link_edit = site_url("admin/$halaman/edit/$post_id");
                $link_delete = site_url("admin/$halaman/?aksi=hapus&post_id=$post_id");
            ?>
                <tr>
                    <td><?php echo $nomor ?></td>
                    <td><?php echo $value['post_title'] ?></td>
                    <td><?php echo tanggal_indonesia($value['post_time']) ?></td>
                    <td>
                        <a href="<?php echo $link_edit ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="<?php echo $link_delete ?>" onclick="return confirm('apakah anda yakin ingin menghapus data ini?')" class="btn btn-sm btn-danger">Hapus</a>
                    </td>
                </tr>
            <?php
                $nomor++;
            } ?>
        </tbody>
    </table>

    <?php echo $pager->links('dt', 'datatable') ?>
</div>