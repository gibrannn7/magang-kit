<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-lock mr-2"></i> Form Ganti Password</h3>
            </div>
            <form action="<?= base_url('peserta/process_ganti_password') ?>" method="POST">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                
                <div class="card-body">
                    <div class="form-group">
                        <label>Password Lama</label>
                        <div class="input-group mb-3">
                            <input type="password" name="old_password" id="old_pass" class="form-control" required placeholder="Masukkan password saat ini">
                            <div class="input-group-append">
                                <div class="input-group-text cursor-pointer" onclick="togglePass('old_pass', 'icon_old')">
                                    <span class="fas fa-eye" id="icon_old"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group">
                        <label>Password Baru</label>
                        <div class="input-group mb-3">
                            <input type="password" name="new_password" id="new_pass" class="form-control" required placeholder="Minimal 6 karakter">
                            <div class="input-group-append">
                                <div class="input-group-text cursor-pointer" onclick="togglePass('new_pass', 'icon_new')">
                                    <span class="fas fa-eye" id="icon_new"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <div class="input-group mb-3">
                            <input type="password" name="conf_password" id="conf_pass" class="form-control" required placeholder="Ulangi password baru">
                            <div class="input-group-append">
                                <div class="input-group-text cursor-pointer" onclick="togglePass('conf_pass', 'icon_conf')">
                                    <span class="fas fa-eye" id="icon_conf"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-save mr-1"></i> Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
</style>

<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
