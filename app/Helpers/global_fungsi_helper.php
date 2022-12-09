<?php

function kirim_email($attachment, $to, $title, $message)
{

    $email = \Config\Services::email();
    $email_pengirim = EMAIL_ALAMAT;
    $email_nama = EMAIL_NAMA;
    $config['protocol'] = 'smtp';
    $config['SMTPHost'] = 'smtp.gmail.com';
    $config['SMTPUser'] = $email_pengirim;
    $config['SMTPPass'] = EMAIL_PASSWORD;
    $config['SMTPPort'] = 465;
    $config['SMTPCrypto'] = 'ssl';
    $config['mailType'] = 'html';
    $config['charset'] = 'utf-8';
    $config['newline'] = '\r\n';
    $config['wordwrap'] = TRUE;

    $email->initialize($config);
    $email->setNewline("\r\n");
    $email->setFrom($email_pengirim, $email_nama);
    $email->setTo($to);

    if ($attachment) {
        $email->attach($attachment);
    }
    $email->setSubject($title);
    $email->setMessage($message);

    if (!$email->send()) {
        $data = $email->printDebugger(['headers']);
        print_r($data);
        return false;
    } else {
        return true;
    };
}
function nomor($currentPage, $jumlahbaris)
{
    if (is_null($currentPage)) {
        $nomor = 1;
    } else {
        $nomor = 1 + ($jumlahbaris * ($currentPage - 1));
    }
    return $nomor;
}

function tanggal_indonesia($parameter)
{
    $split1 = explode(" ", $parameter);
    $parameter1 = $split1[0];

    $bulan = [
        '1' => 'januari', 'februari', 'maret', 'april', 'maret', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'
    ];
    $hari = [
        '1' => 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'
    ];
    $num = date('N', strtotime($parameter1));
    $split2 = explode('-', $parameter1);
    return $hari[$num] . ", " . $split2[2] . " " . $bulan[(int)$split2[1]] . " " . $split2[0];
}

function purify($dirty_html)
{

    $config = HTMLPurifier_Config::createDefault();
    $config->set('URI.AllowedSchemes', array('data' => true));
    $purifier = new HTMLPurifier($config);
    $clean_html = $purifier->purify($dirty_html);
    return $clean_html;
}

function set_post_link($post_id)
{
    $model = new \App\Models\PostModel;
    $data = $model->getPost($post_id);
    $type = $data['post_type'];
    $seo = $data['post_title_seo'];
    return site_url($type . "/" . $seo);
}
