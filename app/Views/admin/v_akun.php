<div class="card-header">
    <i class="fas fa-table me-1"></i>

</div>
<div class="card-body">
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
    <form action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="input_fullname" class="form-label">nama lengkap</label>
            <input type="text" class="form-control" id="input_fullname" name="fullname" value="<?php echo (isset($fullname)) ? $fullname : "" ?>">
        </div>
        <div class="mb-3 col-lg-6">
            <h4>ganti password</h4>
        </div>
        <div class="mb-3">
            <label for="input_password_lama" class="form-label">password lama</label>
            <input type="password" class="form-control" id="input_password_lama" name="password_lama" value="<?php echo (isset($password_lama)) ? $password_lama : "" ?>">
        </div>
        <div class="mb-3">
            <label for="input_password_baru" class="form-label">password baru</label>
            <input type="password" class="form-control" id="input_password_baru" name="password_baru" value="<?php echo (isset($password_baru)) ? $password_baru : "" ?>">
        </div>
        <div class="mb-3">
            <label for="input_password_baru_konfirmasi" class="form-label">password baru konfirmasi</label>
            <input type="password" class="form-control" id="input_password_baru_konfirmasi" name="password_baru_konfirmasi" value="<?php echo (isset($password_baru_konfirmasi)) ? $password_baru_konfirmasi : "" ?>">
        </div>
        <div>
            <input type="submit" name="submit" value="simpan data" class="btn btn-primary">
        </div>
    </form>

</div>