<?php 
    $u_id = $this->session->userdata('user_id');
    $u_role = $this->session->userdata('role');
    $u_data = $this->db->select('u.nama_lengkap, d.nama_divisi')
                       ->from('users u')
                       ->join('master_divisi d', 'd.id = u.divisi_id', 'left')
                       ->where('u.id', $u_id)
                       ->get()->row();
?>
<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fas fa-user-circle"></i>
        <span class="d-none d-md-inline ml-1">
            <?= $u_data ? $u_data->nama_lengkap : 'User' ?> | 
            <small class="text-muted">
                <?= ($u_data && $u_data->nama_divisi) ? $u_data->nama_divisi : strtoupper($u_role) ?>
            </small>
        </span>
    </a>
    </li>
